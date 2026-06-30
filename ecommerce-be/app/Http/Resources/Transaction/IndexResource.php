<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
    public function __construct(Model|LengthAwarePaginator $resource) {
        $this->fields = ['id', 'optimus_id', 'name', 'label'];
        parent::__construct($resource);
    }
}
