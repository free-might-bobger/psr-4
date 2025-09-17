<?php

namespace App\Repositories\Category;

use App\Repositories\BaseRepository;
use App\Models\Category;
use App\Models\Item;
class CategoryRepository extends BaseRepository implements CategoryInterface
{

    
    
    public function __construct()
    {
        $this->model = new Category;
    }

    


}
