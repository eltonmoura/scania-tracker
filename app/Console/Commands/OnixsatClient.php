<?php

namespace App\Console\Commands;

use GuzzleHttp\Client as HttpClient;
use \SimpleXMLElement;

class OnixsatClient
{
    private $uri;
    private $login;
    private $password;

    public function __construct()
    {
        $this->uri = env('ONIXSAT_URI');
        $this->login = env('ONIXSAT_LOGIN');
        $this->password = env('ONIXSAT_PASSWORD');
    }

    public function request($func, $params = [])
    {
        $data = array_merge([
          'login' => $this->login,
          'senha' => $this->password,
        ], $params);

        $data = array_flip($data);
        $xml = new SimpleXMLElement("<$func/>");
        array_walk_recursive($data, array ($xml, 'addChild'));

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'body' => $xml->asXML(),
        ];

        $client = new HttpClient();
        $res = $client->request('POST', $this->uri, $options);

        return $res->getBody();
    }
}
