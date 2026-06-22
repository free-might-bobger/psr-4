<?php

namespace App\Http\Resources\StoreUserMenu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
   public function __construct(Model|LengthAwarePaginator $resource) {
        $this->fields = ['optimus_id', 'store_user_id', 'store_menu_id', 'storeMenu.name', 'storeMenu.icon', 'storeMenu.optimus_id'];
        parent::__construct($resource);
    }
    
}
