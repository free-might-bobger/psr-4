<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudToken extends Model
{
    use HasFactory;

    protected $table = 'cloud_tokens';
    protected $fillable = [
        'token', 'tokenable_id', 'tokenable_type'
    ];
}
