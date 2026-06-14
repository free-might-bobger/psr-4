<?php

namespace App\Repositories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;

class StoreRepository extends BaseRepository implements BaseInterface
{
    protected string $modelClassName;
    public function __construct()
    {
        $this->model = new Store;
        $this->cacheKey = 'stores-get';
        $this->modelClassName = get_class(new Store);
    }

    public function getWithinKm():void{

        $this->model = $this->model->withinKm($this->params['latitude'], $this->params['longitude'], $this->params['radius']);
    }

    public function setParameters(array $parameters):self{
        $this->params = $parameters;
        return $this;
     }

    public function applyFilters(): Collection{

        if(isset($this->params['latitude']) && isset($this->params['longitude']) && isset($this->params['radius'])){
            $this->getWithinKm();
        }
        if(isset($this->params['show_mobile'])){
            $this->showMobile();
        }
         if(isset($this->params['deleted'])){
            $this->includeDeleted();
        }
        $this->filterQuery($this->params);
        return $this->model->get();
    }

    /**
     * Make the mobile field visible in the query results
     * 
     * @return self
     */
    public function showMobile(): self
    {
        $this->model = $this->model->makeVisible(['mobile']);
        return $this;
    }
    
}
