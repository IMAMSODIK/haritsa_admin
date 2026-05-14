@extends('layouts.template')

@section('own_style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .product-card {
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        }

        /* image container */
        .product-image {
            height: 200px;
            overflow: hidden;
            background: #f8f8f8;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        /* text */
        .product-title {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        /* price */
        .price {
            font-weight: bold;
            font-size: 18px;
            color: #16a34a;
        }


        .photo-item {
            transition: all 0.35s ease;
        }

        .photo-remove {
            opacity: 0;
            transform: scale(0.7);
        }

        .promo-price {
            font-weight: bold;
            font-size: 18px;
            color: #16a34a;
        }

        .original-price {
            font-size: 13px;
            color: #999;
            text-decoration: line-through;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ $pageTitle }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#stroke-home') }}">
                                    </use>
                                </svg></a></li>
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container">

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary add-produk" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                    <i class="fa fa-plus"></i> Tambah Produk
                </button>
            </div>
        </div>

        <div class="row g-4">
            @forelse($produks as $p)
                @php
                    $photo = $p['photos'][0]['photoUrl'] ?? 'https://via.placeholder.com/400';
                    $stores = $p['store'] ?? [];
                    $isAllStores = $p['isAllStores'] ?? false;
                @endphp

                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm product-card" data-product='@json($p)'>
                        <div class="product-image">
                            <img src="{{ $photo }}" class="img-fluid rounded-top">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="product-title fw-bold">
                                {{ $p['brand']['name'] ?? '-' }}
                            </div>
                            <small class="text-muted mb-2">
                                {{ $p['name'] }}
                            </small>
                            <div class="mb-2">
                                <span class="badge bg-light text-dark">
                                    {{ $p['category']['name'] ?? '-' }}
                                </span>
                            </div>
                            <div class="mb-2">

                                @if ($isAllStores)
                                    <span class="badge bg-success">
                                        Semua Toko
                                    </span>
                                @elseif(count($stores) > 0)
                                    <span class="badge bg-secondary">
                                        {{ $stores['name'] }}
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        Tidak ada toko
                                    </span>
                                @endif

                            </div>
                            <div class="price mb-2">

                                @if ($p['promoPrice'] && $p['promoPrice'] < $p['price'])
                                    <div class="fw-bold text-danger">
                                        Rp {{ number_format($p['promoPrice'], 0, ',', '.') }}
                                    </div>

                                    <small class="text-muted text-decoration-line-through">
                                        Rp {{ number_format($p['price'], 0, ',', '.') }}
                                    </small>
                                @else
                                    <div class="fw-bold">
                                        Rp {{ number_format($p['price'], 0, ',', '.') }}
                                    </div>
                                @endif

                            </div>

                            <div class="mt-auto d-flex gap-2 pt-3">
                                <button class="btn btn-light border w-100 edit-produk" data-id="{{ $p['id'] }}">
                                    Edit
                                </button>

                                <button class="btn btn-light border text-danger w-100"
                                    onclick="deleteProduct('{{ $p['id'] }}', this)">
                                    Hapus
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Tidak ada produk
                    </div>
                </div>
            @endforelse

        </div>

    </div>

    <div class="modal fade" id="productPreviewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- carousel foto -->
                    <div id="previewCarousel" class="carousel slide mb-3">
                        <div class="carousel-inner" id="previewPhotos"></div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#previewCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>

                        <button class="carousel-control-next" type="button" data-bs-target="#previewCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>

                    <!-- Informasi Brand dan Kategori -->
                    <div class="mb-3">
                        <span class="badge bg-primary me-2" id="previewBrand"></span>
                        <span class="badge bg-secondary" id="previewCategory"></span>
                    </div>

                    <!-- Informasi Toko -->
                    <div class="mb-3" id="previewStoreContainer">
                        <small class="text-muted">Toko:</small>
                        <span id="previewStore" class="ms-2"></span>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Deskripsi</h6>
                        <p id="previewDescription" class="text-muted"></p>
                    </div>

                    <!-- Informasi Harga dan Stok dalam Grid -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Harga Normal</small>
                                <span class="fw-bold" id="previewPrice"></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Harga Promo</small>
                                <span class="fw-bold text-danger" id="previewPromo"></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Stok</small>
                                <span class="fw-bold" id="previewStock"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addStoreModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Tambah Produk</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addProductForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="p_isAllStores">
                                <label class="form-check-label">
                                    Berlaku untuk semua toko
                                </label>
                            </div>
                        </div>

                        <!-- STORE -->
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <div class="input-group">
                                <input type="text" id="p_storeName" class="form-control" readonly
                                    placeholder="Pilih store">
                                <input type="hidden" id="p_storeId">
                                <button class="btn btn-outline-primary" id="select-store" type="button"
                                    data-bs-toggle="modal" data-bs-target="#storePickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <!-- SKU -->
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input class="form-control" id="p_sku" placeholder="Contoh: TOYS-001">
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input class="form-control" id="p_name" placeholder="Nama produk">
                        </div>

                        <!-- DESKRIPSI -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="p_description" rows="3" placeholder="Deskripsi produk"></textarea>
                        </div>

                        <!-- KATEGORI -->
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <div class="input-group">
                                <input class="form-control" id="p_category" placeholder="Kategori produk" readonly>
                                <input type="hidden" id="p_categoryId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#categoryPickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Brand</label>
                            <div class="input-group">
                                <input class="form-control" id="p_brand" placeholder="Brand produk" readonly>
                                <input type="hidden" id="p_brandId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#brandPickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <!-- HARGA -->
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Harga</label>
                                <input class="form-control rupiah" id="p_price" placeholder="Rp 0">
                            </div>
                            <div class="col mb-3">
                                <label class="form-label">Harga Promo</label>
                                <input class="form-control rupiah" id="p_promoPrice" placeholder="Rp 0">
                            </div>
                        </div>

                        <!-- STOCK -->
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input class="form-control" id="p_stock" placeholder="Jumlah stock">
                        </div>

                        <!-- FOTO URL -->
                        <div class="mb-3">
                            <label class="form-label">Upload Foto Produk (max 5)</label>
                            <input type="file" id="p_photos" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Maksimal 5 gambar</small>

                            <div id="photoPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                        </div>

                        <div id="addProductAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100" type="submit">Simpan Produk</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editProductForm">
                    <input type="hidden" id="edit_product_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_isAllStores">
                                <label class="form-check-label">
                                    Berlaku untuk semua toko
                                </label>
                            </div>
                        </div>

                        <!-- STORE -->
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <div class="input-group">
                                <input type="text" id="edit_storeName" class="form-control" placeholder="Pilih nama store">
                                <input type="hidden" id="edit_storeId">
                                <button class="btn btn-outline-primary" id="edit-select-store" type="button"
                                    data-bs-toggle="modal" data-bs-target="#storePickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <!-- SKU -->
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input class="form-control" id="edit_sku">
                        </div>

                        <!-- Nama -->
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input class="form-control" id="edit_name">
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description" rows="3"></textarea>
                        </div>

                        <!-- Kategori -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Kategori</label>
                            <div class="input-group">
                                <input class="form-control" id="p_editCategory" placeholder="Kategori produk" readonly>
                                <input type="hidden" id="p_editCategoryId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#categoryPickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Brand</label>
                            <div class="input-group">
                                <input class="form-control" id="p_editBrand" placeholder="Brand produk" readonly>
                                <input type="hidden" id="p_editBrandId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#brandPickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <!-- Harga -->
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Harga</label>
                                <input class="form-control rupiah" id="edit_price">
                            </div>
                            <div class="col mb-3">
                                <label class="form-label">Harga Promo</label>
                                <input class="form-control rupiah" id="edit_promoPrice">
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input class="form-control" id="edit_stock">
                        </div>

                        <!-- Foto URL -->
                        <div class="mb-3">
                            <label class="form-label">Foto Produk</label>
                            <div id="existingPhotos" class="d-flex flex-wrap gap-2 mb-3"></div>

                            <input type="file" id="newPhotos" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Tambah foto baru</small>

                            <div id="newPhotoPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>

                        <div id="editProductAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary w-100">Update Produk</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="storePickerModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Pilih Store</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- <input type="text" id="storeSearch" class="form-control mb-3" placeholder="Cari store..."> --}}

                    <div id="storeList" style="max-height:400px; overflow:auto;"></div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryPickerModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Pilih Kategori</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- <input type="text" id="storeSearch" class="form-control mb-3" placeholder="Cari store..."> --}}

                    <div id="kategoriList" style="max-height:400px; overflow:auto;"></div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="brandPickerModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Pilih Brand</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- <input type="text" id="storeSearch" class="form-control mb-3" placeholder="Cari store..."> --}}

                    <div id="brandList" style="max-height:400px; overflow:auto;"></div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('own_script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        let modal = "addStoreModal";
        let editSelectedStoreIds = [];

        $('#p_isAllStores').on('change', function() {
            if ($(this).is(':checked')) {
                $('#p_storeId').val('');
                $('#p_storeName').val('Semua Toko');
                $('#p_storeName').prop('disabled', true);
                $('#select-store').css('display', 'none');
            } else {
                $('#p_storeName').val('');
                $('#p_storeName').prop('disabled', false);
                $('#select-store').css('display', '');
            }
        });

        $('#edit_isAllStores').on('change', function() {
            if ($(this).is(':checked')) {
                editSelectedStoreIds = [];
                $('#edit_storeName').val('Semua Toko');
                $('#edit_storeName').prop('disabled', true);
                $('#edit-select-store').css('display', 'none');
            } else {
                $('#edit_storeName').val('');
                $('#edit_storeName').prop('disabled', false);
                $('#edit-select-store').css('display', '');
            }

        });

        $(".add-produk").on("click", function() {
            modal = "addStoreModal";
        });

        $(".edit-produk").on("click", function() {
            modal = "editProductModal";
        });

        function formatRupiah(val) {
            return new Intl.NumberFormat('id-ID').format(val);
        }

        function cleanNumber(val) {
            return val.replace(/\D/g, '');
        }

        $('.rupiah').on('input', function() {
            let clean = cleanNumber(this.value);
            this.value = clean ? formatRupiah(clean) : '';
        });

        let selectedFiles = [];

        $('#p_photos').on('change', function() {
            let newFiles = Array.from(this.files);

            // cek limit
            if (selectedFiles.length + newFiles.length > 5) {
                alert('Maksimal 5 foto!');
                this.value = '';
                return;
            }

            selectedFiles = selectedFiles.concat(newFiles);
            renderPreview();

            this.value = ''; // reset input biar bisa pilih file yang sama lagi
        });

        function renderPreview() {
            let preview = $('#photoPreview');
            preview.html('');

            selectedFiles.forEach((file, index) => {
                let reader = new FileReader();

                reader.onload = function(e) {
                    preview.append(`
                <div style="position:relative">
                    <img src="${e.target.result}"
                        style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">

                    <button type="button"
                        onclick="removePhoto(${index})"
                        style="
                            position:absolute;
                            top:-6px;
                            right:-6px;
                            background:red;
                            color:white;
                            border:none;
                            border-radius:50%;
                            width:22px;
                            height:22px;
                            font-size:12px;
                            cursor:pointer;">
                        ×
                    </button>
                </div>
            `);
                };

                reader.readAsDataURL(file);
            });
        }

        function removePhoto(index) {
            selectedFiles.splice(index, 1);
            renderPreview();
        }


        function loadBrand() {

            $('#brandList').html('<div class="text-center p-3">Loading...</div>');

            $.get('/products/brands', function(res) {

                let html = '';

                res.forEach(cat => {
                    html += `
                <div class="card mb-2 cursor-pointer brand-item"
                     data-id="${cat.id}"
                     data-name="${cat.name}">
                    <div class="card-body">
                        <b>${cat.name}</b><br>
                    </div>
                </div>
            `;
                });

                $('#brandList').html(html || '<div class="text-muted">Tidak ada brand</div>');
            });

        }

        function loadKategori() {

            $('#kategoriList').html('<div class="text-center p-3">Loading...</div>');

            $.get('/products/categories', function(res) {

                let html = '';

                res.forEach(cat => {
                    html += `
                <div class="card mb-2 cursor-pointer cat-item"
                     data-id="${cat.id}"
                     data-name="${cat.name}">
                    <div class="card-body">
                        <b>${cat.name}</b><br>
                    </div>
                </div>
            `;
                });

                $('#kategoriList').html(html || '<div class="text-muted">Tidak ada store</div>');
            });

        }

        function loadStores() {

            $('#storeList').html('<div class="text-center p-3">Loading...</div>');

            $.get('/products/stores', function(res) {

                let html = '';

                res.data.forEach(store => {
                    html += `
                <div class="card mb-2 cursor-pointer store-item"
                     data-id="${store.id}"
                     data-name="${store.name}">
                    <div class="card-body">
                        <b>${store.name}</b><br>
                        <small>${store.location ?? ''}</small>
                    </div>
                </div>
            `;
                });

                $('#storeList').html(html || '<div class="text-muted">Tidak ada store</div>');
            });

        }

        $('#storePickerModal').on('shown.bs.modal', function() {
            loadStores();
        });

        $('#categoryPickerModal').on('shown.bs.modal', function() {
            loadKategori();
        });

        $('#brandPickerModal').on('shown.bs.modal', function() {
            loadBrand();
        });

        // $('#storeSearch').on('input', function() {
        //     loadStores($(this).val());
        // });

        $(document).on('click', '.store-item', function() {

            let id = $(this).data('id');
            let name = $(this).data('name');

            if (modal === 'editProductModal') {
                $('#edit_storeId').val(id);
                $('#edit_storeName').val(name);
            } else {
                $('#p_storeId').val(id);
                $('#p_storeName').val(name);
            }

            let picker = bootstrap.Modal.getInstance(
                document.getElementById('storePickerModal')
            );

            picker.hide();

            // buka lagi modal tambah produk
            setTimeout(() => {
                let addModal = new bootstrap.Modal(
                    document.getElementById(modal)
                );
                addModal.show();
            }, 200);

        });

        $(document).on('click', '.cat-item', function() {

            let id = $(this).data('id');
            let name = $(this).data('name');

            if (modal === 'editProductModal') {
                $('#p_editCategoryId').val(id);
                $('#p_editCategory').val(name);
            } else {
                $('#p_categoryId').val(id);
                $('#p_category').val(name);
            }

            let picker = bootstrap.Modal.getInstance(
                document.getElementById('categoryPickerModal')
            );

            picker.hide();

            // buka lagi modal tambah produk
            setTimeout(() => {
                let addModal = new bootstrap.Modal(
                    document.getElementById(modal)
                );
                addModal.show();
            }, 200);

        });

        $(document).on('click', '.brand-item', function() {

            let id = $(this).data('id');
            let name = $(this).data('name');

            if (modal === 'editProductModal') {
                $('#p_editBrandId').val(id);
                $('#p_editBrand').val(name);
            } else {
                $('#p_brandId').val(id);
                $('#p_brand').val(name);
            }

            let picker = bootstrap.Modal.getInstance(
                document.getElementById('brandPickerModal')
            );

            picker.hide();

            // buka lagi modal tambah produk
            setTimeout(() => {
                let addModal = new bootstrap.Modal(
                    document.getElementById(modal)
                );
                addModal.show();
            }, 200);

        });

        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            let formData = new FormData();

            let isAllStores = $('#p_isAllStores').is(':checked');

            formData.append('isAllStores', isAllStores);

            if (!isAllStores) {
                let storeId = $('#p_storeId').val();

                if (!storeId) {
                    alert('Pilih minimal satu toko');
                    btn.prop('disabled', false).text('Simpan Produk');
                    return;
                }

                formData.append('storeId', storeId)
            }

            // TEXT DATA
            formData.append('sku', $('#p_sku').val());
            formData.append('name', $('#p_name').val());
            formData.append('description', $('#p_description').val());
            formData.append('categoryId', $('#p_categoryId').val());
            formData.append('brandId', $('#p_brandId').val());
            formData.append('price', cleanNumber($('#p_price').val()));
            formData.append('promoPrice', cleanNumber($('#p_promoPrice').val()));
            formData.append('stock', $('#p_stock').val());
            // formData.append('version', $('#p_version').val());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            // MULTIPLE PHOTOS
            selectedFiles.forEach(file => {
                formData.append('photos', file);
            });

            $.ajax({
                url: '/products',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Simpan Produk');

                    $('#addProductAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.message || 'Gagal menyimpan produk'}
                </div>
            `);
                }
            });
        });

        function deleteProduct(id, btn) {
            event.stopPropagation();

            Swal.fire({
                title: 'Yakin ingin menghapus produk?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/products/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire('Terhapus!', res.message, 'success');
                            $(btn).closest('.col-md-3').remove();
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON?.server || 'Gagal menghapus produk',
                                'error');
                        }
                    });
                }
            });
        }
    </script>

    <script>
        $(document).on('click', '.product-card', function() {
            let p = $(this).data('product');

            // Informasi dasar
            $('#previewTitle').text(p.name || 'Produk');

            // Brand dan Kategori
            $('#previewBrand').text(p.brand?.name || 'Tanpa Brand');
            $('#previewCategory').text(p.category?.name || 'Tanpa Kategori');

            // Informasi Toko
            let stores = p.store || [];
            let isAllStores = p.isAllStores || false;
            let storeHtml = '';

            if (isAllStores) {
                storeHtml = '<span class="badge bg-success">Semua Toko</span>';
            } else {
                storeHtml = `<span class="badge bg-light text-dark">${stores.name || 'Toko'}</span>`;
            } 
            $('#previewStore').html(storeHtml);

            // Deskripsi
            $('#previewDescription').text(p.description || 'Tidak ada deskripsi');

            // Harga
            let price = p.price || 0;
            let promoPrice = p.promoPrice || 0;

            $('#previewPrice').text('Rp ' + Number(price).toLocaleString('id-ID'));

            if (promoPrice && promoPrice < price) {
                $('#previewPromo').html(`
                Rp ${Number(promoPrice).toLocaleString('id-ID')}
                <small class="text-muted text-decoration-line-through d-block">
                    Rp ${Number(price).toLocaleString('id-ID')}
                </small>
            `);
            } else {
                $('#previewPromo').text(promoPrice ?
                    'Rp ' + Number(promoPrice).toLocaleString('id-ID') :
                    'Tidak ada promo'
                );
            }

            // Stok
            $('#previewStock').text(p.stock !== undefined ? p.stock + ' unit' : 'Tidak tersedia');

            // Foto carousel
            let photos = p.photos || [];
            let html = '';

            if (photos.length === 0) {
                html = `
                <div class="carousel-item active">
                    <img src="https://via.placeholder.com/600x350?text=Tidak+Ada+Foto" 
                         class="d-block w-100"
                         style="height:350px; object-fit:cover;">
                </div>
            `;
            } else {
                photos.forEach((ph, i) => {
                    html += `
                    <div class="carousel-item ${i === 0 ? 'active' : ''}">
                        <img src="${ph.photoUrl || 'https://via.placeholder.com/600x350'}" 
                             class="d-block w-100"
                             style="height:350px; object-fit:cover;"
                             alt="Foto produk ${i+1}">
                    </div>
                `;
                });
            }

            $('#previewPhotos').html(html);

            // Tampilkan modal
            new bootstrap.Modal(document.getElementById('productPreviewModal')).show();
        });
    </script>

    {{-- EDIT PRODUK --}}
    <script>
        let shouldReload = false;
        let newFiles = [];

        $(document).on('click', '.edit-produk', function(e) {
            e.stopPropagation();
            let id = $(this).data('id');

            $.get(`/products/${id}`, function(res) {

                let p = res.data.data;

                $('#edit_product_id').val(p.id);

                if (p.isAllStores) {
                    $('#edit_isAllStores').prop('checked', true);
                    $('#edit_storeName').val('Semua Toko');
                    $('#edit_storeName').prop('disabled', true);
                    $('#edit-select-store').css('display', 'none');
                } else {
                    $('#edit_isAllStores').prop('checked', false);

                    if (p.stores && p.stores.length > 0) {
                        let names = p.stores.map(s => s.name).join(', ');
                        $('#edit_storeName').val(names);
                    }
                }

                $('#edit_sku').val(p.sku);
                $('#edit_name').val(p.name);
                $('#edit_description').val(p.description);
                $('#p_editCategoryId').val(p.category?.id || '');
                $('#p_editCategory').val(p.category?.name || '');
                $('#p_editBrandId').val(p.brand?.id || '');
                $('#p_editBrand').val(p.brand?.name || '');
                $('#edit_price').val(p.price);
                $('#edit_promoPrice').val(p.promoPrice);
                $('#edit_stock').val(p.stock);
                // $('#edit_version').val(p.version);

                renderExistingPhotos(p.photos || []);
                newFiles = [];
                $('#newPhotoPreview').html('');

                let modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                modal.show();

            }).fail(function(err) {
                console.error(err);
                Swal.fire('Error', 'Gagal mengambil data produk', 'error');
            });

        });

        function renderExistingPhotos(photos) {
            let container = $('#existingPhotos');
            container.html('');

            photos.forEach(photo => {
                container.append(`
            <div class="photo-item" data-id="${photo.id}" style="position:relative">
                <img src="${photo.photoUrl}"
                     style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">

                <button type="button"
                    onclick="deletePhoto('${photo.id}', event)"
                    style="
                        position:absolute;
                        top:-6px;
                        right:-6px;
                        background:red;
                        color:white;
                        border:none;
                        border-radius:50%;
                        width:22px;
                        height:22px;
                        font-size:12px;">
                    ×
                </button>
            </div>
        `);
            });
        }

        function deletePhoto(photoId, event) {
            event.preventDefault();

            if (!confirm('Hapus foto ini?')) return;

            let el = $(`.photo-item[data-id="${photoId}"]`);

            $.ajax({
                url: `/products/photos/${photoId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    el.addClass('photo-remove');

                    setTimeout(() => {
                        el.remove();
                    }, 350);
                },
                error: function() {
                    Swal.fire('Error', 'Gagal hapus foto', 'error');
                }
            });
        }

        $('#newPhotos').on('change', function() {
            let files = Array.from(this.files);

            newFiles = newFiles.concat(files);
            renderNewPreview();

            this.value = '';
        });

        function renderNewPreview() {
            let preview = $('#newPhotoPreview');
            preview.html('');

            newFiles.forEach((file, i) => {
                let reader = new FileReader();

                reader.onload = e => {
                    preview.append(`
                <div style="position:relative">
                    <img src="${e.target.result}"
                         style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">

                    <button type="button" onclick="removeNewPhoto(${i})"
                        style="
                            position:absolute;
                            top:-6px;
                            right:-6px;
                            background:red;
                            color:white;
                            border:none;
                            border-radius:50%;
                            width:22px;
                            height:22px;">
                        ×
                    </button>
                </div>
            `);
                };

                reader.readAsDataURL(file);
            });
        }

        function removeNewPhoto(i) {
            newFiles.splice(i, 1);
            renderNewPreview();
        }

        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $('#editProductForm button[type="submit"]');
            btn.prop('disabled', true).text('Updating...');

            let id = $('#edit_product_id').val();
            let isAllStores = $('#edit_isAllStores').is(':checked');

            let payload = {
                isAllStores: isAllStores,
                sku: $('#edit_sku').val(),
                name: $('#edit_name').val(),
                description: $('#edit_description').val(),
                categoryId: $('#p_editCategoryId').val(),
                brandId: $('#p_editBrandId').val(),
                price: cleanNumber($('#edit_price').val()),
                promoPrice: cleanNumber($('#edit_promoPrice').val()),
                stock: $('#edit_stock').val(),
                // version: $('#edit_version').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            if (!isAllStores) {
                payload.storeId = $("#edit_storeId").val();
            }

            $.ajax({
                url: `/products/${id}`,
                method: 'PATCH',
                data: payload,
                success: function() {

                    let afterSuccess = () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Produk berhasil diupdate!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => location.reload(), 2000);
                    };

                    if (newFiles.length > 0) {
                        let fd = new FormData();
                        newFiles.forEach(f => fd.append('photos[]', f));
                        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));

                        $.ajax({
                            url: `/products/${id}/photos`,
                            method: 'POST',
                            data: fd,
                            processData: false,
                            contentType: false,
                            success: afterSuccess,
                            error: () => Swal.fire('Error', 'Upload foto gagal', 'error')
                        });
                    } else {
                        afterSuccess();
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Update Produk');
                    Swal.fire('Error', xhr.responseJSON?.server || 'Update produk gagal', 'error');
                }
            });
        });
    </script>
@endsection
