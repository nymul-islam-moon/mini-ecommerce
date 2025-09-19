@extends('layouts.frontend.app')

@section('title', $product->name ?? 'Product')

@push('frontend_styles')
    <style>
        /* Product show styles */
        .product-hero {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        @media(min-width: 992px) {
            .product-hero {
                grid-template-columns: 1fr 1fr;
            }
        }

        .product-card {
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(18, 38, 63, 0.06);
            overflow: hidden;
        }

        .product-image {
            background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image img {
            max-height: 520px;
            width: 100%;
            object-fit: contain;
            border-radius: 8px;
        }

        .price-old {
            text-decoration: line-through;
            color: #6c757d;
            margin-right: .6rem;
        }

        .price-current {
            font-size: 1.6rem;
            font-weight: 800;
            color: #dc3545;
        }

        .price-badge {
            font-size: .85rem;
            padding: .25rem .5rem;
            border-radius: 6px;
        }

        .meta-row {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .meta-row .small {
            color: #6c757d;
        }

        .btn-outline-favorite {
            border-color: #e9ecef;
            color: #444;
        }

        .specs dt {
            font-weight: 600;
        }

        .specs dd {
            margin: 0 0 .75rem 0;
            color: #495057;
        }

        .gallery-thumb {
            background: transparent;
        }

        .gallery-thumb img {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            max-width: 88px;
            height: 64px;
            object-fit: cover;
        }
    </style>
@endpush

@section('frontend_content')
    <div class="container my-5">
        <div class="product-hero product-card p-3">
            <!-- Image column -->
            <div class="product-image">
                <img id="mainProductImage"
                    src="{{ asset('storage/' . ($product->main_image ?? 'products/main_images/placeholder.png')) }}"
                    alt="{{ $product->name }}">
            </div>

            <!-- Info column -->
            <div class="p-3">
                <h1 class="mb-2">{{ $product->name }}</h1>

                <div class="meta-row mb-3">
                    @if ($category)
                        <small class="small">Category: <strong>{{ $category->name }}</strong></small>
                    @endif
                    @if ($subcategory)
                        <small class="small">Subcategory: <strong>{{ $subcategory->name }}</strong></small>
                    @endif
                    <small class="small">SKU: <strong>{{ $product->slug }}</strong></small>
                </div>

                <div class="mb-3 d-flex align-items-end gap-3">
                    @if ($product->sale_price && $product->sale_price < $product->price)
                        <div>
                            <div class="price-current">৳ {{ number_format($product->sale_price, 2) }}</div>
                            <div class="price-old">৳ {{ number_format($product->price, 2) }}</div>
                        </div>
                        <div>
                            <span class="badge bg-success price-badge">On Sale</span>
                        </div>
                    @else
                        <div class="price-current">৳ {{ number_format($product->price, 2) }}</div>
                    @endif
                </div>

                <p class="text-muted mb-3">{!! nl2br(
                    e(
                        strlen($product->short_description ?? '')
                            ? $product->short_description
                            : $product->description ?? 'No description available.',
                    ),
                ) !!}</p>

                <dl class="specs mb-4">
                    <dt>Availability</dt>
                    <dd>
                        @if ($product->stock > 0)
                            <span class="badge bg-success">In Stock ({{ $product->stock }})</span>
                        @else
                            <span class="badge bg-danger">Out of Stock</span>
                        @endif
                    </dd>

                    <dt>Sub category</dt>
                    <dd>{{ $product->sub_category_id ? $subcategory->name ?? '—' : '—' }}</dd>

                    <dt>Active</dt>
                    <dd>{{ $product->is_active ? 'Yes' : 'No' }}</dd>
                </dl>

                <form action="" method="POST" class="row g-2 align-items-center">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="col-auto">
                        <label class="form-label">Quantity</label>
                        <div class="input-group" style="width:140px;">
                            <button type="button" class="btn btn-outline-secondary" id="qtyMinus">−</button>
                            <input type="number" name="quantity" id="quantity" class="form-control text-center"
                                value="1" min="1">
                            <button type="button" class="btn btn-outline-secondary" id="qtyPlus">+</button>
                        </div>
                    </div>

                    <div class="col">
                        @if ($product->stock > 0)
                            <button type="submit" class="btn btn-primary btn-lg w-100">Add to cart</button>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg w-100" disabled>Out of stock</button>
                        @endif
                    </div>

                    <div class="col-12 d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-outline-favorite">❤ Add to wishlist</button>
                        <button type="button" class="btn btn-outline-secondary">Share</button>
                    </div>
                </form>

                <hr class="my-4">

                <div>
                    <h5 class="mb-2">Product details</h5>
                    <div class="text-muted small">{!! $product->description ?? '<em>No additional details.</em>' !!}</div>
                </div>
            </div>
        </div>

        <!-- Related products -->
        <div class="mt-4">
            <h4>Related products</h4>
            <div class="row g-3 mt-2">
                @foreach ($related as $rel)
                    <div class="col-md-3">
                        <div class="card">
                            <img src="{{ asset('storage/' . $rel->main_image) }}" class="card-img-top"
                                alt="{{ $rel->name }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $rel->name }}</h5>
                                <p class="mb-0">
                                    @if ($rel->sale_price && $rel->sale_price < $rel->price)
                                        <span class="text-muted text-decoration-line-through">${{ $rel->price }}</span>
                                        <span class="text-danger">${{ $rel->sale_price }}</span>
                                    @else
                                        <span>${{ $rel->price }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection

@push('frontend_scripts')
    <script>
        (function() {
            // quantity controls
            var qtyInput = document.getElementById('quantity');
            document.getElementById('qtyMinus').addEventListener('click', function() {
                var v = parseInt(qtyInput.value) || 1;
                if (v > 1) qtyInput.value = v - 1;
            });
            document.getElementById('qtyPlus').addEventListener('click', function() {
                var v = parseInt(qtyInput.value) || 1;
                qtyInput.value = v + 1;
            });

            // graceful image fallback if file missing
            var img = document.getElementById('mainProductImage');
            img.addEventListener('error', function() {
                this.src = 'https://via.placeholder.com/800x600?text=No+Image';
            });
        })();
    </script>
@endpush
