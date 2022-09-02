<?php

namespace App\Services;

use App\Services\Contracts\TracServiceInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SighraService implements TracServiceInterface
{
    public function getLastPosition($numberPlate)
    {
        $ultimaPosicao = DB::connection('mysql2')
            ->table('view_ultima_posicao')
            ->whereRaw("REPLACE(cvei_placa, '-', '') = ?", [$numberPlate])
            ->select(
                'cvei_placa',
                'lupo_latitude',
                'lupo_longitude',
                'lupo_data_status',
                'cmar_nome'
            )
            ->orderBy('lupo_data_status', 'desc')
            ->first();

        if (empty($ultimaPosicao)) {
            return [];
        }

        return [
            'placa' => $numberPlate,
            'modelo' => $ultimaPosicao->cmar_nome,
            'latitude' => floatval($ultimaPosicao->lupo_latitude),
            'longitude' => floatval($ultimaPosicao->lupo_longitude),
            'data_hora' => Carbon::createFromTimeString($ultimaPosicao->lupo_data_status)
                ->format('d/m/Y H:i:s'),
            'origin' => 'Sighra',
        ];
    }
}
