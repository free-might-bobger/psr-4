<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusId;
class Module extends Model
{
    use HasFactory, OptimusId;

    protected $table = 'modules';
    protected $fillable = [
        'name',
        'desc',
        'created_by'
    ];

    protected $appends = ['optimus_id'];

    public function schedules() {
        return $this->belongsToMany(Schedule::class, 'module_schedule', 'module_id', 'schedule_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id')->orderBy('id', 'desc');
    }

}
