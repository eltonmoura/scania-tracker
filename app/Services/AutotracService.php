<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use GuzzleHttp\Client as HttpClient;

class AutotracService implements TracServiceInterface
{
    protected $httpClient;
    protected $header;

    public function __construct()
    {
        $this->httpClient = new HttpClient(['base_uri' => env('AUTOTRAC_URL')]);
        $this->setHeader();
    }

    public function getLastPosition($numberPlat)
    {
        return [];
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
