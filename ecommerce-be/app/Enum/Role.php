<?php 
namespace App\Enum;

enum Role: int {
    const SUPER_ADMIN = 1;
    const SUPER_STAFF = 2;
    const STORE_ADMIN = 3;
    const STORE_STAFF = 4;
    const DELIVERY_ADMIN = 5;
    const DELIVERY_RIDER = 6;
    const CUSTOMER = 7;
}


?>
