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
        $report = $this->omnilinkClient->getRelatorioDeCoordenadas($numberPlate, 1);
        $lastPosition = (is_array($report)) ? (array)end($report) : null;

        if (!$lastPosition){
            return null;
        }

        return new TracResponse(
            $numberPlate,
            null,
            $lastPosition['Latitude'],
            $lastPosition['Longitude'],
            $lastPosition['Data']
        );
    }
}
