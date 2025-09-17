<?php

namespace App\Repositories;

use App\Models\Invoice;

class InvoiceRepository extends BaseRepository implements BaseInterface
{
    
    public function __construct()
    {
        $this->setModel(new Invoice());
        $this->cacheKey = 'Invoices-get';
    }

    public function setModel($invoice) {
        $this->model = new Invoice;
        return $this->model;
    }

    public function store_ids($param){
        $this->model = $this->model->whereIn('store_id', $param);
    }

}
