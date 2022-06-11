<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'email_verified_at',
        'status',
        'birthdate',
        'password',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function interests()
    {
        return $this->morphToMany(Interest::class, 'interestable');
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class)->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->hasMany(Like::class)->where('type', 'dislike');
    }

    public function actions()
    {
        return $this->hasMany(Like::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
