<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'stock',
        'is_active',
        'sub_category_id',
    ];


    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
    }

    public function getPriceAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getSalePriceAttribute($value)
    {
        return $value ? number_format($value, 2) : null;
    }

    public function isOnSale()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getFinalPriceAttribute()
    {
        return $this->isOnSale() ? $this->sale_price : $this->price;
    }

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
}
