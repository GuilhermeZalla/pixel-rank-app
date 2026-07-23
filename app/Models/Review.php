<?php

namespace App\Models;

use App\ReviewRecommendation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $table = 'reviews';

    protected $casts = [
        'rating' => 'decimal:1',
        'recommendation' => ReviewRecommendation::class,
        'contains_spoilers' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function game(){
        return $this->belongsTo(\App\Models\Game::class);
    }

    public function comments(){
        return $this->hasMany(\App\Models\Comment::class);
    }

    public function getPublishedDate(){
        return $this->created_at->locale('pt_BR')->isoFormat('DD MMM YYYY');
    }

    public function getUpdatedDate(){
        return $this->updated_at->locale('pt_BR')->isoFormat('DD MM YYYY');
    }

    public function getPlatformsFormatted(){
        $platforms = '';
        foreach($this->game->platforms as $platform){
            $platforms .= '/'.$platform->name;
        }
        return substr(implode(', ', explode('/', $platforms)), 1);
    }
}
