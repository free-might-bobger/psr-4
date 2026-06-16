<?php

namespace App\Traits\Obfuscate;
use Jenssegers\Optimus\Optimus;

trait OptimusRequiredToModel
 {

    public function optimus() {
        return new Optimus( 1580030173, 59260789, 1163945558 );
    }

    public function getOptimusIdAttribute() {
        return $this->optimus()->encode( $this->id );
    }

    public function removeStringDecode( $val ) {
        $res = preg_replace( '/[^0-9]/', '', $val );
        return $this->optimus()->decode( ( int )$res );
    }

    /**
    * Model route binding will cause an error that id is not found.
    * e.g The route api/customer-transations/1843449638 could not be found.
    * use this trait to solve that problem.
    */
    public function resolveRouteBinding( $value, $field = NULL ) {
        return $this->where( 'id', $this->optimus()->encode( $value ) )->first() ?? abort( 404 );
    }

    public function getLabelAttribute() {
        return $this->name;
    }

    public function getValueAttribute():int {
        return $this->id;
    }

}