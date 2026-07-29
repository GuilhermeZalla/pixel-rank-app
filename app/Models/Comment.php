<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $appends = ['posted_date'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function review()
    {
        return $this->belongsTo(\App\Models\Review::class);
    }

    public function getPostedDate()
    {
        return $this->created_at->locale('pt_BR')->diffForHumans();
    }

    public function getPostedDateAttribute()
    {
        return $this->getPostedDate();
    }

}