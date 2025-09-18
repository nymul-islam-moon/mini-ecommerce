<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;



class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $term = $request->query('q', '');

        $productCategories = Category::query()
            ->withCount([
                // 'productSubCategories',
                // 'productChildCategories',
            ])
            ->search($term)
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $term]);
        return view('backend.categories.index', compact('productCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            // dd($formData);
            Category::create($formData);

            DB::commit();

            return redirect()->route('backend.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional: log the actual error
            Log::error('Category creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the category.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('backend.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('backend.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            $category->update($formData);
            DB::commit();

            return redirect()->route('backend.categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Category update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();

        try {
            $category->delete();
            DB::commit();

            return redirect()->route('backend.categories.index')
                ->with('success', 'Category deleted successfully.');;
            
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Category deletion failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'something went wrong while deleting the category.');
        }
    }
}
