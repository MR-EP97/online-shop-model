<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $fillable = [
        'name',
        'imageable_id',
        'imageable_type',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
