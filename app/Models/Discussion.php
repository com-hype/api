<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function members()
    {
        return $this->hasMany(DiscussionMember::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
