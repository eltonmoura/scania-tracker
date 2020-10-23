<?php

namespace App\Services;

use GuzzleHttp\Client as HttpClient;

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
        ];

        $client = new HttpClient(['verify' => false]);
        print("REQUEST: $url/$path\n");
        $result = $client->request('GET', "$url/$path", $options);
        $body = json_decode($result->getBody(), true);
        print("RESULT: ");
        print_r($body);
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
