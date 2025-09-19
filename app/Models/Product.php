<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'main_image',
        'price',
        'sale_price',
        'stock',
        'is_active',
        'sub_category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function subCategory()
    {
        return $this->belongsTo(\App\Models\SubCategory::class, 'sub_category_id');
    }

    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'id',            // SubCategory PK → FK on Product? Actually we can skip this
            'id',            // Category PK
            'sub_category_id', // Product.local FK → SubCategory.id
            'category_id'      // SubCategory.local FK → Category.id
        );
    }



    public function scopeWithRelations($query)
    {
        return $query->with(['subCategory.category']);
    }

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }


    public function scopeApplyFilter($query, $filters = null)
    {
        $f = $filters instanceof Request ? $filters : (object) ($filters ?? []);

        if (!empty($f->category_id)) {
            $query->whereHas('subCategory', function ($q) use ($f) {
                $q->where('category_id', $f->category_id);
            });
        }

        if (!empty($f->subcategory_id) || !empty($f->sub_category_id)) {
            $subId = $f->subcategory_id ?? $f->sub_category_id;
            $query->where('sub_category_id', $subId);
        }

        if (!empty($f->name)) {
            $query->search($f->name);
        } elseif (!empty($f->slug)) {
            $query->where('slug', 'like', '%' . $f->slug . '%');
        }

        if (isset($f->price_min) && $f->price_min !== '') {
            $min = (float) $f->price_min;
            $query->whereRaw(
                'CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN sale_price ELSE price END >= ?',
                [$min]
            );
        }

        if (isset($f->price_max) && $f->price_max !== '') {
            $max = (float) $f->price_max;
            $query->whereRaw(
                'CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN sale_price ELSE price END <= ?',
                [$max]
            );
        }

        return $query;
    }


    public function getFinalPriceAttribute()
    {
        $price = $this->price !== null ? (float) $this->price : null;
        $sale  = $this->sale_price !== null ? (float) $this->sale_price : null;

        if ($price === null) {
            return $sale;
        }

        if ($sale !== null && $sale < $price) {
            return $sale;
        }

        return $price;
    }


    public function getCategoryAttribute()
    {
        return $this->subCategory?->category;
    }
}
