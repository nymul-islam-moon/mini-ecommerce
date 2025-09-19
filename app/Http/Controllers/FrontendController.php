<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.index');
    }

    public function show($slug)
    {
        // Fetch product by slug with subcategory and category
        $product = Product::with(['subCategory.category'])->where('slug', $slug)->first();

        if (!$product) {
            abort(404, 'Product not found');
        }

        $category = $product->subCategory->category ?? null;

        // Related products: only active and not the current product
        $related = collect();
        if ($category) {
            $related = Product::whereHas('subCategory', fn($q) => $q->where('category_id', $category->id))
                ->where('is_active', 1)          // only active products
                ->where('id', '!=', $product->id)  // exclude current product
                ->limit(4)
                ->get();
        }

        return view('frontend.show', [
            'product'     => $product,
            'category'    => $category,
            'subcategory' => $product->subCategory,
            'finalPrice'  => $product->final_price,
            'related'     => $related,
        ]);
    }
}
