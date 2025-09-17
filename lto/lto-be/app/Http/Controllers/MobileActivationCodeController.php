<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\MobileActivationCodeService;

class MobileActivationCodeController extends Controller
{
    public $service;
    public function __construct(MobileActivationCodeService $mobileActivationCodeService){

        $this->service = $mobileActivationCodeService;
    }

    public function createNewActivationcode(Request $request) {

        return response()->json([
            'data' => $this->service->createNewActivationcode($request->mobile)
        ]);

    }

    public function useOldPasscode(){

    }

    public function verifyPasscode(Request $request){

        return response()->json([
            'data' => $this->service->verifyPasscode($request->mobile, $request->passCode)
        ]);
    }
}
