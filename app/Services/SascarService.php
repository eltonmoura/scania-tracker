<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Models\SascarVeiculo;
use App\Models\SascarPacotePosicao;

class SascarService implements TracServiceInterface
{
    protected $httpClient;
    protected $header;

    public function __construct()
    {
        $this->soapClient = new \SoapClient(env('SASCAR_WSDL'));
        $this->user = env('SASCAR_USER');
        $this->password = env('SASCAR_PASSWORD');
    }

    public function getLastPosition($numberPlate)
    {
        $veiculo = SascarVeiculo::where('placa', $numberPlate)->first();
        if (empty($veiculo)) {
            return [];
        }

        $posicao = SascarPacotePosicao::where('idVeiculo', $veiculo->idVeiculo)
            ->orderBy('dataPosicao', 'desc')
            ->first();

        if (empty($posicao)) {
            return [];
        }

        return [
            'placa' => $numberPlate,
            'modelo' => '',
            'latitude' => floatval($posicao->latitude),
            'longitude' => floatval($posicao->longitude),
            'data_hora' => $posicao->dataPosicao,
        ];
    }

    public function obterPacotePosicoesWS($quantidade = 1000)
    {
        $function = 'obterPacotePosicoes';
        $arguments = [
            $function => [
                'usuario' => $this->user,
                'senha' => $this->password,
                'quantidade' => $quantidade,
            ]
        ];
        $options = [];

        $result = $this->soapClient->__soapCall($function, $arguments, $options); 
        return property_exists($result, 'return') ? $result->return : [];
    }

    public function obterPacotePosicaoPorRangeWS(
        $idInicio,
        $idFinal,
        $quantidade = 1000
    ) {
        $function = 'obterPacotePosicaoPorRange';
        $arguments = [
            $function => [
                'usuario' => $this->user,
                'senha' => $this->password,
                'idInicio' => $idInicio,
                'idFinal' => $idFinal,
                'quantidade' => $quantidade,
            ]
        ];
        $options = [];

        $result = $this->soapClient->__soapCall($function, $arguments, $options); 
        return property_exists($result, 'return') ? $result->return : [];
    }

    public function getVeiculosFromWS($idVeiculo = null, $quantidade = 1000)
    {
        $function = 'obterVeiculos';
        $arguments = [
            $function => [
                'usuario' => $this->user,
                'senha' => $this->password,
                'idVeiculo' => $idVeiculo,
                'quantidade' => $quantidade,
            ]
        ];
        $options = [];

        $result = $this->soapClient->__soapCall($function, $arguments, $options); 
        return property_exists($result, 'return') ? $result->return : [];
    }

    public function saveVeiculosToDB($data)
    {
        foreach ($data as $item) {
            if (!property_exists($item, 'idVeiculo')) {
                break;
            }
            $sascarVeiculo = SascarVeiculo::firstOrNew(['idVeiculo' => $item->idVeiculo]);
            $sascarVeiculo->fill((array) $item);
            $sascarVeiculo->save();
        }

        return true;
    }

    public function getPacotePosicaoFromWS()
    {
        $maxId = \DB::table('sascar_pacote_posicao')->max('idPacote') ?: 0;

        $quantidade = 1000;
        $idInicio = $maxId + 1;
        $idFinal = $idInicio + $quantidade;

        return ($maxId === 0) ?
            $this->obterPacotePosicoesWS()
            : $this->obterPacotePosicaoPorRangeWS(
                $idInicio,
                $idFinal,
                $quantidade
            );
    }

    public function savePacotePosicaoToDB($data)
    {
        foreach ($data as $item) {
            if (!property_exists($item, 'idPacote')) {
                break;
            }
            $sascarPacotePosicao = SascarPacotePosicao::firstOrNew(['idPacote' => $item->idPacote]);
            $sascarPacotePosicao->fill((array) $item);
            $sascarPacotePosicao->save();
        }

        return true;
    }
}
