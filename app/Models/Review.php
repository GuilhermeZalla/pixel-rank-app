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
            if (is_array($platform) && isset($platform['name'])) {
                $result .= '/'.$platform['name'];
            }
        }

        return substr(implode(', ', explode('/', $result)), 1);
    }

    /**
     * Build the game metadata shown on the review page.
     *
     * The external game API may be unavailable or return an incomplete
     * payload, so every key is treated as optional.
     *
     * @param  array<string, mixed>  $game
     * @return array<string, mixed>
     */
    public function getGameInfo(array $game = []): array{
        $platforms = is_array($game['platforms'] ?? null) ? $game['platforms'] : [];
        $releaseDate = $game['first_release_date'] ?? null;

        return [
            'platforms' => $this->getPlatformsFormatted($platforms),
            'platforms_count' => count($platforms),
            'release_date' => ! empty($releaseDate) ? LocaleFormatter::date(Carbon::createFromTimestamp($releaseDate)) : null,
            'summary' => $game['summary'] ?? null,
            'cover' => $game['cover'] ?? null
        ];
    }
}
