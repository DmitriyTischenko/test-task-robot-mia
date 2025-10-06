<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    protected $fillable = [
        'website_id',
        'x',
        'y',
        'url',
        'viewport_width',
        'viewport_height',
        'clicked_at'
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
