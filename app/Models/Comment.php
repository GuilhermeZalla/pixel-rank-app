<?php

namespace App\Models;

use App\Support\LocaleFormatter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $appends = ['posted_date'];
    protected $fillable = ['body', 'review_id'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function review()
    {
        return $this->belongsTo(\App\Models\Review::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_likes')->withTimestamps();
    }

    public function getPostedDate()
    {
        return LocaleFormatter::relative($this->created_at);
    }

    public function getPostedDateAttribute()
    {
        return $this->getPostedDate();
    }

}
