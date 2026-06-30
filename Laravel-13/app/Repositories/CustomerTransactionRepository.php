<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Enum\Status;
use Illuminate\Support\Facades\Auth;

class CustomerTransactionRepository extends BaseRepository implements BaseInterface
{

    public function __construct()
    {
        $this->setModel(new Transaction());
        $this->cacheKey = 'Transactions-get';
    }

    public function createTransaction(
        int $storeId,
        float $deliveryCharge,
        int $selectedReceiveMethod,
        int $selectedPaymenthMethod,
        string $lat,
        string $lng,
        float $total,
        string $receivers_mobile
    ): Transaction {

        $user = Auth::user();
        return $this->model->create([
            'store_id'              => $storeId,
            'user_id'               => $user->id,
            'payment_method_id'     => $selectedPaymenthMethod,
            'receive_method_id'     => $selectedReceiveMethod,
            'delivery_charge'       => $deliveryCharge,
            'contact_number'        => $receivers_mobile,
            'reference_id'          => uniqid(),
            'lat'                   => $lat,
            'lng'                   => $lng,
            'total'                 => $total,
            'grand_total'           => $total + $deliveryCharge,
            'status_id'             => status::PREPARING_ORDERS,
            'receivers_mobile'     => $receivers_mobile
        ]);
    }
}
