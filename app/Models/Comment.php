<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function review()
    {
        return $this->belongsTo(\App\Models\Review::class);
    }
}