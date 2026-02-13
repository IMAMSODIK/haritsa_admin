@extends('layouts.template')

@section('own_style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .promo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
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
                <button class="btn btn-primary add-promo" data-bs-toggle="modal" data-bs-target="#addPromoModal">
                    <i class="fa fa-plus"></i> Tambah Promo
                </button>
            </div>
        </div>

        <div class="row g-4">

            @forelse ($promos as $promo)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm promo-card border-0" style="cursor:pointer; transition:.2s;"
                        onclick="editPromo('{{ $promo['id'] }}')">

                        {{-- Banner --}}
                        <div class="position-relative">
                            @if (!empty($promo['bannerUrl']))
                                <img src="{{ $promo['bannerUrl'] }}" class="card-img-top"
                                    style="height:200px;object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height:200px;">
                                    <span class="text-muted">No Banner</span>
                                </div>
                            @endif

                            {{-- Age Badge --}}
                            <span class="badge bg-dark position-absolute top-0 end-0 m-2">
                                {{ $promo['minAge'] }}–{{ $promo['maxAge'] }} th
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">

                            {{-- Title --}}
                            <h5 class="card-title mb-1">{{ $promo['name'] }}</h5>

                            {{-- Description --}}
                            <p class="card-text text-muted small mb-2">
                                {{ $promo['description'] ?? '-' }}
                            </p>

                            {{-- Date --}}
                            <small class="text-muted mb-3">
                                📅 {{ \Carbon\Carbon::parse($promo['startDate'])->format('d M Y H:i') }}
                                —
                                {{ \Carbon\Carbon::parse($promo['endDate'])->format('d M Y H:i') }}
                            </small>

                            {{-- Terms --}}
                            <div class="bg-light rounded p-2 mb-2">
                                <small class="fw-semibold d-block text-dark">Syarat & Ketentuan</small>
                                <small class="text-muted">
                                    Berlaku untuk anak usia {{ $promo['minAge'] }}–{{ $promo['maxAge'] }} tahun
                                </small>
                            </div>

                            <div class="mt-auto"></div>

                            <button class="btn btn-outline-primary mt-3 w-100" data-bs-toggle="modal"
                                data-bs-target="#previewPromoModal"
                                onclick='event.stopPropagation(); previewPromo(@json($promo))'> Preview
                            </button> <button class="btn btn-outline-danger mt-2 w-100"
                                onclick="deletePromo('{{ $promo['id'] }}', this)"> Hapus </button>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada Promo Customer
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
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <div class="input-group">
                                <input type="text" id="p_storeName" class="form-control" placeholder="Pilih toko" readonly>
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
                            <textarea class="form-control" id="promo_description" placeholder="Masukkan deksripsi promo"></textarea>
                        </div>

                        <!-- DISCOUNT -->
                        <div class="mb-3">
                            <label class="form-label">Age Range</label>
                            <div id="ageRange"></div>

                            <div class="mt-2">
                                <span id="ageValue"></span>
                            </div>

                            <!-- hidden field -->
                            <input type="hidden" id="minAge" name="minAge">
                            <input type="hidden" id="maxAge" name="maxAge">
                        </div>


                        <!-- BANNER -->
                        <div class="mb-3">
                            <label class="form-label">Banner Promo</label>
                            <input class="form-control" id="promo_bannerUrl" placeholder="https://link-banner.jpg">
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

    <div class="modal fade" id="previewPromoModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">

                <div class="modal-header bg-light">
                    <h5 id="previewTitle" class="fw-bold mb-0"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <img id="previewBanner" class="img-fluid rounded mb-3 d-none"
                        style="object-fit:cover; width:100%;">

                    <p id="previewDesc" class="text-muted"></p>

                    <div class="border rounded p-3 bg-light">

                        <div id="previewAge" class="small text-muted mb-1"></div>

                        <small id="previewDate" class="text-muted"></small>

                    </div>

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
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <div class="input-group">
                                <input type="text" id="edit_p_storeName" placeholder="Pilih Toko" class="form-control" readonly>
                                <input type="hidden" id="edit_p_storeId">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#storePickerModal">
                                    Pilih
                                </button>
                            </div>
                        </div>

                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">Nama Promo</label>
                            <input type="text" class="form-control" placeholder="Masukkan nama promo" id="edit_promo_name">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_promo_description" placeholder="Masukkan deskripsi promo"></textarea>
                        </div>

                        <!-- DISCOUNT -->
                        <div class="mb-3">
                            <label class="form-label">Age Range</label>
                            <div id="ageRangeEdit"></div>

                            <div class="mt-2">
                                <span id="ageValueEdit"></span>
                            </div>

                            <!-- hidden field -->
                            <input type="hidden" id="minAgeEdit" name="minAge">
                            <input type="hidden" id="maxAgeEdit" name="maxAge">
                        </div>

                        <!-- BANNER URL -->
                        <div class="mb-3">
                            <label class="form-label">Banner URL</label>
                            <input type="text" class="form-control" id="edit_promo_bannerUrl"
                                placeholder="https://link-banner.jpg">
                            <img id="edit_banner_preview" class="img-fluid mt-2" style="max-height:200px;" />
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
        let modal = "addPromoModal";

        $(".add-promo").on("click", function() {
            modal = "addPromoModal";
        });

        $(".promo-card").on("click", function() {
            modal = "editPromoModal";
        });
    </script>
    <script>
        var slider = document.getElementById('ageRange');
        var slider2 = document.getElementById('ageRangeEdit');

        noUiSlider.create(slider, {
            start: [0, 18],
            connect: true,
            range: {
                min: 0,
                max: 18
            },
            step: 1
        });

        slider.noUiSlider.on('update', function(values) {
            let min = Math.round(values[0]);
            let max = Math.round(values[1]);

            document.getElementById('ageValue').innerText =
                min + ' - ' + max + ' tahun';

            $('#minAge').val(min);
            $('#maxAge').val(max);
        });


        noUiSlider.create(slider2, {
            start: [0, 18],
            connect: true,
            range: {
                min: 0,
                max: 18
            },
            step: 1
        });

        slider2.noUiSlider.on('update', function(values) {
            let min = Math.round(values[0]);
            let max = Math.round(values[1]);

            document.getElementById('ageValueEdit').innerText =
                min + ' - ' + max + ' tahun';

            $('#minAgeEdit').val(min);
            $('#maxAgeEdit').val(max);
        });
    </script>

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
        // $('#promo_banner').on('change', function(e) {
        //     let file = e.target.files[0];
        //     if (!file) return;

        //     let reader = new FileReader();
        //     reader.onload = function(ev) {
        //         $('#promo_preview')
        //             .attr('src', ev.target.result)
        //             .removeClass('d-none');
        //     };
        //     reader.readAsDataURL(file);
        // });

        $('#addPromoForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData();

            formData.append('storeId', $('#p_storeId').val());
            formData.append('name', $('#promo_name').val());
            formData.append('description', $('#promo_description').val());
            formData.append('startDate', new Date($('#promo_startDate').val()).toISOString());
            formData.append('endDate', new Date($('#promo_endDate').val()).toISOString());
            formData.append('bannerUrl', $('#promo_bannerUrl').val());
            formData.append('minAge', $('#minAge').val());
            formData.append('maxAge', $('#maxAge').val());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '/promo-customer',
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

            if (modal === 'editPromoModal') {
                $('#edit_p_storeId').val(id);
                $('#edit_p_storeName').val(name);
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
    </script>

    <script>
        function previewPromo(promo) {

            const safe = v => v ?? '-';

            document.getElementById('previewTitle').innerText = safe(promo.name);
            document.getElementById('previewDesc').innerText = safe(promo.description);

            // age
            document.getElementById('previewAge').innerText =
                `Usia ${promo.minAge ?? 0} - ${promo.maxAge ?? 18} tahun`;

            // date
            let start = new Date(promo.startDate);
            let end = new Date(promo.endDate);

            document.getElementById('previewDate').innerText =
                `${start.toLocaleString()} — ${end.toLocaleString()}`;

            // banner
            const img = document.getElementById('previewBanner');

            if (promo.bannerUrl) {
                img.src = promo.bannerUrl;
                img.classList.remove('d-none');
            } else {
                img.classList.add('d-none');
            }
        }


        function editPromo(id) {
            $.get(`/promo-customer/${id}`, function(res) {
                let p = res.data;

                $('#edit_id').val(id);
                $('#edit_p_storeId').val(p.store.id);
                $('#edit_p_storeName').val(p.store.name);
                $('#edit_promo_name').val(p.name);
                $('#edit_promo_description').val(p.description);

                $('#edit_promo_bannerUrl').val(p.bannerUrl);
                $('#edit_banner_preview').attr('src', p.bannerUrl || '');

                // fix datetime
                $('#edit_promo_startDate').val(p.startDate.slice(0, 16));
                $('#edit_promo_endDate').val(p.endDate.slice(0, 16));

                let minAge = p.minAge ?? 0;
                let maxAge = p.maxAge ?? 18;

                slider2.noUiSlider.set([minAge, maxAge]);

                $('#minAgeEdit').val(minAge);
                $('#maxAgeEdit').val(maxAge);
                $('#ageValueEdit').text(minAge + ' - ' + maxAge + ' tahun');

                $('#editPromoModal').modal('show');
            });
        }


        // Preview banner saat link diubah
        $('#edit_promo_bannerUrl').on('input', function() {
            let url = $(this).val();
            $('#edit_banner_preview').attr('src', url);
        });

        // Submit form update
        $('#editPromoForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: `/promo-customer/${$('#edit_id').val()}`,
                method: 'patch',
                data: {
                    storeId: $('#edit_p_storeId').val(),
                    name: $('#edit_promo_name').val(),
                    description: $('#edit_promo_description').val(),
                    bannerUrl: $('#edit_promo_bannerUrl').val(),
                    minAge: $('#minAgeEdit').val(),
                    maxAge: $('#maxAgeEdit').val(),
                    startDate: new Date($('#edit_promo_startDate').val()).toISOString(),
                    endDate: new Date($('#edit_promo_endDate').val()).toISOString(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
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
                }
            });
        });


        // Preview banner sebelum submit
        $('#edit_promo_banner').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#edit_banner_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // Submit form update
        $('#editPromoForm').on('submit', async function(e) {
            e.preventDefault();

            let bannerFile = $('#edit_promo_banner')[0].files[0];
            let bannerBase64 = $('#edit_banner_preview').attr('src') || '';

            if (bannerFile) {
                bannerBase64 = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = err => reject(err);
                    reader.readAsDataURL(bannerFile);
                });
            }

            $.ajax({
                url: `/promo-customer/${$('#edit_id').val()}`,
                method: 'PUT',
                data: {
                    storeId: $('#edit_storeId').val(),
                    name: $('#edit_promo_name').val(),
                    description: $('#edit_promo_description').val(),
                    discountType: $('#edit_promo_discountType').val(),
                    discountValue: $('#edit_promo_discountValue').val(),
                    minPurchase: cleanNumber($('#edit_promo_minPurchase').val()),
                    maxDiscount: cleanNumber($('#edit_promo_maxDiscount').val()),
                    startDate: new Date($('#edit_promo_startDate').val()).toISOString(),
                    endDate: new Date($('#edit_promo_endDate').val()).toISOString(),
                    banner: bannerBase64,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
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
                        url: `/promo-customer/${id}`,
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
