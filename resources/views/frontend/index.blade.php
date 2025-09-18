<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Products — Browse & Filter</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (for simplicity of AJAX in this example) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* small custom tweaks */
        .spinner-placeholder {
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card {
            min-height: 160px;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col">
                <h2 class="mb-0">Shop — Products</h2>
                <small class="text-muted">Filter by category, subcategory, name, slug, price</small>
            </div>
        </div>

        <!-- Filter row -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select id="category_id" name="category_id" class="form-select">
                            <option value="">All categories</option>
                            <!-- categories populated by JS -->
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Subcategory</label>
                        <select id="subcategory_id" name="subcategory_id" class="form-select" disabled>
                            <option value="">Select category first</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Name</label>
                        <input id="name" name="name" class="form-control" placeholder="Product name">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Slug</label>
                        <input id="slug" name="slug" class="form-control" placeholder="product-slug">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Price (min - max)</label>
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

        <!-- Results -->
        <div id="resultsArea">
            <div id="productsGrid" class="row gy-3">
                <!-- product cards injected here -->
            </div>

            <nav id="paginationWrapper" class="mt-4" aria-label="Products pagination">
                <!-- pagination links injected here -->
            </nav>
        </div>
    </div>

    <script>
        /*
      Requirements / expectations:
      - API endpoints:
          GET /api/categories            -> returns [{id,name},...]
          GET /api/categories/{id}/subcategories -> returns [{id,name},...]
          GET /api/products/filter      -> accepts query params (category_id, subcategory_id, name, slug, price_min, price_max, page)
                                           returns JSON { data: [ {id,name,slug,category_name,subcategory_name,price,final_price,stock_quantity,thumbnail_url}, ... ], links: '<ul class="pagination">...</ul>' }
      - If your API returns different field names, adapt the mapping below.
    */

        const API = {
            categories: '/api/categories',
            subcategories: id => `/api/categories/${id}/subcategories`,
            products: '/api/products/filter'
        };

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
                const price = (p.final_price != null) ? p.final_price : p.price;
                const thumb = p.thumbnail_url || 'https://via.placeholder.com/240x160?text=No+Image';
                return `
      <div class="col-md-4">
        <div class="card product-card">
          <img src="${thumb}" class="card-img-top" alt="${escapeHtml(p.name)}" style="height:160px; object-fit:cover;">
          <div class="card-body">
            <h5 class="card-title mb-1">${escapeHtml(p.name)}</h5>
            <small class="text-muted d-block mb-2">/${escapeHtml(p.slug)}</small>
            <p class="mb-1"><strong>Price:</strong> ৳ ${formatMoney(price)}</p>
            <p class="mb-1"><small class="text-muted">${escapeHtml(p.category_name || '')} › ${escapeHtml(p.subcategory_name || '')}</small></p>
            <p class="mb-1"><small>Stock: ${p.stock_quantity ?? '-'}</small></p>
            <a href="/product/${p.id}" class="btn btn-sm btn-outline-primary mt-2">View</a>
          </div>
        </div>
      </div>
    `;
            }).join('');

            $('#productsGrid').html(html);
        }

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function loadCategories() {
            $('#category_id').prop('disabled', true);
            $.get(API.categories).done(function(data) {
                let html = '<option value="">All categories</option>';
                (data || []).forEach(c => {
                    html += `<option value="${c.id}">${escapeHtml(c.name)}</option>`;
                });
                $('#category_id').html(html).prop('disabled', false);
            }).fail(function() {
                $('#category_id').html('<option value="">Error loading</option>');
            });
        }

        function loadSubcategories(categoryId) {
            const $sub = $('#subcategory_id');
            if (!categoryId) {
                $sub.html('<option value="">Select category first</option>').prop('disabled', true);
                return;
            }
            $sub.prop('disabled', true).html('<option>Loading...</option>');
            $.get(API.subcategories(categoryId)).done(function(data) {
                let html = '<option value="">All subcategories</option>';
                (data || []).forEach(s => html += `<option value="${s.id}">${escapeHtml(s.name)}</option>`);
                $sub.html(html).prop('disabled', false);
            }).fail(function() {
                $sub.html('<option value="">Error</option>').prop('disabled', true);
            });
        }

        function loadProducts(url) {
            // if url provided (pagination), use it; else use API.products
            url = url || API.products;
            const payload = {
                category_id: $('#category_id').val(),
                subcategory_id: $('#subcategory_id').val(),
                name: $('#name').val(),
                slug: $('#slug').val(),
                price_min: $('#price_min').val(),
                price_max: $('#price_max').val()
            };

            // show spinner/placeholder
            $('#productsGrid').html(
                '<div class="col-12 spinner-placeholder"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                );
            $('#paginationWrapper').empty();

            $.get(url, payload).done(function(res) {
                // res expected to contain res.data (array) and res.links (html string)
                renderProducts(res.data || []);
                if (res.links) {
                    $('#paginationWrapper').html(res.links);
                } else {
                    $('#paginationWrapper').empty();
                }
            }).fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message :
                    'Could not load products.';
                $('#productsGrid').html(
                    `<div class="col-12"><div class="alert alert-danger">${escapeHtml(message)}</div></div>`);
                $('#paginationWrapper').empty();
            });
        }

        $(function() {
            // initial bootstrap
            loadCategories();
            loadProducts(); // initial product load

            // when category changes, load subcategories
            $('#category_id').on('change', function() {
                const id = $(this).val();
                loadSubcategories(id);
            });

            // apply & reset
            $('#applyFilter').on('click', function() {
                loadProducts();
            });

            $('#resetFilter').on('click', function() {
                $('#filterForm')[0].reset();
                $('#subcategory_id').html('<option value="">Select category first</option>').prop(
                    'disabled', true);
                loadProducts();
            });

            // handle pagination clicks (server should return full <ul class="pagination"> with links)
            $(document).on('click', '#paginationWrapper a', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href) loadProducts(href);
            });

        });
    </script>
</body>

</html>
