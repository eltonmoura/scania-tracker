<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Models\AutotracPosition;

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
                'data_hora' => $position->PositionTime,
            ];
    }

    public function importPositions() {
        $accounts = AutotracClient::getAccounts();
        array_map(function ($account) {
            $vehicles = AutotracClient::getVehicles($account['Code']);
            array_map(function ($vehicle) {
                $positions = AutotracClient::getPositions(
                    $account['Code'],
                    $vehicle['Code'],
                    $this->getLastPositionTime()
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

                $row[$keyField] = $valueField;
            }
            MensaAutotracPositiongemCb::create($row);
        }
        return true;
    }

    private function getLastPositionTime()
    {
        $positionTime = \DB::table('autotrac_positions')->max('PositionTime');

        $positionTime = ($positionTime) ? $positionTime : null;
        print("Last PositionTime: $maxId \n");

        return $positionTime;
    }
}
