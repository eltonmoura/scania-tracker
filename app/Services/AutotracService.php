<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Services\Contracts\TracResponse;
use App\Models\AutotracPosition;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutotracService implements TracServiceInterface
{
    public function getLastPosition($numberPlate): ?TracResponse
    {
        $position = AutotracPosition::where('VehicleName', $numberPlate)
            ->orderBy('PositionTime', 'desc')
            ->first();

        if (!empty($position)) return new TracResponse(
            $numberPlate,
            null,
            floatval($ultimaPosicao->lupo_latitude),
            floatval($ultimaPosicao->lupo_longitude),
            Carbon::createFromTimeString($position->PositionTime)
                    ->timezone('America/Sao_Paulo')
                    ->format('d/m/Y H:i:s')
            
        );
    }

    public function importPositions() {
        try {
            $accounts = AutotracClient::getAccounts();
            array_map(function ($account) {
                $vehicles = AutotracClient::getVehicles($account['Code']);
                array_map(function ($vehicle) use ($account) {
                    Log::info("AutotracService:importPositions: atualizando " . $vehicle['Name']);
                    $positions = AutotracClient::getPositions(
                        $account['Code'],
                        $vehicle['Code'],
                        $this->getLastPositionTime($vehicle['Name'])
                    );
                    $this->savePositionsToDB($positions['Data']);
                }, $vehicles['Data']);
            }, $accounts);

            return true;
        } catch (Exception $e) {
            Log::error('AutotracService => ' . $e->getMessage());
        }
    }

    private function savePositionsToDB($data)
    {

        foreach ($data as $key => $value) {
            $row = [];
            foreach ($value as $keyField => $valueField) {
                // trata o booleano que vem como texto
                if (in_array($valueField, ['true', 'false'])) {
                    $row[$keyField] = ($valueField == 'true');
                    continue;
                }

                // Coloca a data no formato do banco
                if (in_array($keyField, ['PositionTime', 'ReceivedTime'])) {
                    $row[$keyField] = (new Carbon($valueField))->toDateTimeString();
                    continue;
                }

                $row[$keyField] = $valueField;
            }
            AutotracPosition::create($row);
        }
        return true;
    }

    private function getLastPositionTime($vehicleName)
    {
        $positionTime = \DB::table('autotrac_positions')
            ->where('VehicleName', $vehicleName)->max('PositionTime');

        // A consulta tem um intervalo máximo de 72 hs
        $minDate = (new Carbon())->subHours(72);
        if ($positionTime && $minDate->greaterThan(new Carbon($positionTime))) {
            $positionTime = $minDate->toDateTimeString();
        }

        // adiciona 1 minuto
        $positionTime = ($positionTime)
            ? (new Carbon($positionTime))->addMinutes(1)->toDateTimeString()
            : null;
        Log::info("AutotracService:getLastPositionTime $positionTime");
        return $positionTime;
    }
}
