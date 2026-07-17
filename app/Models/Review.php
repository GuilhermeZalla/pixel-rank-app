<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $table = 'reviews';

    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function game(){
        return $this->belongsTo(\App\Models\Game::class);
    }
}
