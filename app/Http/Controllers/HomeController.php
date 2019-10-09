<?php

namespace App\Http\Controllers;

use App\Models\MensagemCb;
use App\Models\Veiculo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function getPosition($numberPlate)
    {
        try {
            $veiculo = Veiculo::where('placa', $numberPlate)->first();
            $mensagens = MensagemCb::where('veiid', $veiculo->veiid)
                ->orderBy('dt', 'desc')
                ->get();

            return response()->json($mensagens);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    public function getLasPosition()
    {
        try {
            $response = DB::connection('mysql2')->table('view_ultima_posicao')
                ->select(
                    'cvei_placa',
                    'lupo_latitude',
                    'lupo_longitude',
                    'lupo_data_status',
                    'cmar_nome'
                )
                ->get();

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }

    public function getVehicle($numberPlate = null)
    {
        try {
            if ($numberPlate) {
                $veiculo = Veiculo::where('placa', $numberPlate)->first();
            } else {
                $veiculo = Veiculo::all();
            }

            return response()->json($veiculo);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }
}
