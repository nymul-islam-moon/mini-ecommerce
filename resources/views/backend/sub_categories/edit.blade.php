{{-- resources/views/admin/sub-categories/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit SubCategory')

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
                    <h3 class="mb-0">Edit SubCategory</h3>
                </div>
                <div class="col-sm-6">
                    <x-backend.breadcrumb :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'SubCategories', 'route' => 'admin.products.sub-categories.index'],
                        ['label' => 'Edit', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Edit SubCategory — {{ $subCategory->name }}</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('backend.sub-categories.update', $subCategory) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- SubCategory Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Sub-Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $subCategory->name) }}" placeholder="Enter sub-category name"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="category_status"
                                        class="form-select @error('is_active') is-invalid @enderror" required>
                                        <option value="1"
                                            {{ old('is_active', $subCategory->is_active) == '1' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ old('is_active', $subCategory->is_active) == '0' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id"
                                        class="form-select @error('category_id') is-invalid @enderror" required>
                                        @if (old('category_id', $subCategory->category_id) && old('category_name', $subCategory->category->name ?? ''))
                                            <option value="{{ old('category_id', $subCategory->category_id) }}" selected>
                                                {{ old('category_name', $subCategory->category->name ?? '') }}</option>
                                        @endif
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional category description">{{ old('description', $subCategory->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('backend.sub-categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update SubCategory
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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
            // Status select2
            $('#category_status').select2({
                theme: 'bootstrap4',
                placeholder: "Select Status",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });

            // Category select2 AJAX
            function initCategorySelect2() {
                const $select = $('#category_id');

                $select.select2({
                    theme: 'bootstrap4',
                    placeholder: "Select Category",
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route('api.categories') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                // preload old value if exists
                const oldId = '{{ old('category_id', $subCategory->category_id) }}';
                const oldName = '{{ old('category_name', $subCategory->category->name ?? '') }}';
                if (oldId && oldName) {
                    const option = new Option(oldName, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            }

            initCategorySelect2();
        });
    </script>
@endpush
