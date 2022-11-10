<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Services\Contracts\TracResponse;
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

    public function getLastPosition($numberPlate): ?TracResponse
    {
        // inserts a space at position 3
        $numberPlate = substr_replace($numberPlate, ' ', 3, 0);

        Log::info("TresSTecnologiaService:getLastPosition [$numberPlate]");

        $function = 'ListaUltimaPosicaoVeiculoPlaca';
        $arguments = [
            $function => [
                'Usuario' => $this->user,
                'Senha' => $this->password,
                'Placa' => $numberPlate,
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

        return new TracResponse(
            $position['Placa'],
            $position['Modelo'],
            $position['Latitude'],
            $position['Longitude'],
            $position['Data']
        );
    }
}
