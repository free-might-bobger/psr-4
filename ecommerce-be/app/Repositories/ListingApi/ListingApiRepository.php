<?php 

namespace App\Repositories\ListingApi;

class ListingApiRepository implements ListingApiInterface
{
    /**
     * example query url: http://127.0.0.1:8000/api/listing_api?listingApi=units
     */
    public function get($lists){

        foreach( explode(",", $lists) as $list ){
           $listing = Listing::setListing($list);
        }
        return $listing->get();
    }
}