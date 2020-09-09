<?php

namespace App\Services;

use GuzzleHttp\Client as HttpClient;
use App\Services\Contracts\AutotracServiceInterface;

class AutotracService implements AutotracServiceInterface
{
    protected $httpClient;
    protected $header;

    public function __construct()
    {
        $this->httpClient = new HttpClient(['base_uri' => env('AUTOTRAC_URL')]);
        $this->setHeader();
    }

    public function getAccounts()
    {
        $response = $this->httpClient->get('/accounts');
        return json_decode($response->getBody(), true);
    }

    public function getExpandedAlerts($vehicleCode)
    {
        //
    }

    public function getReturnMessages($vehicleCode)
    {
        //
    }

    public function getForwardMessages($vehicleCode)
    {
        //
    }

    private function setHeader()
    {
        $user = env('AUTOTRAC_USER');
        $password = env('AUTOTRAC_PASSWORD');

        $this->header = [
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => env('AUTOTRAC_SUBSCRIPTION_KEY'),
            'Authorization' => "Basic $user@tcmlog:$password",
        ];
    }
}
