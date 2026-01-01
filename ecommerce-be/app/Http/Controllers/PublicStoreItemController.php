<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Repositories\ItemRepository;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Service\PublicStoreItemService;
class PublicStoreItemController extends ApiController
{
    private $publicStoreItemService; 

    public function __construct(ItemRepository $repository, PublicStoreItemService $publicStoreItemService)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->updateRequest = Request::class;
        $this->publicStoreItemService = $publicStoreItemService;
    }

    /**
     * Show the resource
     * @param int $id
     * @return BaseResource
     */
    public function show( int $id ) : BaseResource {
        $this->result = $this->publicStoreItemService->getPublicStoreItem( $id );
        return $this->getResource();
    }

}
