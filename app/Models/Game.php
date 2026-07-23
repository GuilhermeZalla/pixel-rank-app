<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $table = 'games';

    public function reviews(){
        return $this->hasMany(\App\Models\Review::class);
    }

    public function genres(){
        return $this->belongsToMany(\App\Models\Genre::class, 'game_genre');
    }

    public function platforms(){
        return $this->belongsToMany(\App\Models\Platform::class, 'game_platform');
    }
}
