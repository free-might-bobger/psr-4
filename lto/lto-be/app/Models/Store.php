<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Slug;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Google\Maps;

class Store extends Model implements Auditable
{
    use HasFactory, Slug, OptimusRequiredToModel, SoftDeletes, Maps;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'stores';
    protected $fillable = [
       'name', 'mobile', 'desc', 'is_active', 'user_id', 'latitude', 'longitude'
    ];

    protected $hidden = [
        'mobile',
    ];

    protected $appends = ['slug_name', 'optimus_id', 'logo',  'value', 'label', 'distance'];

    public function getLabelAttribute(){
        return $this->name;
    }

    public function getValueAttribute(){
        return $this->id;
    }

    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable', 'imageable_type', 'imageable_id')
                ->where('is_primary', 1);
    }

    public function getLogoAttribute(){
        if(count($this->images) > 0){
            $image = $this->images->filter(function($v){
                return $v->is_primary == true && $v->type == 'logo';
            })->first();
            if($image){
                return $image;
            }
        }
        return [
            "path_url" => null
        ];
            
    }

    public function storeUsers(){
        return $this->hasMany(StoreUser::class, 'store_id', 'id');
    }

    public function scopeWithinKm(Builder $query, string $latitude, string $longitude, string $radius ): void {

        $boundingBox = Maps::getBoundingBox($latitude, $longitude, $radius);

        $minLat = $boundingBox['minLat'];
        $maxLat = $boundingBox['maxLat'];
        $minLon = $boundingBox['minLon'];
        $maxLon = $boundingBox['maxLon'];

        $earthRaidusInKm = 6371;

        $query->select('*', DB::raw("
            ($earthRaidusInKm * acos(cos(radians($latitude)) 
            * cos(radians(latitude)) 
            * cos(radians(longitude) - radians($longitude)) 
            + sin(radians($latitude)) 
            * sin(radians(latitude)))) AS distance
        "))
        ->whereBetween('latitude', [$minLat, $maxLat])
        ->whereBetween('longitude', [$minLon, $maxLon])
        ->having('distance', '<=', $radius)
        ->orderBy('distance', 'asc');
    }

    public function getDistanceAttribute():String{
       $request = app()->make('request');
       if( $request->latitude && $request->longitude){
        return Maps::calculateDistance($this->latitude, $this->longitude, $request->latitude, $request->longitude);
       }
       return '';
    }

}
