<?php 
namespace App\Enum;

enum Status: int {
    const PREPARING_ORDERS = 1;
    const READY_FOR_PICKUP = 2;
    const OUT_FOR_DELIVERY = 3;
    const CUSTOMER_RECEIVED = 4;
    const COMPLETED = 5;
    const CANCELLED = 6;
    const RETURNED_REFUND = 7;
}


?>