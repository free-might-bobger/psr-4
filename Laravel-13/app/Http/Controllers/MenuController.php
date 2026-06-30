<?php

namespace App\Http\Controllers;


use App\Http\Requests\BaseIndexRequest;
use App\Models\Menu;
class MenuController extends ApiController
{

    public function __construct(MenuRepository $repository){
        $this->model =  Menu::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
    }


    public function indexRequest()
    {
        $user = \Auth::User();
        
        $menuIds = [];
       
        foreach($user->roles as $role){
            $menus = $role->menus->pluck('id')->values()->all();
            foreach($menus as $menu){
                $menuIds[] = $menu;
            }
        }
        
        $menuIds = collect($menuIds)->unique()->values()->all();

        if( count($menuIds) > 0 ){
            $this->params = array_merge($this->params, [
                'filters' => 'ids:' . implode(';', $menuIds)
            ] );
        }else{
            $this->params = array_merge($this->params, [
                'filters' => 'ids:0'
            ] );
        }

    }
    public function isPublicRoute(string $routeName): Bool {
        return true;
    }



}