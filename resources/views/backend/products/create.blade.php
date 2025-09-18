@extends('layouts.backend.app')

@section('title', 'Create Product')

@push('backend_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('admin_content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Create Product</h3>
            </div>
            <div class="col-sm-6">
                <x-backend.breadcrumb :items="[
                    ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                    ['label' => 'Products', 'route' => 'backend.products.index'],
                    ['label' => 'Create', 'active' => true],
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
                        <h3 class="card-title mb-0">Add New Product</h3>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="card-body">
                        <form action="{{ route('backend.products.store') }}" method="POST" id="product-create-form">
                            @csrf

                            {{-- Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Enter product name" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Slug --}}
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <small class="text-muted">(auto)</small></label>
                                <input type="text" name="slug" id="slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug') }}" placeholder="product-slug" readonly>
                                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Optional product description" rows="4">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                {{-- Price --}}
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" step="0.01" min="0"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price') }}" placeholder="0.00" required>
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Sale Price --}}
                                <div class="col-md-4 mb-3">
                                    <label for="sale_price" class="form-label">Sale Price <small class="text-muted">(optional)</small></label>
                                    <input type="number" name="sale_price" id="sale_price" step="0.01" min="0"
                                        class="form-control @error('sale_price') is-invalid @enderror"
                                        value="{{ old('sale_price') }}" placeholder="0.00">
                                    @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">If set, sale price must be smaller than Price.</div>
                                </div>

                                {{-- Stock --}}
                                <div class="col-md-4 mb-3">
                                    <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" id="stock" min="0"
                                        class="form-control @error('stock') is-invalid @enderror"
                                        value="{{ old('stock', 0) }}" placeholder="0" required>
                                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Category & SubCategory (Select2 AJAX) --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        @if(old('category_id') && old('category_name'))
                                            <option value="{{ old('category_id') }}" selected>{{ old('category_name') }}</option>
                                        @endif
                                    </select>
                                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sub_category_id" class="form-label">Sub Category <span class="text-danger">*</span></label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-select @error('sub_category_id') is-invalid @enderror" required>
                                        @if(old('sub_category_id') && old('sub_category_name'))
                                            <option value="{{ old('sub_category_id') }}" selected>{{ old('sub_category_name') }}</option>
                                        @endif
                                    </select>
                                    @error('sub_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('backend.products.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Create Product
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
    // Initialize status Select2 for consistent UI
    $('#is_active').select2({
        theme: 'bootstrap4',
        width: '100%',
        minimumResultsForSearch: 0
    });

    // Helper slugify
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start
            .replace(/-+$/, '');            // Trim - from end
    }

    $('#name').on('input', function() {
        $('#slug').val(slugify($(this).val()));
    });

    // Named route URLs (Blade will render these server-side)
    const categoriesUrl = '{{ route("api.categories") }}';
    // route('api.sub-categories', ['id' => '___ID___']) produces something like /categories/___ID___/subcategories
    const subcategoriesUrlTemplate = '{{ route("api.sub-categories", ["id" => "___ID___"]) }}';

    // --- Category Select2 (AJAX) ---
    const $category = $('#category_id');
    $category.select2({
        theme: 'bootstrap4',
        placeholder: "Select Category",
        allowClear: true,
        width: '100%',
        ajax: {
            url: categoriesUrl, // uses named route: api.categories
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term || '' }; // backend search param
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return { id: item.id, text: item.name };
                    })
                };
            },
            cache: true
        }
    });

    // Preload old category if provided (expects old('category_id') and old('category_name'))
    (function preloadCategory() {
        const oldId = '{{ old('category_id') }}';
        const oldName = '{{ old('category_name') ?? '' }}';
        if (oldId && oldName) {
            const option = new Option(oldName, oldId, true, true);
            $category.append(option).trigger('change');
        }
    })();

    // --- SubCategory Select2 (AJAX but depends on selected category) ---
    const $sub = $('#sub_category_id');

    function initSubSelectForCategory(categoryId, preselectId, preselectName) {
        // destroy previous Select2 to reset ajax endpoint if already initialized
        if ($sub.hasClass('select2-hidden-accessible')) {
            $sub.select2('destroy');
        }

        $sub.select2({
            theme: 'bootstrap4',
            placeholder: "Select Sub Category",
            allowClear: true,
            width: '100%',
            ajax: {
                // Build URL by replacing placeholder with actual categoryId
                url: function () {
                    return subcategoriesUrlTemplate.replace('___ID___', categoryId);
                },
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                },
                cache: true
            }
        });

        // Preselect if available (e.g., old input)
        if (preselectId && preselectName) {
            const option = new Option(preselectName, preselectId, true, true);
            $sub.append(option).trigger('change');
        }
    }

    // When category changes, load subcategories for that category
    $category.on('change', function() {
        const catId = $(this).val();
        // clear previous sub selection
        $sub.val(null).trigger('change');

        if (!catId) {
            // destroy sub select2 to clear ajax endpoint and empty options
            if ($sub.hasClass('select2-hidden-accessible')) {
                $sub.select2('destroy');
            }
            $sub.empty();
            return;
        }

        initSubSelectForCategory(catId);
    });

    // If old category and old subcategory present, initialize both on load
    (function preloadSubIfNeeded() {
        const oldCatId = '{{ old('category_id') }}';
        const oldSubId = '{{ old('sub_category_id') }}';
        const oldSubName = '{{ old('sub_category_name') ?? '' }}';

        if (oldCatId) {
            // ensure sub select initialized for this category (preselect old sub if present)
            initSubSelectForCategory(oldCatId, oldSubId, oldSubName);
        } else {
            // initialize empty sub select (no category chosen)
            if (!$sub.hasClass('select2-hidden-accessible')) {
                $sub.select2({
                    theme: 'bootstrap4',
                    placeholder: "Select Sub Category (choose Category first)",
                    allowClear: true,
                    width: '100%',
                    data: []
                });
            }
        }
    })();

    // Validate sale_price < price on submit
    $('#product-create-form').on('submit', function(e) {
        const price = parseFloat($('#price').val()) || 0;
        const saleRaw = $('#sale_price').val();
        const sale = saleRaw === '' ? null : parseFloat(saleRaw);

        // remove previous sale error if any
        $('#sale_price').removeClass('is-invalid');
        $('#sale_price').next('.invalid-feedback.d-block').remove();

        if (sale !== null && sale >= price) {
            e.preventDefault();
            $('<div class="invalid-feedback d-block">Sale price must be less than Price.</div>').insertAfter($('#sale_price'));
            $('#sale_price').addClass('is-invalid').focus();
            return false;
        }
    });
});
</script>

@endpush
