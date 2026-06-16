<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Municipality;
use App\Repositories\Municipality\MunicipalityRepository;

class MunicipalityController extends ApiController
{
    public function __construct(MunicipalityRepository $repository){
        $this->model =  Municipality::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
