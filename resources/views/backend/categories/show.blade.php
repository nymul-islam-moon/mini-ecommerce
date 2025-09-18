{{-- resources/views/admin/categories/show.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Category Details')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Category Details</h3>
                </div>
                <div class="col-sm-6">
                    <x-backend.breadcrumb :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Category', 'route' => 'admin.products.categories.index'],
                        ['label' => 'Details', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Category Show Card -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Category — {{ $category->name }}</h3>


                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <dl class="row mb-0">
                                {{-- Name --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Name
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 fw-semibold text-break">
                                    {{ $category->name }}
                                </dd>

                                {{-- Slug --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Slug
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ $category->slug ?? '—' }}
                                </dd>

                                {{-- Description --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Description
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break"
                                    style="white-space: pre-line;">
                                    @if (filled($category->description))
                                        {{ $category->description }}
                                    @else
                                        <em class="text-muted">Not provided</em>
                                    @endif
                                </dd>

                                {{-- Products (optional) --}}
                                @isset($category->products_count)
                                    <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                        Products
                                    </dt>
                                    <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                        <span class="badge text-bg-secondary">{{ $category->products_count }}</span>
                                    </dd>
                                @endisset

                                {{-- Status --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Status
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    @if ($category->is_active)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>
                                    @endif
                                </dd>

                                {{-- Created --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Created
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    {{ optional($category->created_at)->format('M d, Y h:i A') }}
                                </dd>

                                {{-- Last Updated --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3">
                                    Last Updated
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 mb-0">
                                    {{ optional($category->updated_at)->format('M d, Y h:i A') }}
                                </dd>
                            </dl>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('backend.categories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>

                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('backend.categories.edit', $category) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form action="{{ route('backend.categories.destroy', $category) }}" method="POST"
                                    class="d-inline-block m-0 p-0"
                                    onsubmit="return confirm('Delete this category? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                    <!-- End Card -->
                </div>
            </div>
        </div>
    </div>
@endsection
