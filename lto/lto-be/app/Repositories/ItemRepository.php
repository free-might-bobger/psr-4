<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\BaseRepository;
use App\Traits\UtilsTrait;
class ItemRepository extends BaseRepository
{
    use UtilsTrait;
    public function __construct()
    {
        $this->setModel(new Item);
        $this->cacheKey = 'items-get';
    }

    public function category_id($value)
    {   
        $this->model = $this->model->where('category_id', $value);
    }

   

    public function itemUpdateWithImage(int $id, array $params){

        /**get fillable should be before accessing the model */
        $this->setFillable();
        $this->where('id', $id);
        
        $data = array_intersect_key(
            $params,
            array_flip($this->fillable)
        );
        unset($data['store_id']);
        $this->filesUpload();
        return tap( $this->model->first() )->update( $data );

    }
    
}
