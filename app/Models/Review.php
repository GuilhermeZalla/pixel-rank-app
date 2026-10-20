<?php

namespace App\Models;

use Carbon\Carbon;
use App\Support\LocaleFormatter;
use App\ReviewRecommendation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'game_id',
        'game_name',
        'game_cover',
        'title',
        'recommendation',
        'contains_spoilers',
        'body',
        'rating',
        'platform_playable',
    ];

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
        return LocaleFormatter::relative($this->created_at);
    }

    public function getPublishedDate(){
        return LocaleFormatter::date($this->created_at);
    }

    public function getUpdatedDate(){
        return LocaleFormatter::date($this->updated_at);
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
           'platforms_count' => count($game['platforms']),
           'release_date' => ! empty($game['first_release_date']) ? LocaleFormatter::date(Carbon::createFromTimestamp($game['first_release_date'])) : null,
           'summary' => $game['summary'],
           'cover' => $game['cover']
       ];
    }
}
