<?php 

namespace App\Repositories\Support;
use App\Traits\Obfuscate\OptimusRequiredToModel;

trait SearchFieldSupport {
    use OptimusRequiredToModel;
    
    public function id(int $id): void {
        $this->model = $this->model->where('id', $this->optimus()->decode($id));
    }

    /**
     * Query String Example filters=ids:1;2
     * @author Bobby Gerez
     * @param string $ids
     * @return void
     */
    public function ids(string $ids): void {
        $explode = explode(';', $ids);
        $this->model = $this->model->whereIn('id', $explode);
    }
    /**
     * Name the resource
     * @param string $value
     * @return void
     */
    public function name(string $value): void {
        $this->model = $this->model->where('name', 'LIKE', '%'. $value . '%');
    }

    /**
     * Store ID the resource
     * @param int $optimusId
     * @return void
     */
    public function store_id(int $optimusId): void {
        $this->model = $this->model->where('store_id', $this->optimus()->decode($optimusId));
    }

    /**
     * Transaction ID the resource
     * @param int $optimusId
     * @return void
     */
    public function transaction_id(int $optimusId): void {
        $this->model = $this->model->where('transaction_id', $this->optimus()->decode($optimusId));
    }

    /**
     * Barcode the resource
     * @param string $value
     * @return void
     */
    public function barcode(string $value): void {
        $this->model = $this->model->where('barcode', 'LIKE', '%'. $value . '%');
    }

    /**
     * SKU the resource
     * @param string $value
     * @return void
     */
    public function sku(string $value): void {
        $this->model = $this->model->where('sku', 'LIKE', '%'. $value . '%');
    }

    /**
     * User ID the resource
     * @param int $value
     * @return void
     */
    public function user_id(int $value): void {
        $this->model = $this->model->where('user_id', $value);
    }

}