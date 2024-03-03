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
        $report = $this->omnilinkClient->getRelatorioDeCoordenadas($numberPlate, 30);
        $lastPosition = (array)end($report);

        // Log::info("lastPosition: " . print_r($lastPosition, true));

        if (!$lastPosition) return null;

        return new TracResponse(
            $numberPlate,
            null,
            $lastPosition['Latitude'],
            $lastPosition['Longitude'],
            $lastPosition['Data']
        );
    }
}
