<?php

namespace App\Http\Controllers;

use App\Repositories\AttendanceRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Attendance;
use Auth;
use App\Enum\Role;

class AttendanceController extends ApiController
{
    public function __construct(AttendanceRepository $repository) {
        $this->model =  Attendance::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function indexRequest()
    {
        $arrayIntersect = array_intersect (Auth::User()->roles->pluck('id')->toArray(), [Role::Registrar, Role::Admin]);
        if ( count($arrayIntersect) === 0){
            $this->params['user_id'] = Auth::User()->id;
         }
       
    }
    
    public function mergeRequest()
    {
        $this->params['user_id'] = \Auth::User()->id; 
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }

}
