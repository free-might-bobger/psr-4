<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Repositories\ProfileRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\User;

class ProfileController extends ApiController
{
    public function __construct( ProfileRepository $repository ) {
        $this->model            = User::class;
        $this->repository       = $repository;
        $this->indexRequest     = BaseIndexRequest::class;
        $this->storeRequest     = ProfileRequest::class;
        $this->updateRequest    = ProfileRequest::class;
    }

}
