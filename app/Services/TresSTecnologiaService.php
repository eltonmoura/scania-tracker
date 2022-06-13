<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Models\SascarVeiculo;
use App\Models\SascarPacotePosicao;
use Illuminate\Support\Facades\Log;

class TresSTecnologiaService implements TracServiceInterface
{
    protected $httpClient;
    protected $header;

    public function __construct()
    {
        $this->soapClient = new \SoapClient(env('TRESSTECNOLOGIA_WSDL'));
        $this->user = env('TRESSTECNOLOGIA_USER');
        $this->password = env('TRESSTECNOLOGIA_PASSWORD');
    }

    public function getLastPosition($numberPlate)
    {
        Log::info("TresSTecnologiaService:getLastPosition [$numberPlate]");

        $function = 'ListaUltimaPosicaoVeiculoPlaca';
        $arguments = [
            $function => [
                'Usuario' => $this->user,
                'Senha' => $this->password,
                'Placa' => urldecode($numberPlate),
            ]
        ];

        $result = $this->soapClient->__soapCall($function, $arguments, []);

        $content = property_exists($result, 'ListaUltimaPosicaoVeiculoPlacaResult')
            ? $result->ListaUltimaPosicaoVeiculoPlacaResult
            : '';

        if (preg_match('/^Erro/', $content)) {
            Log::info($content);
            return [];
        }

        $xml = simplexml_load_string($content);

        $position = (array)$xml->tbPosicao;
        return [
            'placa' => $position['Placa'],
            'modelo' => $position['Modelo'],
            'latitude' => $position['Latitude'],
            'longitude' => $position['Longitude'],
            'data_hora' => $position['Data'],
        ];
    }
}
