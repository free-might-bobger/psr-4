<?php

namespace App\Http\Controllers;

use App\Repositories\StoreMenuAccessRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\StoreMenuAccessRequest;
use App\Models\Store;
use App\Http\Resources\BaseResource;

class StoreMenuAccessController extends ApiController
{
    public function __construct(StoreMenuAccessRepository $repository){
        $this->middleware('storeMenuAccessMiddleware')->only(['store']);
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreMenuAccessRequest::class;
        $this->updateRequest = StoreMenuAccessRequest::class;
    }

    protected function getResource() : BaseResource {
        return new BaseResource( $this->result );
    }
}
