<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use App\Models\MensagemCb;
use App\Models\Veiculo;

class OnixsatService implements TracServiceInterface
{
    public function getLastPosition($numberPlate)
    {
        $veiculo = Veiculo::where('placa', $numberPlate)->first();
        if (empty($veiculo)) {
            return [];
        }

        $mensagens = MensagemCb::where('veiid', $veiculo->veiid)
            ->orderBy('dt', 'desc')
            ->first();

        if (empty($mensagens)) {
            return [];
        }

        return [
            'placa' => $numberPlate,
            'modelo' => $veiculo->ident,
            'latitude' => floatval($mensagens->lat),
            'longitude' => floatval($mensagens->lon),
            'data_hora' => $mensagens->dt,
        ];
    }
}
