<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Services\Contracts\TracResponse;
use App\Models\SascarVeiculo;
use App\Models\SascarPacotePosicao;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

    public function getLastPosition($numberPlate): ?TracResponse
    {
        $veiculo = SascarVeiculo::where('placa', $numberPlate)->first();
        if (empty($veiculo)) {
            return null;
        }

        $posicao = SascarPacotePosicao::where('idVeiculo', $veiculo->idVeiculo)
            ->orderBy('dataPosicao', 'desc')
            ->first();

        if (empty($posicao)) {
            return null;
        }

        return new TracResponse(
            $numberPlate,
            null,
            floatval($posicao->latitude),
            floatval($posicao->longitude),
            Carbon::createFromTimeString($posicao->dataPosicao)->format('d/m/Y H:i:s')
        );
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
        Log::info("SascarService:obterPacotePosicaoPorRangeWS [idInicio: {$idInicio}, idFinal: {$idFinal}, quantidade: {$quantidade}]");

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
        $placas = [];
        foreach ($data as $item) {
            if (!property_exists($item, 'idVeiculo')) {
                break;
            }
            $sascarVeiculo = SascarVeiculo::firstOrNew(['idVeiculo' => $item->idVeiculo]);
            $sascarVeiculo->fill((array) $item);
            $sascarVeiculo->save();
            $placas[] = $item->placa;
        }

        Log::info("SascarService:saveVeiculosToDB placas: " . implode(', ', $placas));
        return true;
    }

    public function getPacotePosicaoFromWS($idInicio = null)
    {
        $quantidade = 1000;

        // forçar um início
        if ($idInicio) {
            return $this->obterPacotePosicaoPorRangeWS(
                $idInicio,
                $idInicio + $quantidade,
                $quantidade
            );
        }

        $maxId = \DB::table('sascar_pacote_posicao')->max('idPacote') ?: 0;

        $idInicio = $maxId + 1;
        $idFinal = $idInicio + $quantidade;

        Log::info("SascarService:getPacotePosicaoFromWS maxId: {$maxId}");

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
        $lastDate = null;
        foreach ($data as $item) {
            if (!property_exists($item, 'idPacote')) {
                break;
            }

            $item->uf = substr($item->uf, 0, 2);

            $sascarPacotePosicao = SascarPacotePosicao::firstOrNew(['idPacote' => $item->idPacote]);
            $sascarPacotePosicao->fill((array) $item);
            $sascarPacotePosicao->save();

            $lastDate = $item->dataPosicao;
        }

        Log::info("SascarService:savePacotePosicaoToDB count: " . count($data) . " lastDate: $lastDate");
        return true;
    }
}
