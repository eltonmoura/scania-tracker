<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class OmnilinkClient {

    public function __construct()
    {
        $this->soapClient = new \SoapClient(env('OMNILINK_WSDL'));
        $this->user = env('OMNILINK_USER');
        $this->password = env('OMNILINK_PASSWORD');
    }

    public function getAllVehicles()
    {
        $function = 'ListarVeiculoTodos';
        $arguments = [
            $function => [
                'Usuario' => $this->user,
                'Senha' => $this->password,
            ]
        ];

        $result = $this->soapClient->__soapCall($function, $arguments, []);

        if (! property_exists($result, 'return')) {
            Log::error("'return' not exists in: " . print_r($result, true));
            return [];
        }
        
        $data = (array)(simplexml_load_string($result->return));

        if (! array_key_exists('Veiculo', $data)) {
            Log::error("'Veiculo' not exists in: " . print_r($data, true));
            return [];
        }

        return $data['Veiculo'];
    }

    public function getEvents($lastSequence = 0)
    {
        $function = 'ObtemEventosNormais';
        $arguments = [
            $function => [
                'Usuario' => $this->user,
                'Senha' => $this->password,
                'UltimoSequencial' => $lastSequence,
            ]
        ];

        $result = $this->soapClient->__soapCall($function, $arguments, []);

        if (! property_exists($result, 'return')) {
            Log::error("'return' not exists in: " . print_r($result, true));
            return [];
        }

        $data = (array)(simplexml_load_string('<content>'.$result->return.'</content>'));

        if (! array_key_exists('TeleEvento', $data)) {
            Log::error("'TeleEvento' not exists in: " . print_r($data, true));
            return [];
        }

        // previnindo que quando tem apenas um objeto retorne como array
        return is_array($data['TeleEvento']) ? $data['TeleEvento'] : [$data['TeleEvento']];
    }
}
