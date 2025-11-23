<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Repositories\ItemRepository;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Http\Request;

class PublicItemController extends ApiController
{
    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->updateRequest = Request::class;
    }

}
