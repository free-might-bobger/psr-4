<?php

namespace App\Http\Controllers;

use App\Repositories\InterConnectedCityRepository;
use Illuminate\Http\Request;

class InterConnectedCityController extends ApiController
{
    public function __construct(InterConnectedCityRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = Request::class;
        $this->storeRequest = Request::class;
        $this->updateRequest = Request::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
