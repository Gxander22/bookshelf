<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    // use SoftDeletes

    public $timestamps = false;

    protected $fillable = ['title', 'author', 'genre', 'cover', 'publisher_id'];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
}
