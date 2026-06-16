<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Province;
use App\Repositories\Province\ProvinceRepository;

class ProvinceController extends ApiController
{
    public function __construct(ProvinceRepository $repository){
        $this->model =  Province::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
