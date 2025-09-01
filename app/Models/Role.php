<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'label',
        'description',
        'guard_name',
        'is_active',
    ];

}
