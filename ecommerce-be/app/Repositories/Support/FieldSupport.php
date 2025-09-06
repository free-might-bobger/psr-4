<?php 

namespace App\Repositories\Support;
use App\Traits\Obfuscate\OptimusRequiredToModel;

trait FieldSupport {
    use OptimusRequiredToModel;
    
    public function id(int $id): void {
        $this->model = $this->model->where('id', $this->optimus()->decode($id));
    }

    /**
     * Query String Example filters=ids:1;2
     * @author Bobby Gerez
     */
    public function ids($ids): void {
        $explode = explode(';', $ids);
        $this->model = $this->model->whereIn('id', $explode);
    }

    public function name(string $value): void {
        $this->model = $this->model->where('name', 'LIKE', '%'. $value . '%');
    }

    public function store_id(int $optimusId): void {
        $this->model = $this->model->where('store_id', $this->optimus()->decode($optimusId));
    }

    public function transaction_id(int $optimusId): void {
        $this->model = $this->model->where('transaction_id', $this->optimus()->decode($optimusId));
    }

    public function barcode(string $value): void {
        $this->model = $this->model->where('barcode', 'LIKE', '%'. $value . '%');
    }

    public function sku(string $value): void {
        $this->model = $this->model->where('sku', 'LIKE', '%'. $value . '%');
    }

    public function user_id(int $value): void {
        $this->model = $this->model->where('user_id', $value);
    }

}