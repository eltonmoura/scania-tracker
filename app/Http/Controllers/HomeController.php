<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function lastPosition($numberPlate)
    {
        try {
            return response()->json([$numberPlate]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }
}
