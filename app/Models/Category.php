<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'description',
    ];


    /** Scope: search by name/slug/description */
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        // escape wildcards to avoid weird matches
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    // Category.php
    public function products()
    {
        return $this->hasManyThrough(
            \App\Models\Product::class,
            \App\Models\SubCategory::class,
            'category_id',    // Foreign key on sub_categories table...
            'sub_category_id', // Foreign key on products table...
            'id',             // Local key on categories
            'id'              // Local key on sub_categories
        );
    }
}
