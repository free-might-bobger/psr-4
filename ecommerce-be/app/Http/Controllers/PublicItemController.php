<?php

namespace App\Http\Controllers;

use App\Http\Resources\PublicItemResource;
use App\Repositories\ItemRepository;
use App\Http\Requests\PublicItem\IndexRequest; 
use Illuminate\Support\Arr;
use App\Http\Resources\PublicItem\IndexResource;

class PublicItemController extends ApiController
{
    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = IndexRequest::class;
    }

    public function index(): IndexResource
    {
        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->filterQuery($this->params)->getCollection();
        return $this->getResource();
    }

    public function getResource(): IndexResource {
        return new IndexResource($this->result);
    }

    public function showResource(): IndexResource {
        return new IndexResource($this->result);
    }

}
