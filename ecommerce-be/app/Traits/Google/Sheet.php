<?php 

namespace App\Traits\Google;

trait Sheet {

    use ServiceAccount;

    public function getAccess( $client ){

        $service = new \Google\Service\Sheets($client);
        $sheets = new Sheets();
        $sheets->setService($service);

    }
}