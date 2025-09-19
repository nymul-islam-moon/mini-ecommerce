<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $term = $request->query('q', '');

        $productSubCategories = SubCategory::query()
            ->withCount([
                // 'productSubCategories',
                // 'productChildCategories',
            ])
            ->search($term)
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $term]);
        return view('backend.sub_categories.index', compact('productSubCategories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.sub_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            SubCategory::create($formData);
            DB::commit();
            return redirect()->route('backend.sub-categories.index')
                   ->with('success', 'Sub-category created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sub-category: ' . $e->getMessage());
            return redirect()->back()
                   ->withInput()
                   ->with('error', 'An error occurred while creating the sub-category.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        return view('backend.sub_categories.show', compact('subCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCategory $subCategory)
    {
        return view('backend.sub_categories.edit', compact('subCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            $subCategory->update($formData);
            DB::commit();
            return redirect()->route('backend.sub-categories.index')
                   ->with('success', 'Sub-category updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sub-category: ' . $e->getMessage());
            return redirect()->back()
                     ->withInput()
                     ->with('error', 'An error occurred while updating the sub-category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory)
    {
        DB::beginTransaction();

        try {
            $subCategory->delete();
            DB::commit();
            return redirect()->route('backend.sub-categories.index')
                     ->with('success', 'Sub-category deleted successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sub-category: ' . $e->getMessage());
            return redirect()->back()
                     ->with('error', 'An error occurred while deleting the sub-category.');
        }
    }
}
