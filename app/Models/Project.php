<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'title',
        'description',
        'avatar',
        'categories',
        'type',
    ];

    public function categories()
    {
        return $this->morphToMany(Interest::class, 'interestable');
    }

    public function action()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable')->where('action', 'like');
    }

    public function dislikes()
    {
        return $this->morphMany(Like::class, 'likeable')->where('action', 'dislike');
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function crowdfunding()
    {
        return $this->hasOne(Crowdfunding::class);
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }
}
