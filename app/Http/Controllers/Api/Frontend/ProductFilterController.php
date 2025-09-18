<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    public function categories()
    {
        $cats = Category::select('id', 'name')->orderBy('name')->get();
        return response()->json($cats);
    }

    public function subcategories($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 200);
        }
        $subs = $category->subcategories()->select('id', 'name')->orderBy('name')->get();
        return response()->json($subs);
    }

    public function filter(Request $request)
    {
        // Build base query. Eager load small relations to avoid N+1
        $q = Product::query()->with(['category', 'subcategory']);

        if ($request->filled('category_id')) {
            $q->where('category_id', $request->category_id);
        }
        if ($request->filled('subcategory_id')) {
            $q->where('subcategory_id', $request->subcategory_id);
        }
        if ($request->filled('name')) {
            $q->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('slug')) {
            $q->where('slug', 'like', '%' . $request->slug . '%');
        }

        // Filter by effective/final price: use CASE to compute final_price in SQL
        // final_price = CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN sale_price ELSE price END
        // Apply min / max comparators if provided.
        if ($request->filled('price_min')) {
            $min = (float) $request->price_min;
            $q->whereRaw('CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN sale_price ELSE price END >= ?', [$min]);
        }
        if ($request->filled('price_max')) {
            $max = (float) $request->price_max;
            $q->whereRaw('CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN sale_price ELSE price END <= ?', [$max]);
        }

        // Pagination
        $perPage = 12;
        $products = $q->orderBy('created_at', 'desc')->paginate($perPage);

        // Map output
        $data = $products->getCollection()->map(function ($p) {
            // compute final_price in PHP as well (safety)
            $price = $p->price ?? null;
            $sale  = $p->sale_price ?? null;
            $final = ($sale !== null && $sale < $price) ? $sale : $price;

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'category_name' => $p->category?->name,
                'subcategory_name' => $p->subcategory?->name,
                'price' => $price,
                'sale_price' => $sale,
                'final_price' => $final,
                'stock_quantity' => $p->stock_quantity ?? null,
                'thumbnail_url' => $p->main_image_url ?? null, // adapt to your accessor
            ];
        })->toArray();

        // Return JSON: data + html links for frontend pagination handling
        return response()->json([
            'data' => $data,
            'links' => $products->withQueryString()->links('pagination::bootstrap-5')->render(),
        ]);
    }
}
