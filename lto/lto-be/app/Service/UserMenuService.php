<?php

namespace App\Service;

use App\Repositories\Menu\MenuRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class UserMenuService {

    public $repository, $params;
    
    public function __construct(MenuRepository $menuRepository, Request $request){

        $this->repository = $menuRepository;
        $this->params = $request->all();
    }

    public function getUserMenus(): Collection  {

        $user = \Auth::User();
        $menuIds = [];

        foreach ( $user->roles as $role ) {
            $menus = $role->menus->pluck( 'id' )->values()->all();
            foreach ( $menus as $menu ) {
                $menuIds[] = $menu;
            }
        }

        $menuIds = collect( $menuIds )->unique()->values()->all();

        if ( count( $menuIds ) > 0 ) {
            $this->params = array_merge( $this->params, [
                'filters' => 'ids:' . implode( ';', $menuIds )
            ] );
        } else {
            $this->params = array_merge( $this->params, [
                'filters' => 'ids:0'
            ] );
        }
        unset($this->params['mobile']);
        unset($this->params['password']);
        $this->params['type'] = 'collection';
        return $this->repository->filterQuery($this->params)->getResults();
    }

}
