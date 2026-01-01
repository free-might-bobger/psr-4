<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\ItemPrice;
use App\Enum\Status;
use Illuminate\Support\Facades\Auth;

class TransactionRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
 {
        $this->setModel( new Transaction() );
        $this->cacheKey = 'Transactions-get';
    }

    public function createTransaction(
        Array $items,
        float $deliveryCharge,
        int $selectedReceiveMethod,
        int $selectedPaymenthMethod,
        string $lat,
        string $lng,
        float $total,
    ): Transaction {

        $user = Auth::user();
        return $this->model->create( [
            'user_id'               => $user->id,
            'payment_method_id'     => $selectedPaymenthMethod,
            'receive_method_id'     => $selectedPaymenthMethod,
            'delivery_charge'       => $deliveryCharge,
            'contact_number'        => $user->mobile,
            'reference_id'          => uniqid(),
            'lat'                   => $lat,
            'lng'                   => $lng,
            'total'                 => $total,
            'grand_total'           => $total + $deliveryCharge,
            'status_id'             => status::PREPARING_ORDERS
        ] );

    }

}
