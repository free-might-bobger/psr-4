<?php

namespace App\Repositories\Store;

use App\Models\Store;
use App\Repositories\BaseRepository;
use App\Models\Franchisee;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Support\Arr;

class StoreRepository extends BaseRepository implements StoreInterface
{
    use OptimusRequiredToModel;
    public function __construct()
    {
        $this->model = new Store;
        $this->cacheKey = 'stores-get';
        $this->modelClassName = get_class(new Store);
    }

    public function orWhere($value) {
        $this->model = $this->model->orWhere('user_id', $value);
    }

    public function franchisee_id($val){

        $franchisee = Franchisee::where('id', $this->optimus()->decode( $val ) )->with('city')->first();
        $this->model = $this->model->whereHas('address', function($q) use ($franchisee) {
            $q->where('city_mun_code', $franchisee->city->citymunCode);
        });
    }

    //relation=itemPrice:price;<;1439664,images:id;1439664
    public function relation()
    {
        $relationships = $this->pregSplit('@,@', Arr::get($this->params, 'relation', []));
        
        foreach ($relationships as $relationship) {
            [$relationTable, $fieldConditionValue] = $this->pregSplit('@:@', $relationship);
            $fieldConditionValueArray = $this->pregSplit('@;@', $fieldConditionValue);
            if (count($fieldConditionValueArray) === 3) {
                
                [$field, $comparison, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $comparison, $value);
            } else {
                
                [$field, $value] = $fieldConditionValueArray;
                $this->model = $this->model->whereRelation($relationTable, $field, $value);
            }
        }

        return $this;
    }

    

}
