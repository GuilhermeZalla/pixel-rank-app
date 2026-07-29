<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsCons extends Model
{
    protected $table = 'pros_cons';

    public function review(){
        return $this->belongsTo(\App\Models\Review::class);
    }
}
