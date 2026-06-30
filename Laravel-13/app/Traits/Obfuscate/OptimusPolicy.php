<?php

namespace App\Traits\Obfuscate;
use Jenssegers\Optimus\Optimus;
use App\Models\User;
use App\Models\MenuRole;
use App\Models\Menu;
use Auth;
trait OptimusPolicy
 {

    public function accessable( $menuName, $accessRight ) {

        $user = User::where( 'id', Auth::User()->id )
        ->with( [ 'roles.menus' ] )->first();
        $menu = Menu::where( 'name', $menuName )->first();
        $roleIds = $user->roles->pluck( 'id' );
        if ( $roleIds->count() > 0 && $menu) {
            $menuRoles = MenuRole::whereIn( 'role_id', $roleIds )
            ->where( 'menu_id', $menu->id )
            ->get() ;
            foreach ( $menuRoles as $menuRole ) {
                $accessRights = $menuRole->AccessRights->pluck( 'name' )->values()->all();
                if ( in_array( $accessRight, $accessRights ) ) {
                    return true;
                }
            }
        }

        return false;

    }

}