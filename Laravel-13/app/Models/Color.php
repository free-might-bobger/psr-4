<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\LabelValue;

class Color extends Model
{
    use HasFactory, LabelValue;
    protected $table = 'colors';
    protected $fillable = [
        'name', 'desc'
    ];
    protected $appends = ['label', 'value'];
}
