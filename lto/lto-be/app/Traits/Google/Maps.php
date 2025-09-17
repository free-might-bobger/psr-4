<?php

namespace App\Traits\Google;
use App\Constants\Config;

trait Maps {
    
    public static function getBoundingBox( $latitude, $longitude, $distanceInKm ) : Array {
        $earthRadius = Config::EARTH_RADIUS;
        $lat = deg2rad( $latitude );
        $lon = deg2rad( $longitude );

        // Radius of the bounding box in radians
        $radDist = $distanceInKm / $earthRadius;

        $minLat = $lat - $radDist;
        $maxLat = $lat + $radDist;

        $minLon = $lon - $radDist / cos( $lat );
        $maxLon = $lon + $radDist / cos( $lat );

        // Convert back to degrees
        return [
            'minLat' => rad2deg( $minLat ),
            'maxLat' => rad2deg( $maxLat ),
            'minLon' => rad2deg( $minLon ),
            'maxLon' => rad2deg( $maxLon ),
        ];
    }

    public static function calculateDistance($latitudeOrigin,$longitudeOrigin,$latitudeTo,$longitudeTo ): String {
        $earthRadius = Config::EARTH_RADIUS;
        // Earth's radius in kilometers

        // Convert degrees to radians
       $latitudeOrigin = deg2rad($latitudeOrigin );
       $longitudeOrigin = deg2rad($longitudeOrigin );
       $latitudeTo = deg2rad($latitudeTo );
       $longitudeTo = deg2rad($longitudeTo );

        // Haversine formula
        $latDiff =$latitudeTo -$latitudeOrigin;
        $lonDiff =$longitudeTo -$longitudeOrigin;

        $a = sin( $latDiff / 2 ) * sin( $latDiff / 2 ) +
        cos($latitudeOrigin ) * cos($latitudeTo ) *
        sin( $lonDiff / 2 ) * sin( $lonDiff / 2 );

        $c = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );

        $distance = $earthRadius * $c;
        // Distance in kilometers
        
        return number_format($distance, 2);
    }

}