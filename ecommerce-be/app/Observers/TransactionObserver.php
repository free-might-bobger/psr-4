<?php

namespace App\Observers;
use App\Models\Transaction;
use App\Http\Requests\TransactionRequest;
use App\Models\Order;
use App\Models\Item;
use App\Models\ItemPrice;

class TransactionObserver {
    public function created( Transaction $transaction ): void {

        $request = app( TransactionRequest::class );
        foreach ( $request->items as $requestItem ) {

            foreach ( $requestItem[ 'items' ] as $item ) {
                $modelItem = Item::whereId( $item[ 'item_id' ] )->first();
                foreach ( collect( $item[ 'variations' ] )->groupBy( 'unit' ) as $variation ) {
                    $unitId = $variation[0][ 'unit' ];
                    $itemPrice =  ItemPrice::where( 'item_id', $item[ 'item_id' ] )->where( 'unit_id', $unitId )->first();
                    if ( $itemPrice ) {

                        Order::create( [
                            'transaction_id'    => $transaction->id,
                            'store_id'          => $requestItem[ 'store_id' ],
                            'item_id'           => $item[ 'item_id' ],
                            'item_name'         => $modelItem->name,
                            'item_description'  => $modelItem->description,
                            'unit_id'           => $unitId,
                            'base_price'        => $itemPrice->original_price,
                            'store_price'       => $itemPrice->selling_price,
                            'online_price'      => $itemPrice->online_price,
                            'qty'               => $variation->sum( 'count' )
                        ] );

                    }
                }

            }

        }
    }
}
