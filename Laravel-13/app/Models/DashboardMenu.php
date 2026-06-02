<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardMenu extends Model
{
    use HasFactory;

    protected $table = 'dashboard_menus';
    protected $fillable = [
        'id', 'icon', 'name', 'path'
    ];
}
