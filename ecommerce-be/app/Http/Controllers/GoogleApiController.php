<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class GoogleApiController extends Controller
{
    
    public function textSearch(Request $request): Array {
        $response = Http::get(config('app.mapsApiTextSearchUrl') . '?query=' . $request->input('query') . '&key=' . config('app.googleMapApiKey'));
        return $response->json($key = null, $default = null);
    }

    public function nearBySearch(): Array {

        $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json?keyword=cruise&location=10.359952633451057,123.98596204050837&radius=3500&key=AIzaSyBQqI-E0G9CJgg2Ew8sf3YRX3GAEgFBA3o');
        return $response->json($key = null, $default = null);
    }
}
