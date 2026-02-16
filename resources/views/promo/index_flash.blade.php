@extends('layouts.template')

@section('own_style')
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">
                    <i class="fa fa-plus"></i> Tambah Promo
                </button>
            </div>
        </div>

        <div class="row g-4">
            @forelse ($promos as $promo)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm promo-card" style="cursor:pointer"
                        onclick="editPromo('{{ $promo['id'] }}')">

                        {{-- Banner --}}
                        @if (!empty($promo['bannerUrl']))
                            <img src="{{ $promo['bannerUrl'] }}" class="card-img-top"
                                style="height:200px;object-fit:cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                <span class="text-muted">No Banner</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">

                            {{-- Title --}}
                            <h5 class="card-title">{{ $promo['name'] }}</h5>

                            {{-- Description --}}
                            <p class="card-text text-muted small">
                                {{ $promo['description'] ?? '-' }}
                            </p>

                            {{-- Date --}}
                            <div class="mt-2">
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($promo['startDate'])->format('d M Y H:i') }}
                                    —
                                    {{ \Carbon\Carbon::parse($promo['endDate'])->format('d M Y H:i') }}
                                </small>
                            </div>

                            <div class="mt-auto"></div>

                            {{-- Buttons --}}
                            <button class="btn btn-outline-primary mt-3 w-100" data-bs-toggle="modal"
                                data-bs-target="#previewPromoModal"
                                onclick='event.stopPropagation(); previewPromo(@json($promo))'>
                                Preview
                            </button>

                            <button class="btn btn-outline-danger mt-2 w-100"
                                onclick="deletePromo('{{ $promo['id'] }}', this)">
                                Hapus
                            </button>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada Promo Flash
                    </div>
                </div>
            @endforelse



        </div>


    </div>

    <div class="modal fade" id="addPromoModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Tambah Promo</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addPromoForm">

                    <div class="modal-body">

                        <!-- STORE -->
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <div class="input-group">
                                <input type="text" id="p_storeName" placeholder="Pilih Toko" class="form-control"
                                    readonly>
                                <input type="hidden" id="p_storeId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#storePickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">Nama Promo</label>
                            <input class="form-control" id="promo_name" placeholder="Masukkan nama promo">
                        </div>

                        <!-- DESC -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="promo_description" placeholder="Masukkan deskripsi promo"></textarea>
                        </div>

                        <!-- BANNER -->
                        <div class="mb-3">
                            <label class="form-label">Banner Promo</label>
                            <input type="file" class="form-control" id="promo_banner" accept="image/*">

                            <!-- PREVIEW -->
                            <div id="bannerPreview" style="margin-top:12px; display:none;">
                                <img id="bannerPreviewImg"
                                    style="width:100%; max-height:220px; object-fit:cover; border-radius:10px; border:1px solid #ddd;">
                            </div>
                        </div>

                        <!-- DATE -->
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" class="form-control" id="promo_startDate">
                            </div>

                            <div class="col mb-3">
                                <label class="form-label">Tanggal Berakhir</label>
                                <input type="datetime-local" class="form-control" id="promo_endDate">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">
                            Simpan Promo
                        </button>
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

    <div class="modal fade" id="previewPromoModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="previewTitle"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">

                    <img id="previewBanner" class="img-fluid mb-3">

                    <p id="previewDesc"></p>

                    <div id="previewDiscount" class="fw-bold"></div>

                    <small id="previewDate" class="text-muted"></small>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editPromoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Promo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editPromoForm">
                    <input type="hidden" id="edit_id">

                    <div class="modal-body">

                        <!-- STORE -->
                        <div class="mb-3" style="display: none">
                            <label class="form-label">Store ID</label>
                            <input type="text" class="form-control" id="edit_storeId" placeholder="Pilih Toko"
                                readonly>
                        </div>

                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">Nama Promo</label>
                            <input type="text" class="form-control" id="edit_promo_name"
                                placeholder="Masukkan nama promo">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_promo_description" placeholder="Masukkan deskripsi promo"></textarea>
                        </div>

                        <!-- BANNER URL -->
                        <div class="mb-3">
                            <label class="form-label">Banner Promo</label>

                            <input type="file" class="form-control" id="edit_promo_banner" accept="image/*">

                            <!-- preview -->
                            <div id="editBannerPreviewBox" style="margin-top:12px;">
                                <img id="edit_banner_preview" class="img-fluid"
                                    style="max-height:200px;border-radius:10px;border:1px solid #ddd;">
                            </div>
                        </div>

                        <!-- DATE -->
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" class="form-control" id="edit_promo_startDate">
                            </div>
                            <div class="col mb-3">
                                <label class="form-label">Tanggal Berakhir</label>
                                <input type="datetime-local" class="form-control" id="edit_promo_endDate">
                            </div>
                        </div>

                        <div id="promoAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary w-100">Update Promo</button>
                    </div>

                </form>

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

        $('#promo_banner').on('change', function() {
            let file = this.files[0];

            if (!file) return;

            let reader = new FileReader();

            reader.onload = function(e) {
                $('#bannerPreviewImg').attr('src', e.target.result);
                $('#bannerPreview').fadeIn(200);
            };

            reader.readAsDataURL(file);
        });

        $('#addPromoForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData();

            formData.append('storeId', $('#p_storeId').val());
            formData.append('name', $('#promo_name').val());
            formData.append('description', $('#promo_description').val());
            formData.append('startDate', new Date($('#promo_startDate').val()).toISOString());
            formData.append('endDate', new Date($('#promo_endDate').val()).toISOString());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            let banner = $('#promo_banner')[0].files[0];
            if (banner) {
                formData.append('banner', banner);
            }

            $.ajax({
                url: '/promo-flash',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },

                error: function(xhr) {
                    $('#promoAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal simpan promo'}
                </div>
            `);
                    btn.prop('disabled', false).text('Simpan Promo');
                }
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
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
                    document.getElementById('addPromoModal') // ✅ FIX ID
                );
                addModal.show();
            }, 200);

        });
    </script>

    <script>
        function previewPromo(promo) {
            console.log(promo);
            // TITLE
            document.getElementById('previewTitle').innerText =
                promo.name || 'Tanpa Judul';

            // DESCRIPTION
            document.getElementById('previewDesc').innerText =
                promo.description || '-';

            // DISCOUNT (optional)
            let discountText = '-';

            if (promo.discountType && promo.discountValue != null) {
                discountText =
                    promo.discountType === 'PERCENTAGE' ?
                    `Diskon ${promo.discountValue}%` :
                    `Diskon Rp ${Number(promo.discountValue).toLocaleString('id-ID')}`;
            }

            document.getElementById('previewDiscount').innerText = discountText;

            // DATE FORMAT
            let start = promo.startDate ?
                new Date(promo.startDate).toLocaleString('id-ID') :
                '-';

            let end = promo.endDate ?
                new Date(promo.endDate).toLocaleString('id-ID') :
                '-';

            document.getElementById('previewDate').innerText =
                `${start} — ${end}`;

            // BANNER
            let banner = document.getElementById('previewBanner');

            if (promo.bannerUrl) {
                banner.src = promo.bannerUrl;
                banner.style.display = 'block';
            } else {
                banner.style.display = 'none';
            }

            // SHOW MODAL
            $('#previewPromoModal').modal('show');
        }


        $('#edit_promo_banner').on('change', function() {
            let file = this.files[0];
            if (!file) return;

            let reader = new FileReader();

            reader.onload = function(e) {
                $('#edit_banner_preview')
                    .attr('src', e.target.result)
                    .hide()
                    .fadeIn(200);
            };

            reader.readAsDataURL(file);
        });

        function editPromo(id) {
            $.get(`/promo-flash/${id}`, function(res) {
                let p = res.data;

                $('#edit_id').val(id);
                $('#edit_storeId').val(p.storeId);
                $('#edit_promo_name').val(p.name);
                $('#edit_promo_description').val(p.description);

                $('#edit_banner_preview').attr('src', p.bannerUrl || '');

                $('#edit_promo_startDate').val(p.startDate.slice(0, 16));
                $('#edit_promo_endDate').val(p.endDate.slice(0, 16));

                $('#editPromoModal').modal('show');
            });
        }

        // Submit form update
        $('#editPromoForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            let fd = new FormData();

            fd.append('storeId', $('#edit_storeId').val());
            fd.append('name', $('#edit_promo_name').val());
            fd.append('description', $('#edit_promo_description').val());
            fd.append('startDate', new Date($('#edit_promo_startDate').val()).toISOString());
            fd.append('endDate', new Date($('#edit_promo_endDate').val()).toISOString());
            fd.append('_method', 'PATCH');
            fd.append('_token', $('meta[name="csrf-token"]').attr('content'));

            let banner = $('#edit_promo_banner')[0].files[0];
            if (banner) {
                fd.append('banner', banner);
            }

            $.ajax({
                url: `/promo-flash/${$('#edit_id').val()}`,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    $('#promoAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal update promo'}
                </div>
            `);
                    btn.prop('disabled', false).text('Update Promo');
                }
            });
        });

        function deletePromo(id, btn) {
            event.stopPropagation(); // supaya card tidak ikut ter-klik

            Swal.fire({
                title: 'Yakin ingin menghapus promo?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/promo-flash/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire('Terhapus!', res.message, 'success');

                            // Hapus card dari DOM
                            $(btn).closest('.col-md-4').remove();
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON?.server || 'Gagal menghapus promo',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
