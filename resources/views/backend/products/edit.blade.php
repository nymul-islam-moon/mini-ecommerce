@extends('layouts.backend.app')

@section('title', 'Edit Product')

@push('backend_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Product</h3>
                </div>
                <div class="col-sm-6">
                    <x-backend.breadcrumb :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Products', 'route' => 'backend.products.index'],
                        ['label' => 'Edit', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Update Product</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('backend.products.update', $product->id) }}" method="POST"
                                id="product-edit-form" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                {{-- Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Slug --}}
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug <small
                                            class="text-muted">(auto)</small></label>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug', $product->slug) }}" placeholder="product-slug" readonly>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Main Image --}}
                                <div class="mb-3">
                                    <label for="main_image" class="form-label">Main Image <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="main_image" id="main_image"
                                        class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                                    @error('main_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($product->main_image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $product->main_image) }}" alt="Current Image"
                                                width="150">
                                        </div>
                                    @endif
                                    <div class="form-text">Upload a new image to replace the existing one (optional).</div>
                                </div>

                                {{-- Description --}}
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional product description" rows="4">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    {{-- Price --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label">Price <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="price" id="price" step="0.01" min="0"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ old('price', $product->price) }}" placeholder="0.00" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Sale Price --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="sale_price" class="form-label">Sale Price <small
                                                class="text-muted">(optional)</small></label>
                                        <input type="number" name="sale_price" id="sale_price" step="0.01"
                                            min="0" class="form-control @error('sale_price') is-invalid @enderror"
                                            value="{{ old('sale_price', $product->sale_price) }}" placeholder="0.00">
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">If set, sale price must be smaller than Price.</div>
                                    </div>

                                    {{-- Stock --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="stock" class="form-label">Stock Quantity <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="stock" id="stock" min="0"
                                            class="form-control @error('stock') is-invalid @enderror"
                                            value="{{ old('stock', $product->stock) }}" placeholder="0" required>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Category & SubCategory (Select2 AJAX) --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label">Category <span
                                                class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id"
                                            class="form-select @error('category_id') is-invalid @enderror" required>
                                            @if (old('category_id') || $product->category_id)
                                                <option value="{{ old('category_id', $product->category_id) }}" selected>
                                                    {{ old('category_name', $product->category?->name ?? '') }}
                                                </option>
                                            @endif
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="sub_category_id" class="form-label">Sub Category <span
                                                class="text-danger">*</span></label>
                                        <select name="sub_category_id" id="sub_category_id"
                                            class="form-select @error('sub_category_id') is-invalid @enderror" required>
                                            @if (old('sub_category_id') || $product->sub_category_id)
                                                <option value="{{ old('sub_category_id', $product->sub_category_id) }}"
                                                    selected>
                                                    {{ old('sub_category_name', $product->subCategory?->name ?? '') }}
                                                </option>
                                            @endif
                                        </select>
                                        @error('sub_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="is_active"
                                        class="form-select @error('is_active') is-invalid @enderror" required>
                                        <option value="1"
                                            {{ (string) old('is_active', (string) $product->is_active) === '1' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="0"
                                            {{ (string) old('is_active', (string) $product->is_active) === '0' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('backend.products.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Update Product
                                    </button>
                                </div>
                            </form>
                        </div> {{-- card-body --}}
                    </div> {{-- card --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('backend_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize status Select2
            $('#is_active').select2({
                theme: 'bootstrap4',
                width: '100%',
                minimumResultsForSearch: 0
            });

            // Slugify
            function slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            }
            $('#name').on('input', function() {
                $('#slug').val(slugify($(this).val()));
            });

            const categoriesUrl = '{{ route('api.categories') }}';
            const subcategoriesUrlTemplate = '{{ route('api.sub-categories', ['id' => '___ID___']) }}';

            const $category = $('#category_id');
            $category.select2({
                theme: 'bootstrap4',
                placeholder: "Select Category",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: categoriesUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(i => ({
                                id: i.id,
                                text: i.name
                            }))
                        };
                    },
                    cache: true
                }
            });

            const $sub = $('#sub_category_id');

            function initSubSelectForCategory(categoryId, preselectId, preselectName) {
                if ($sub.hasClass('select2-hidden-accessible')) {
                    $sub.select2('destroy');
                }
                $sub.select2({
                    theme: 'bootstrap4',
                    placeholder: "Select Sub Category",
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: function() {
                            return subcategoriesUrlTemplate.replace('___ID___', categoryId);
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(i => ({
                                    id: i.id,
                                    text: i.name
                                }))
                            };
                        },
                        cache: true
                    }
                });
                if (preselectId && preselectName) {
                    const option = new Option(preselectName, preselectId, true, true);
                    $sub.append(option).trigger('change');
                }
            }

            $category.on('change', function() {
                const catId = $(this).val();
                $sub.val(null).trigger('change');
                if (!catId) {
                    if ($sub.hasClass('select2-hidden-accessible')) $sub.select2('destroy');
                    $sub.empty();
                    return;
                }
                initSubSelectForCategory(catId);
            });

            // Preload old category/subcategory
            (function() {
                const oldCatId = '{{ old('category_id', $product->category_id) }}';
                const oldSubId = '{{ old('sub_category_id', $product->sub_category_id) }}';
                const oldSubName = '{{ old('sub_category_name', $product->subCategory?->name) ?? '' }}';
                if (oldCatId) {
                    initSubSelectForCategory(oldCatId, oldSubId, oldSubName);
                } else {
                    if (!$sub.hasClass('select2-hidden-accessible')) $sub.select2({
                        theme: 'bootstrap4',
                        placeholder: "Select Sub Category (choose Category first)",
                        allowClear: true,
                        width: '100%',
                        data: []
                    });
                }
            })();

            // Validate sale_price < price
            $('#product-edit-form').on('submit', function(e) {
                const price = parseFloat($('#price').val()) || 0;
                const saleRaw = $('#sale_price').val();
                const sale = saleRaw === '' ? null : parseFloat(saleRaw);
                $('#sale_price').removeClass('is-invalid');
                $('#sale_price').next('.invalid-feedback.d-block').remove();
                if (sale !== null && sale >= price) {
                    e.preventDefault();
                    $('<div class="invalid-feedback d-block">Sale price must be less than Price.</div>')
                        .insertAfter($('#sale_price'));
                    $('#sale_price').addClass('is-invalid').focus();
                    return false;
                }
            });
        });
    </script>
@endpush
