<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('price');
    }
}
