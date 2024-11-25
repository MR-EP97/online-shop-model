<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }


    public function getAllChildren(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->children()->with('children')->get();
    }

    //Limiting to Four Parent Layers for Creating Product
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function ($category) {
            if ($category->parent_id && $category->parent->getCategoryDepth() >= 3) {
                throw new \RuntimeException('Cannot add category, maximum depth reached.');
            }
        });
    }

    //Layer Calculation
    protected function getCategoryDepth(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    // checks if a category has no children.
    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    //checks if a category is a root category or not.
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    //returns the sibling categories (categories that share the same parent)
    public function siblings(): Collection
    {
        return self::where('parent_id', $this->parent_id)
            ->where('id', '!=', $this->id)
            ->get();
    }

    //returns the full path of the category from the root to the current category
    public function path(): \Illuminate\Support\Collection
    {
        $path = [];
        $current = $this;

        while ($current) {
            $path[] = $current;
            $current = $current->parent;
        }

        return collect(array_reverse($path));
    }

    //returns the root category (the category without a parent) for any given category
    public function root(): Collection
    {
        $current = $this;

        while ($current->parent) {
            $current = $current->parent;
        }

        return $current;
    }


    //recursively collects all descendants (children, grandchildren, etc.) of a category and returns them as a collection
    public function descendants(): Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }


}
