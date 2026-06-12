<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Repositories\ProfileRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\User;
use App\Http\Resources\Profile\IndexResource;

class ProfileController
{
    public function __construct( ProfileRepository $repository ) {
        $this->repository       = $repository;
        $this->storeRequest     = ProfileRequest::class;
    }

    public function profileUpdate(){
        $request = app($this->storeRequest);
        $this->result = $this->repository->profileUpdate([
            'name' => $request->name,
            'mobile' => $request->mobile,
        ]);

        return new IndexResource($this->result);
    }

}
