<?php

namespace App\Http\Controllers;

use App\Services\SascarService;
use App\Services\OnixsatService;
use App\Services\SighraService;
use App\Services\AutotracService;

class HomeController extends Controller
{
    public function __construct(
        SascarService $sascarService,
        OnixsatService $onixsatService,
        SighraService $sighraService,
        AutotracService $autotracService
    ) {
        $this->sascarService = $sascarService;
        $this->onixsatService = $onixsatService;
        $this->sighraService = $sighraService;
        $this->autotracService = $autotracService;
    }

    public function getLastPosition($numberPlate)
    {
        try {
            return response()->json(array_merge(
                $this->sascarService->getLastPosition($numberPlate),
                $this->onixsatService->getLastPosition($numberPlate),
                $this->sighraService->getLastPosition($numberPlate),
                $this->autotracService->getLastPosition($numberPlate)
            ));

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }
}
