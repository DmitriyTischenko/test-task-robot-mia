<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Website extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'tracking_id'
    ];

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    public function getClickCountAttribute(): int
    {
        return $this->clicks()->count();
    }
}
