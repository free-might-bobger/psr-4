<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Repositories\PublicStoreItemRepository;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Services\PublicStoreItemService;

class PublicStoreItemController extends ApiController
{
    private PublicStoreItemService $publicStoreItemService; 

    public function __construct(PublicStoreItemRepository $repository, PublicStoreItemService $publicStoreItemService)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->updateRequest = Request::class;
        $this->publicStoreItemService = $publicStoreItemService;
    }

    

}
