<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    public function categories(Request $request)
    {
        // Support Select2: q param (search), page param for pagination
        $q = Category::where('is_active', 1);

        if ($request->filled('q')) {
            $q->where('name', 'like', '%' . $request->q . '%');
        }

        // If Select2 wants paginated results (page param), we can return in Select2 format
        if ($request->has('page')) {
            $perPage = 20;
            $p = $q->orderBy('name')->paginate($perPage);

            // map to Select2 format
            $results = $p->getCollection()->map(function ($c) {
                return ['id' => $c->id, 'text' => $c->name];
            })->toArray();

            return response()->json([
                'results' => $results,
                'pagination' => ['more' => $p->hasMorePages()]
            ]);
        }

        // Otherwise return simple array [{id,name}, ...]
        $cats = $q->select('id', 'name')->orderBy('name')->get();
        return response()->json($cats);
    }

    public function subcategories(Request $request, $id)
    {
        $category = Category::find($id);
        if (! $category) {
            return response()->json($request->has('page') ? ['results' => [], 'pagination' => ['more' => false]] : []);
        }

        $q = $category->subcategories()->where('is_active', 1);
        if ($request->filled('q')) {
            $q->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->has('page')) {
            $perPage = 20;
            $p = $q->select('id', 'name')->orderBy('name')->paginate($perPage);
            $results = $p->getCollection()->map(function ($s) {
                return ['id' => $s->id, 'text' => $s->name];
            })->toArray();
            return response()->json(['results' => $results, 'pagination' => ['more' => $p->hasMorePages()]]);
        }

        $subs = $q->select('id', 'name')->orderBy('name')->get();
        return response()->json($subs);
    }
}
