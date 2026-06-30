<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApplyStoreService;

class ApplyStoreController extends BaseController
{
    public function create(Request $request, ApplyStoreService $service)
    {
        $store = $service->create($request);

        return $this->sendResponse($store, 'Store application submitted successfully.');
    }
}
