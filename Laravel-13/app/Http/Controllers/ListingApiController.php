<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ListingApi\ListingApiRepository;

class ListingApiController extends Controller
{
    
    public function index(ListingApiRepository $listingApi, Request $request){

        return response()->json([
            'data' => $listingApi->get($request->listingApi)
        ]);
    }
}
