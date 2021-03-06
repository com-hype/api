<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'discussion_id',
        'is_read'
    ];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}
