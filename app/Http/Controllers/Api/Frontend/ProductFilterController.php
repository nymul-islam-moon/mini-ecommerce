<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    
    /**
     * Filter products endpoint used by frontend.
     * Accepts category_id, subcategory_id (or sub_category_id), name, slug, price_min, price_max, etc.
     */
    public function filter(Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);

        // Build query using Product model scopes (clean and testable)
        $q = Product::query()
            ->withRelations() // eager load subCategory.category
            ->applyFilter($request);

        // ordering
        $products = $q->where('is_active', 1)->orderBy('created_at', 'desc')->paginate($perPage);

        // Map output
        $data = $products->getCollection()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'category_name' => $p->subCategory?->category?->name,
                'subcategory_name' => $p->subCategory?->name,
                'price' => $p->price,
                'sale_price' => $p->sale_price,
                'final_price' => $p->final_price, // uses accessor
                'stock' => $p->stock,
                // If you have accessors for main image or thumbnail, adapt here:
                'thumbnail_url' => $p->main_image ?? null,
            ];
        })->toArray();

        // Return JSON: data + rendered pagination links (if you still want HTML links)
        return response()->json([
            'data' => $data,
            'links' => $products->withQueryString()->links('pagination::bootstrap-5')->render(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
