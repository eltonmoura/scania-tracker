<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Models\AutotracPosition;
use Carbon\Carbon;

class AutotracService implements TracServiceInterface
{
    public function getLastPosition($numberPlate)
    {
        $position = AutotracPosition::where('VehicleName', $numberPlate)
            ->orderBy('PositionTime', 'desc')
            ->first();

        return (empty($position))
            ? []
            : [
                'placa' => $numberPlate,
                'latitude' => floatval($position->Latitude),
                'longitude' => floatval($position->Longitude),
                'data_hora' => Carbon::createFromTimeString($position->PositionTime)
                    ->timezone('America/Sao_Paulo')
                    ->toDateTimeString(),
            ];
    }

    public function importPositions() {
        $accounts = AutotracClient::getAccounts();
        array_map(function ($account) {
            $vehicles = AutotracClient::getVehicles($account['Code']);
            array_map(function ($vehicle) use ($account) {
                $positions = AutotracClient::getPositions(
                    $account['Code'],
                    $vehicle['Code'],
                    $this->getLastPositionTime($vehicle['Name'])
                );
                $this->savePositionsToDB($positions['Data']);
            }, $vehicles['Data']);
            return true;
        }, $accounts);
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

        // adiciona 1 minuto
        $positionTime = ($positionTime)
            ? (new Carbon($positionTime))->addMinutes(1)->toDateTimeString()
            : null;
        print("Last PositionTime: $positionTime \n");

        return $positionTime;
    }
}
