<?php

namespace App\Http\Controllers;

use App\Services\SascarService;
use App\Services\OnixsatService;
use App\Services\SighraService;
use App\Services\AutotracService;
use App\Services\TresSTecnologiaService;
use App\Services\OmnilinkService;

class HomeController extends Controller
{
    public function __construct(
        SascarService $sascarService,
        OnixsatService $onixsatService,
        SighraService $sighraService,
        AutotracService $autotracService,
        TresSTecnologiaService $treSTecnologiaService,
        OmnilinkService $omnilinkService
    ) {
        $this->sascarService = $sascarService;
        $this->onixsatService = $onixsatService;
        $this->sighraService = $sighraService;
        $this->autotracService = $autotracService;
        $this->treSTecnologiaService = $treSTecnologiaService;
        $this->omnilinkService = $omnilinkService;
    }

    public function getLastPosition($numberPlate)
    {
        try {
            // remove invalid characters
            $numberPlate = preg_replace('/\W/', '', urldecode($numberPlate));

            // vai procurar em cada serviço nessa ordem
            $services = [
                'sascarService',
                'onixsatService',
                'autotracService',
                'treSTecnologiaService',
                'omnilinkService',
                // 'sighraService', // Erro ao acessar o BD
            ];

            foreach ($services as $service) {
                if ($response = $this->$service->getLastPosition($numberPlate)) {
                    // retorna o primeiro que encontrar
                    return response()->json($response->toArray());
                }
            }

            // não encontrou
            return response()->json([]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }
}
