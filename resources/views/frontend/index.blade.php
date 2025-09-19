@extends('layouts.frontend.app')


@push('frontend_styles')
    <style>
        .product-card {
            min-height: 180px;
        }

        .price-old {
            text-decoration: line-through;
            color: #777;
            margin-right: .5rem;
        }

        .price-sale {
            font-weight: 700;
            color: #d63384;
        }

        .spinner-placeholder {
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select2-container--bootstrap5 .select2-selection {
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
        }
    </style>
@endpush

@section('frontend_content')
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col">
                <h2 class="mb-0">Shop — Products</h2>
                <small class="text-muted">Filter by category, subcategory (Select2), name, slug, and effective price
                    (sale price if available).</small>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select id="category_id" name="category_id" class="form-select"></select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Subcategory</label>
                        <select id="subcategory_id" name="subcategory_id" class="form-select"></select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Name</label>
                        <input id="name" name="name" class="form-control" placeholder="Product name">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Slug</label>
                        <input id="slug" name="slug" class="form-control" placeholder="product-slug">
                    </div>

                    <div class="col-md-4 mt-2">
                        <label class="form-label">Effective Price (min - max)</label>
                        <div class="d-flex gap-2">
                            <input id="price_min" name="price_min" type="number" step="0.01" class="form-control"
                                placeholder="min">
                            <input id="price_max" name="price_max" type="number" step="0.01" class="form-control"
                                placeholder="max">
                        </div>
                    </div>

                    <div class="col-12 text-end mt-2">
                        <button type="button" id="applyFilter" class="btn btn-primary">Apply</button>
                        <button type="button" id="resetFilter" class="btn btn-outline-secondary">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="resultsArea">
            <div id="productsGrid" class="row gy-3"></div>

            <nav id="paginationWrapper" class="mt-4" aria-label="Products pagination"></nav>
        </div>
    </div>
@endsection

@push('frontend_scripts')
    <script>
        const API = {
            categories: '/api/categories',
            subcategories: id => `/api/categories/${id}/subcategories`,
            products: '/api/products/filter'
        };

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatMoney(val) {
            if (val === null || val === undefined) return '-';
            return parseFloat(val).toFixed(2);
        }

        function renderProducts(items) {
            if (!items || items.length === 0) {
                $('#productsGrid').html(
                    '<div class="col-12"><div class="alert alert-info mb-0">No products found.</div></div>');
                $('#paginationWrapper').empty();
                return;
            }

            const html = items.map(p => {
                const price = (p.price != null) ? parseFloat(p.price) : null;
                const sale = (p.sale_price != null) ? parseFloat(p.sale_price) : null;
                const final = (p.final_price != null) ? parseFloat(p.final_price) : (sale && sale < price ? sale :
                    price);
                const thumb = p.thumbnail_url ?
                    '/storage/' + p.thumbnail_url.replace(/^public\//, '') :
                    'https://via.placeholder.com/360x200?text=No+Image';


                let priceHtml = '';
                if (sale !== null && sale < price) {
                    priceHtml =
                        `<span class="price-old">৳ ${formatMoney(price)}</span><span class="price-sale">৳ ${formatMoney(sale)}</span>`;
                } else {
                    priceHtml = `<span class="fw-semibold">৳ ${formatMoney(price)}</span>`;
                }

                return `
                    <div class="col-md-4">
                        <div class="card product-card">
                        <img src="${escapeHtml(thumb)}" class="card-img-top" alt="${escapeHtml(p.name)}" style="height:160px;object-fit:cover;">
                        <div class="card-body">
                            <h5 class="card-title mb-1">${escapeHtml(p.name)}</h5>
                            <small class="text-muted d-block mb-2">/${escapeHtml(p.slug)}</small>
                            <p class="mb-1">${priceHtml}</p>
                            <p class="mb-1"><small class="text-muted">${escapeHtml(p.category_name || '')} › ${escapeHtml(p.subcategory_name || '')}</small></p>
                            <p class="mb-1"><small>Stock: ${p.stock_quantity ?? '-'}</small></p>
                            <a href="/product/${p.slug}" class="btn btn-sm btn-outline-primary mt-2">View</a>
                        </div>
                        </div>
                    </div>
                    `;
            }).join('');

            $('#productsGrid').html(html);
        }

        // Initialize Select2 for category (AJAX)
        function initCategorySelect2() {
            $('#category_id').select2({
                theme: 'bootstrap4',
                placeholder: 'All categories',
                allowClear: true,
                ajax: {
                    url: API.categories,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        // Expect backend to return Select2 format: { results: [{id, text}], pagination: { more: bool } }
                        if (Array.isArray(data)) {
                            // if backend returned plain array [{id,name}] adapt it
                            const results = data.map(i => ({
                                id: i.id,
                                text: i.name
                            }));
                            return {
                                results: results,
                                pagination: {
                                    more: false
                                }
                            };
                        }
                        return data;
                    },
                    cache: true
                }
            });

            // when category changes, clear and refresh subcategory select2
            $('#category_id').on('change', function() {
                const cid = $(this).val();
                // clear current subcategory selection and set param
                $('#subcategory_id').val(null).trigger('change');

                // destroy and re-init subcategory select2 with the chosen category param
                initSubcategorySelect2(cid);
            });
        }

        // Initialize Select2 for subcategory; takes categoryId (may be null)
        function initSubcategorySelect2(categoryId) {
            // destroy old instance if exists
            if ($.fn.select2 && $('#subcategory_id').data('select2')) {
                $('#subcategory_id').select2('destroy');
                $('#subcategory_id').empty();
            }

            $('#subcategory_id').select2({
                theme: 'bootstrap4',
                placeholder: categoryId ? 'All subcategories' : 'Select category first',
                allowClear: true,
                ajax: {
                    url: function() {
                        if (!categoryId) return API.categories; // fallback: list categories (none will match)
                        return API.subcategories(categoryId);
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                        };
                    },
                    processResults: function(data, params) {
                        if (Array.isArray(data)) {
                            const results = data.map(i => ({
                                id: i.id,
                                text: i.name
                            }));
                            return {
                                results: results,
                                pagination: {
                                    more: false
                                }
                            };
                        }
                        return data;
                    },
                    cache: true
                }
            });

            // if no categoryId, disable the control visually
            if (!categoryId) {
                $('#subcategory_id').prop('disabled', true);
                (function enableWhenNext() {
                    // keep it disabled until category is selected (Select2 shows placeholder)
                    setTimeout(function() {
                        if ($('#category_id').val()) {
                            $('#subcategory_id').prop('disabled', false);
                        } else enableWhenNext();
                    }, 300);
                })();
            } else {
                $('#subcategory_id').prop('disabled', false);
            }
        }

        function loadProducts(url) {
            url = url || API.products;
            const payload = {
                category_id: $('#category_id').val(),
                subcategory_id: $('#subcategory_id').val(),
                name: $('#name').val(),
                slug: $('#slug').val(),
                price_min: $('#price_min').val(),
                price_max: $('#price_max').val()
            };

            $('#productsGrid').html(
                '<div class="col-12 spinner-placeholder"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
            );
            $('#paginationWrapper').empty();

            $.get(url, payload).done(function(res) {
                renderProducts(res.data || []);
                if (res.links) $('#paginationWrapper').html(res.links);
                else $('#paginationWrapper').empty();
            }).fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message :
                    'Could not load products.';
                $('#productsGrid').html(
                    `<div class="col-12"><div class="alert alert-danger">${escapeHtml(message)}</div></div>`);
                $('#paginationWrapper').empty();
            });
        }

        $(function() {
            // init Select2 controls
            initCategorySelect2();
            initSubcategorySelect2(null); // disabled until category chosen

            // initial product load
            loadProducts();

            $('#applyFilter').on('click', function() {
                loadProducts();
            });
            $('#resetFilter').on('click', function() {
                $('#filterForm')[0].reset();
                // reset Select2 controls
                $('#category_id').val(null).trigger('change');
                $('#subcategory_id').val(null).trigger('change');
                initSubcategorySelect2(null);
                loadProducts();
            });

            // handle pagination clicks
            $(document).on('click', '#paginationWrapper a', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href) loadProducts(href);
            });
        });
    </script>
@endpush
