<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Services\Contracts\TracResponse;
use Illuminate\Support\Facades\Log;
use App\Models\OmnilinkVehicle;
use App\Models\OmnilinkPosition;
use Carbon\Carbon;

class OmnilinkService implements TracServiceInterface
{
    public function __construct()
    {
        $this->omnilinkClient = new OmnilinkClient();
    }

    public function getLastPosition($numberPlate): ?TracResponse
    {
        $vehicle = OmnilinkVehicle::where('placa', $numberPlate)->first();
        Log::info("vehicle: ". print_r($vehicle, true));

        if (!$vehicle) return null;

        $position = OmnilinkPosition::where('idTerminal', $vehicle->idTerminal)
            ->orderBy('dataHoraEvento', 'desc')
            ->first();

        if (!$position) return null;

        return new TracResponse(
            $numberPlate,
            null,
            $position->latitude,
            $position->longitude,
            $position->dataHoraEvento
        );
    }

    public function importVehicles() {
        $vehiclesFromService = $this->omnilinkClient->getAllVehicles();
        // Log::info("vehiclesFromService:". print_r($vehiclesFromService, true));
        foreach ($vehiclesFromService as $vehicleFS) {
            $vehicleDB = OmnilinkVehicle::updateOrCreate(
                ['placa' => $vehicleFS->Placa],
                ['idTerminal' => $vehicleFS->IdTerminal, 'terminal' => $vehicleFS->Terminal]
            );
        }
    }

    public function importPositions() {
        $lastSequence = OmnilinkPosition::max('numeroSequencia');
        $lastSequence = ($lastSequence) ? $lastSequence : 0;

        Log::info("lastSequence: " . $lastSequence);

        $events = $this->omnilinkClient->getEvents($lastSequence);

        Log::info("Count new events: " . count($events));
        Log::info("events:". print_r($events, true));

        $positionsData = [];
        foreach ($events as $event) {
            $positionsData[] = [
                'numeroSequencia' => $event->NumeroSequencia,
                'idTerminal' => $event->IdTerminal,
                'dataHoraEvento' => Carbon::createFromFormat('d/m/Y H:i:s', $event->DataHoraEvento)
                                    ->toDateTimeString(),
                'latitude' => $event->Latitude,
                'longitude' => $event->Longitude,
                'idSeqVeiculo' => $event->IdSeqVeiculo,
                'localizacao' => $event->Localizacao,
                'cidade' => $event->Cidade,
                'uf' => $event->UF,
            ];
            // Log::info("event:". print_r($event, true));
        }

        // Log::info("positionsData:". print_r($positionsData, true));
        if (count($positionsData) > 0) {
            OmnilinkPosition::insert($positionsData);
            Log::info("Insert ". count($positionsData) . " positions");
        }
    }
}
