<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Repositories\ItemRepository;
use App\Http\Requests\Item\StoreRequest;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Http\Request;

class ItemController extends ApiController
{

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = Request::class;
    }
    protected function getResource() : ItemResource {
        return new ItemResource( $this->result );
    }

    protected function itemUpdate(int $id) : ItemResource {
        $params = app( $this->storeRequest )->all();
        $this->result = $this->repository->itemUpdateWithImage( $id, $params );
        return $this->getResource();
    }
}
