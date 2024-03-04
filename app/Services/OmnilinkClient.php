<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OmnilinkClient {

    public function __construct()
    {
        $this->soapClient = new \SoapClient(env('OMNILINK_WSDL'));
        $this->user = env('OMNILINK_USER');
        $this->password = env('OMNILINK_PASSWORD');
    }

    public function getRelatorioDeCoordenadas($numberPlate, $qtdDays) {
        $timezone = 'America/Sao_Paulo';
        $endDate = Carbon::now()->timezone($timezone)->format('d/m/Y H:i');
        $startDate = Carbon::now()->subDays($qtdDays)->format('d/m/Y H:i');

        $function = 'GerarRelatorioDeCoordenadas';
        $arguments = [
            $function => [
                'Usuario' => $this->user,
                'Senha' => $this->password,
                'DataHoraInicial' => $startDate,
                'DataHoraFinal' => $endDate,
                'Placa' => $numberPlate,
            ]
        ];

        $result = $this->soapClient->__soapCall($function, $arguments, []);
        Log::error("result: " . print_r($result, true));

        $data = (array)(simplexml_load_string($result->return));

        if (array_key_exists('msgerro', $data)) {
            Log::error("Erro getRelatorioDeCoordenadas('$startDate', '$endDate',  '$numberPlate'): " . $data['msgerro']);
            return null;
        }

        if (!array_key_exists('Posicao', $data)) {
            Log::error("Sem retorno para getRelatorioDeCoordenadas('$startDate', '$endDate',  '$numberPlate'): " . print_r($data, true));
            return null;
        }

        return $data['Posicao'];
    }
}
