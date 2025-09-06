<?php

namespace App\Repositories\Municipality;

use App\Models\Municipality;
use App\Repositories\BaseRepository;

class MunicipalityRepository extends BaseRepository implements MunicipalityInterface
{
    
    public function __construct()
    {
        $this->model = new Municipality;
        $this->cacheKey = 'Municipalitys-get';
    }

    public function province_id($value){
        $this->model = $this->model->where('province_id', $value);
    }

}
