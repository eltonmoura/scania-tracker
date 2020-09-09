<?php

namespace App\Http\Controllers;

use App\Models\MensagemCb;
use App\Models\Veiculo;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\AutotracServiceInterface;

class HomeController extends Controller
{
    public function __construct(AutotracServiceInterface $autotracService)
    {
        $this->autotracService = $autotracService;
    }

    public function getLastPosition($numberPlate)
    {
        try {
            $lastPositionWS = $this->getLastPositionWS($numberPlate);

            $lastPositionBD = $this->getLastPositionBD($numberPlate);

            return response()->json(array_merge($lastPositionWS, $lastPositionBD));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    private function getLastPositionWS($numberPlate)
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

    private function getLastPositionBD($numberPlate)
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
            'data_hora' => $ultimaPosicao->lupo_data_status,
        ];
    }

    public function getAutotracAccounts()
    {
        $response = $this->autotracService->getAccounts();
        return response()->json($response);
    }
}
