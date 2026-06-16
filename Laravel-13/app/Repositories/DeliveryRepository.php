<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DeliveryRepository extends BaseRepository implements BaseInterface
{

    public function __construct()
    {
        $this->setModel(new Transaction());
        $this->cacheKey = 'Delivery-get';
    }

    public function getWithinKm():void{
        $this->model = $this->model->withinKm($this->params['latitude'], $this->params['longitude'], $this->params['radius']);
    }

    public function applyFilters(): LengthAwarePaginator|Collection{

        if(isset($this->params['latitude']) && isset($this->params['longitude']) && isset($this->params['radius'])){
            $this->getWithinKm();
        }
        $this->model = $this->model->with($this->params['with'] ?? []);
        return $this->getResults();
    }

}
