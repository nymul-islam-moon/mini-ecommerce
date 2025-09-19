<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\MediaService;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $term = $request->query('q', '');

        $products = Product::query()
            ->withCount([
                // 'productSubCategories',
                // 'productChildCategories',
            ])
            ->search($term)
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $term]);
        return view('backend.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, MediaService $mediaService)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            // Handle main image upload if provided
            if ($request->hasFile('main_image')) {
                $imagePath = $mediaService->storeFile($request->file('main_image'), 'products/main_images');
                $formData['main_image'] = $imagePath;
            }
            // dd($formData);
            Product::create($formData);

            DB::commit();

            return redirect()->route('backend.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional: log the actual error
            Log::error('Product creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the product.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('backend.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, MediaService $mediaService)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            // Normalize sale_price empty string to null
            if (array_key_exists('sale_price', $formData) && $formData['sale_price'] === '') {
                $formData['sale_price'] = null;
            }

            // Handle main image replacement
            if ($request->hasFile('main_image')) {
                // Delete old file if exists
                if (!empty($product->main_image)) {
                    try {
                        $mediaService->deleteFile($product->main_image);
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete old main image during product update: ' . $e->getMessage(), [
                            'product_id' => $product->id,
                            'old_path'   => $product->main_image,
                        ]);
                    }
                }

                // Store new image
                $formData['main_image'] = $mediaService->storeFile(
                    $request->file('main_image'),
                    'products/main_images'
                );
            }

            // Update product
            $product->update($formData);

            DB::commit();

            return redirect()->route('backend.products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Product creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the product.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, MediaService $mediaService)
    {
        DB::beginTransaction();

        try {
            // Delete main image file if it exists
            if (!empty($product->main_image)) {
                $mediaService->deleteFile($product->main_image);
            }

            // Delete product record
            $product->delete();

            DB::commit();

            return redirect()->route('backend.products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Product deletion failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
            ]);

            return redirect()->back()
                ->with('error', 'Something went wrong while deleting the product.');
        }
    }
}
