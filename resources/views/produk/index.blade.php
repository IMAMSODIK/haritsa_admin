@extends('layouts.template')

@section('own_style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .cursor-pointer {
            cursor: pointer;
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                    <i class="fa fa-plus"></i> Tambah Produk
                </button>
            </div>
        </div>

        <div class="row g-4">

            @forelse($produks as $p)
                @php
                    $photo = $p['photos'][0]['photoUrl'] ?? 'https://via.placeholder.com/400';
                @endphp

                <div class="col-md-3">
                    <div class="card h-100 shadow-sm product-card" data-product='@json($p)'>
                        <img src="{{ $photo }}" class="card-img-top" style="height:200px; object-fit:cover;">

                        <div class="card-body d-flex flex-column">
                            <b>{{ $p['name'] }}</b><br>
                            <small class="text-muted">{{ $p['category'] ?? '-' }}</small>
                            <div class="mt-2 text-success fw-bold">
                                Rp {{ number_format($p['price'], 0, ',', '.') }}
                            </div>

                            <div class="mt-auto d-flex gap-2">
                                <button class="btn btn-outline-primary w-100"
                                    onclick="editProduct('{{ $p['id'] }}', this, event)" style="margin-bottom: 5px;">
                                    Edit
                                </button>
                                <button class="btn btn-outline-danger w-100"
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
                    <h5 id="previewTitle"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- carousel foto -->
                    <div id="previewCarousel" class="carousel slide mb-3">
                        <div class="carousel-inner" id="previewPhotos"></div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#previewCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>

                        <button class="carousel-control-next" type="button" data-bs-target="#previewCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>

                    <p id="previewDescription"></p>

                    <div class="row">
                        <div class="col">
                            <b>Harga</b><br>
                            <span id="previewPrice" class="text-success"></span>
                        </div>

                        <div class="col">
                            <b>Promo</b><br>
                            <span id="previewPromo"></span>
                        </div>

                        <div class="col">
                            <b>Stock</b><br>
                            <span id="previewStock"></span>
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

                        <!-- STORE -->
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <div class="input-group">
                                <input type="text" id="p_storeName" class="form-control" readonly
                                    placeholder="Pilih store">
                                <input type="hidden" id="p_storeId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#storePickerModal">
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
                            <input class="form-control" id="p_category" placeholder="Kategori produk">
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

                        <!-- VERSION -->
                        <div class="mb-3">
                            <label class="form-label">Versi Produk</label>
                            <input class="form-control" id="p_version" placeholder="v1.0">
                        </div>

                        <!-- FOTO URL -->
                        <div class="mb-3">
                            <label class="form-label">Foto Produk (pisahkan koma untuk multiple)</label>
                            <input type="text" id="p_photos" class="form-control"
                                placeholder="https://link1.jpg, https://link2.jpg">
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

                        <!-- STORE -->
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <div class="input-group">
                                <input type="text" id="edit_storeName" class="form-control" readonly>
                                <input type="hidden" id="edit_storeId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#storePickerModal">
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
                            <label class="form-label">Kategori</label>
                            <input class="form-control" id="edit_category">
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

                        <!-- Version -->
                        <div class="mb-3">
                            <label class="form-label">Versi Produk</label>
                            <input class="form-control" id="edit_version">
                        </div>

                        <!-- Foto URL -->
                        <div class="mb-3">
                            <label class="form-label">Foto Produk (pisahkan koma)</label>
                            <input type="text" id="edit_photos" class="form-control"
                                placeholder="https://link1.jpg, https://link2.jpg">
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
@endsection

@section('own_script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
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

        $('#p_photos').on('change', function() {

            let files = Array.from(this.files);

            if (files.length > 5) {
                alert('Maksimal 5 foto');
                this.value = '';
                $('#photoPreview').html('');
                return;
            }

            $('#photoPreview').html('');

            files.forEach(file => {
                let reader = new FileReader();
                reader.onload = e => {
                    $('#photoPreview').append(`
                <img src="${e.target.result}"
                     style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
            `);
                };
                reader.readAsDataURL(file);
            });

        });

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

        // $('#storeSearch').on('input', function() {
        //     loadStores($(this).val());
        // });

        $(document).on('click', '.store-item', function() {

            let id = $(this).data('id');
            let name = $(this).data('name');

            $('#p_storeId').val(id);
            $('#p_storeName').val(name);

            let picker = bootstrap.Modal.getInstance(
                document.getElementById('storePickerModal')
            );

            picker.hide();

            // buka lagi modal tambah produk
            setTimeout(() => {
                let addModal = new bootstrap.Modal(
                    document.getElementById('addStoreModal') // ✅ FIX ID
                );
                addModal.show();
            }, 200);

        });

        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            let photos = $('#p_photos').val()
                .split(',')
                .map(url => url.trim())
                .filter(url => url !== '');

            $.ajax({
                url: '/products',
                method: 'POST',
                data: {
                    storeId: $('#p_storeId').val(),
                    sku: $('#p_sku').val(),
                    name: $('#p_name').val(),
                    description: $('#p_description').val(),
                    category: $('#p_category').val(),
                    price: cleanNumber($('#p_price').val()),
                    promoPrice: cleanNumber($('#p_promoPrice').val()),
                    stock: $('#p_stock').val(),
                    version: $('#p_version').val(),
                    photos: photos, // array of URL strings
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Simpan Produk');
                    $('#addProductAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal menyimpan produk'}
                </div>
            `);
                }
            });
        });

        function editProduct(id, btn, event) {
            event.stopPropagation(); // sekarang aman
            $.get(`/products/${id}`, function(res) {
                let p = res.data.data;

                $('#edit_product_id').val(p.id);
                $('#edit_storeId').val(p.storeId);
                $('#edit_storeName').val(p.storeName || '');
                $('#edit_sku').val(p.sku);
                $('#edit_name').val(p.name);
                $('#edit_description').val(p.description);
                $('#edit_category').val(p.category);
                $('#edit_price').val(p.price);
                $('#edit_promoPrice').val(p.promoPrice);
                $('#edit_stock').val(p.stock);
                $('#edit_version').val(p.version);

                // Array of URLs => string pisahkan koma
                $('#edit_photos').val((p.photos || []).map(x => x.photoUrl).join(', '));

                $('#editProductModal').modal('show');
            }).fail(function() {
                Swal.fire('Error', 'Gagal mengambil data produk', 'error');
            });
        }


        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#edit_product_id').val();
            let photos = $('#edit_photos').val()
                .split(',')
                .map(url => url.trim())
                .filter(url => url !== '');

            $.ajax({
                url: `/products/${id}`,
                method: 'PATCH',
                data: {
                    storeId: $('#edit_storeId').val(),
                    sku: $('#edit_sku').val(),
                    name: $('#edit_name').val(),
                    description: $('#edit_description').val(),
                    category: $('#edit_category').val(),
                    price: cleanNumber($('#edit_price').val()),
                    promoPrice: cleanNumber($('#edit_promoPrice').val()),
                    stock: $('#edit_stock').val(),
                    version: $('#edit_version').val(),
                    photos: photos,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                },
                error: function(xhr) {
                    $('#editProductAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal update produk'}
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

            $('#previewTitle').text(p.name);
            $('#previewDescription').text(p.description || '-');

            $('#previewPrice').text(
                'Rp ' + Number(p.price).toLocaleString('id-ID')
            );

            $('#previewPromo').text(
                p.promoPrice ?
                'Rp ' + Number(p.promoPrice).toLocaleString('id-ID') :
                '-'
            );

            $('#previewStock').text(p.stock);

            // foto carousel
            let photos = p.photos || [];
            let html = '';

            if (photos.length === 0) {
                html = `
            <div class="carousel-item active">
                <img src="https://via.placeholder.com/600"
                     class="d-block w-100">
            </div>
        `;
            } else {
                photos.forEach((ph, i) => {
                    html += `
                <div class="carousel-item ${i === 0 ? 'active' : ''}">
                    <img src="${ph.photoUrl}"
                         class="d-block w-100"
                         style="height:350px; object-fit:cover;">
                </div>
            `;
                });
            }

            $('#previewPhotos').html(html);

            new bootstrap.Modal(
                document.getElementById('productPreviewModal')
            ).show();

        });
    </script>
@endsection
