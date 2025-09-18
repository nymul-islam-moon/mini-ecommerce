{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Products')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Products</h3>
                </div>
                <div class="col-sm-6">
                    <x-backend.breadcrumb :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title flex-grow-1 mb-0">All Products</h3>

                            {{-- Optional: simple search by name/slug/description --}}
                            <form action="{{ route('backend.products.index') }}" method="GET"
                                class="d-none d-sm-flex me-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                        placeholder="Search name/slug/desc">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>

                            <a href="{{ route('backend.products.create') }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-lg"></i> Create Product
                            </a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px">#</th>
                                            <th style="min-width: 220px;">Name</th>
                                            <th style="min-width: 200px;">Slug</th>
                                            <th style="min-width: 200px;">Status</th>
                                            <th style="width: 130px;">Categories</th>
                                            <th style="width: 130px;">Subcategories</th>
                                            <th style="width: 170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($products as $idx => $product)
                                            <tr>
                                                <td>{{ $products->firstItem() + $idx }}</td>
                                                <td class="fw-semibold text-break">{{ $product->name }}</td>
                                                <td class="text-break">{{ $product->slug }}</td>
                                                <td class="text-break">
                                                    <span
                                                        class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        
                                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span
                                                        class="badge bg-info">{{ $product->category ?? 'N/A'}}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-info">{{ $product->subCategory ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <a href="{{ route('backend.products.show', $product) }}"
                                                            class="btn btn-sm btn-outline-secondary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('backend.products.edit', $product) }}"
                                                            class="btn btn-sm btn-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('backend.products.destroy', $product) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Delete this product? This action cannot be undone.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    No products found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <div class="float-end">
                                {!! $products->appends(['q' => request('q')])->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>

                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
@endsection
