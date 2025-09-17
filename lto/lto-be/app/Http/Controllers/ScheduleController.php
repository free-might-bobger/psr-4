<?php

namespace App\Http\Controllers;

use App\Repositories\Schedule\ScheduleRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Schedule;
use Illuminate\Support\Arr;
class ScheduleController extends ApiController
{
    public function __construct(ScheduleRepository $repository) {
        $this->model =  Schedule::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    
    public function mergeRequest()
    {
        $trainer = Arr::get($this->params, 'trainer_id', null);
        if($trainer){
            $this->params['trainer_id'] = Arr::get($trainer, 'id', null);
        }


    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
