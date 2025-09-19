@extends('layouts.backend.app')

@section('title', 'Edit Category')

@push('backend_styles')
    <style>
        .card {
            border-radius: .75rem;
            box-shadow: 0 6px 18px rgba(29, 31, 45, 0.06);
        }

        .card-header {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
            border-bottom: none;
        }

        .form-label.small {
            font-size: .85rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 .15rem rgba(13, 110, 253, 0.12);
        }
    </style>
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Category</h3>
                </div>
                <div class="col-sm-6">
                    <x-backend.breadcrumb :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Category', 'route' => 'backend.categories.index'],
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
                        <div class="card-header d-flex align-items-center">
                            <div>
                                <h3 class="card-title mb-0">Edit Category - {{ $category->name }}</h3>
                            </div>

                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('backend.categories.show', $category) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('backend.categories.destroy', $category) }}" method="POST"
                                    class="m-0 p-0"
                                    onsubmit="return confirm('Delete this category? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i>
                                        Delete</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('backend.categories.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label small">Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" autofocus
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $category->name) }}" placeholder="Enter category name"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="is_active" class="form-label small">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="category_status"
                                        class="form-select select2 @error('is_active') is-invalid @enderror" required>
                                        <option value="1"
                                            {{ old('is_active', $category->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0"
                                            {{ old('is_active', $category->status) == '0' ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="description" class="form-label small">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional category description" rows="5">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('backend.categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Category
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-muted">
                                Created: {{ optional($category->created_at)->format('M d, Y h:i A') }}
                            </div>

                            <div class="small text-muted ms-auto">
                                Last updated: {{ optional($category->updated_at)->diffForHumans() ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('backend_scripts')
   
@endpush
