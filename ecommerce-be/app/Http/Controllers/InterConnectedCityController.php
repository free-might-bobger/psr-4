<?php

namespace App\Http\Controllers;

use App\Repositories\InterConnectedCityRepository;
use Illuminate\Http\Request;
use App\Http\Requests\BaseIndexRequest;

class InterConnectedCityController extends ApiController
{
    public function __construct(InterConnectedCityRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = Request::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest = Request::class;
        $this->updateRequest = Request::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
