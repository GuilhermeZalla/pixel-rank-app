<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    /** @use HasFactory<\Database\Factories\PlatformFactory> */
    use HasFactory;

    protected $table = 'platforms';

    public function games(){
        return $this->belongsToMany(\App\Models\Game::class, 'game_platform');
    }
}
