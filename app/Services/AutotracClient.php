<?php

namespace App\Services;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;

class AutotracClient
{
    private static function request($path)
    {
        $user = env('AUTOTRAC_USER');
        $password = env('AUTOTRAC_PASSWORD');
        $url = env('AUTOTRAC_URL');
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => env('AUTOTRAC_SUBSCRIPTION_KEY'),
                'Authorization' => "Basic $user:$password",
            ],
            'timeout' => 5,
        ];

        $client = new HttpClient(['verify' => false]);
        $t1 = microtime(1);
        $result = $client->request('GET', "$url/$path", $options);
        $t2 = microtime(1);

        Log::info('AutotracClient ' . $path . ' - ' . ($t2 - $t1) . ' microsegundos');

        $body = json_decode($result->getBody(), true);
        if ($result->getStatusCode() !== 200) {
            throw new Exception("AutotracClient: \n$body", 1);
        }

        return $body; 
    }

    public static function getAccounts()
    {
        return self::request('accounts');
    }

    public static function getVehicles($account)
    {
        return self::request("accounts/$account/vehicles");
    }

    public static function getPositions($account, $vehicle, $lastPositionTime)
    {
        $queryString = '?_limit=1000';
        $queryString .= ($lastPositionTime) ? "&_dateTimeFrom=$lastPositionTime" : '';
        return self::request("accounts/$account/vehicles/$vehicle/positions$queryString");
    }
}
