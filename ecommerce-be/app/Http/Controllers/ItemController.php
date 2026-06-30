<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Repositories\ItemRepository;
use App\Http\Requests\Item\StoreRequest;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Http\Request;
use App\Http\Resources\Item\IndexResource;
use App\Http\Resources\Item\ShowResource;
class ItemController extends ApiController
{

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = Request::class;
    }

    protected function itemUpdate(int $id) : BaseResource {
        $params = app( $this->storeRequest )->all();
        $this->result = $this->repository->itemUpdateWithImage( $id, $params );
        return $this->getResource();
    }

    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new ShowResource($this->result);
    }
}
