<?php
namespace App\Repositories\ListingApi;

class Listing {

    public static $listing;

    public static function setListing( $list ) {

        switch ( $list ) {
            case 'units':
            self::$listing[ 'units' ] = \App\Models\Unit::all();
            break;
            case 'categories':
            self::$listing[ 'categories' ] = \App\Models\Category::all();
            break;
            case 'colors':
            self::$listing[ 'colors' ] = \App\Models\Color::all();
            break;
            case 'sizes':
            self::$listing[ 'sizes' ] = \App\Models\Size::all();
            break;
            case 'accessRights':
            self::$listing[ 'accessRights' ] = \App\Models\AccessRight::all();
            break;
            case 'storeMenus':
            self::$listing[ 'storeMenus' ] = \App\Models\StoreMenu::all();
            break;
        }

        return new Static();
    }

    public static function get() {
        return self::$listing;
    }
}
