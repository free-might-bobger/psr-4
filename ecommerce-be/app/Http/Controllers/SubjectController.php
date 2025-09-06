<?php

namespace App\Http\Controllers;

use App\Repositories\Subject\SubjectRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Subject;

class SubjectController extends ApiController
{
    public function __construct(SubjectRepository $repository) {
        $this->model =  Subject::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
