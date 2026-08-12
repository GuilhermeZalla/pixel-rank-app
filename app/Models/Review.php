<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function comments(){
        return $this->hasMany(\App\Models\Comment::class);
    }

    public function pros_cons(){
        return $this->hasMany(\App\Models\ProsCons::class);
    }

    public function getPostedDate()
    {
        return $this->created_at->locale('pt_BR')->diffForHumans();
    }

    public function getPublishedDate(){
        return $this->created_at->locale('pt_BR')->isoFormat('DD/MM/YYYY');
    }

    public function getUpdatedDate(){
        return $this->updated_at->locale('pt_BR')->isoFormat('DD/MM/YYYY');
    }

    public function getPlatformsFormatted(array $platforms){

        $result = '';
        foreach($platforms as $platform){
            $result .= '/'.$platform['name'];
        }

        return substr(implode(', ', explode('/', $result)), 1);
    }

    public function getGameInfo(array $game){
       return [
           'platforms' => $this->getPlatformsFormatted($game['platforms']),
           'release_date' => Carbon::createFromTimestamp($game['first_release_date'])->locale('pt_BR')->isoFormat('DD/MM/YYYY'),
           'summary' => $game['summary'],
           'cover' => !empty($game['artworks']) ? $game['artworks'][rand(0, count($game['artworks']) - 1)]['image_id'] : $game['cover']
       ];
    }
}
