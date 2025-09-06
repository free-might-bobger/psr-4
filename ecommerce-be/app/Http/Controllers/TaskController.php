<?php

namespace App\Http\Controllers;

use App\Repositories\Task\TaskRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Task;
use Illuminate\Support\Arr;
use Auth;
use App\Enum\Role;
class TaskController extends ApiController
{
    public function __construct(TaskRepository $repository) {
        $this->model =  Task::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function indexRequest()
    {
        // $arrayIntersect = array_intersect (Auth::User()->roles->pluck('id')->toArray(), [Role::Registrar, Role::Admin]);
        // if ( count($arrayIntersect) === 0){
        //     $this->params['created_by'] = Auth::User()->id;
        //  }
       
    }

    
    public function mergeRequest()
    {
        
        $this->params['created_by'] = \Auth::User()->id; 
        
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
