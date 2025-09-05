<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    protected $fillable = [
        'type', 'name', 'code', 'parent_id', 'status', 'description', 'created_by', 'updated_by'
    ];

    function parent()
    {
        return $this->belongsTo(MasterData::class, 'parent_id');
    }
    
}
