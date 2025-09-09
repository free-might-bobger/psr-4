<?php

namespace App\Http\Controllers;

use App\Http\Resources\PublicStoreResource;
use App\Repositories\StoreRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\Store\StoreRequest;
use Illuminate\Support\Arr;
use App\Http\Resources\BaseResource;

class PublicStoreController extends ApiController
{
    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
    }

    public function index(): BaseResource
    {

        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->setParameters($this->params)->getWithinKm();
        return $this->getResource();
    }

    public function getResource()
    {
        return new BaseResource($this->result);
    }

    public function isPublicRoute(string $routeName): Bool
    {
        $publicRoute = [
            'index', 'view'
        ];
        if (in_array($routeName, $publicRoute)) {
            return true;
        }

        return false;
    }

    public function pregSplit(string $pattern, $value)
    {
        return preg_split($pattern, $value, 0, PREG_SPLIT_NO_EMPTY);
    }

}
