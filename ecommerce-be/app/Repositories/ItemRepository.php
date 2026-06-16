<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\BaseRepository;
use App\Traits\UtilsTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use App\Repositories\Support\SearchFieldSupport;
class ItemRepository extends BaseRepository
{
    use UtilsTrait, SearchFieldSupport;
    public function __construct()
    {
        $this->setModel(new Item);
        $this->cacheKey = 'items-get';
        $this->collection = new Collection();
    }

    public function category_id($value) : void
    {   
        $this->model = $this->model->where('category_id', $value);
    }

    public function itemUpdateWithImage(int $id, array $params): bool
    {
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

     /**
     * Filter the resource
     * @param array $parameters
     * @return self
     */
    public function filterQuery(array $parameters): self
    {
        $this->setParameters($parameters);
        $filters = $this->pregSplit('@,@', Arr::get($parameters, 'filters', ''));
        foreach ($filters as $filterKeys => $filterValues) {
            [$column, $value] = $this->pregSplit('@:@', $filterValues);
            if (method_exists($this, $column)) {
                call_user_func([$this, $column], $value);
            }
        }
        $this->with();
        
        $this->collection = $this->model->get()
        ->filter(function ($item) {
            return $item->store && $item->store->distance <= 30;
        })
        ->sortBy(function ($item) {
            return $item->store->distance ?? PHP_INT_MAX;
        })
        ->values();
        return $this;
    }

        /**
        * Get the resource collection
        * @return Collection
        */  
    public function getCollection(): Collection
    {        
        return $this->collection;
    }


}
