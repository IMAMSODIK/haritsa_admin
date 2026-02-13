@extends('layouts.template')

@section('own_style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
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

                            {{-- Purchase Limit --}}
                            {{-- <small class="text-muted">
                                Promo untuk anak usia {{ number_format($promo['minPurchase']) }} hingga {{ number_format($promo['maxDiscount']) }}
                            </small> --}}

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
                        Belum ada Promo Video
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
                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">Nama Promo</label>
                            <input class="form-control" id="promo_name">
                        </div>

                        <!-- DESC -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="promo_description"></textarea>
                        </div>

                        <!-- DISCOUNT -->
                        <div class="mb-3">
                            <label class="form-label">Age Range</label>
                            <div id="ageRange"></div>
                            <div class="mt-2">
                                <span id="ageValue"></span>
                            </div>
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
                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">Nama Promo</label>
                            <input type="text" class="form-control" id="edit_promo_name">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_promo_description"></textarea>
                        </div>

                        <!-- DISCOUNT -->
                        <div class="mb-3">
                            <label class="form-label">Age Range</label>
                            <div id="ageRangeEdit"></div>
                            <div class="mt-2">
                                <span id="ageValueEdit"></span>
                            </div>
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
@endsection

@section('own_script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        var slider = document.getElementById('ageRange');
        var slider2 = document.getElementById('ageRange2');

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
            document.getElementById('ageValue').innerText =
                Math.round(values[0]) + ' - ' + Math.round(values[1]) + ' tahun';
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
            document.getElementById('ageValue2').innerText =
                Math.round(values[0]) + ' - ' + Math.round(values[1]) + ' tahun';
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
            formData.append('discountType', $('#promo_discountType').val());
            formData.append('discountValue', $('#promo_discountValue').val());
            formData.append('minPurchase', cleanNumber($('#promo_minPurchase').val()));
            formData.append('maxDiscount', cleanNumber($('#promo_maxDiscount').val()));
            formData.append('startDate', new Date($('#promo_startDate').val()).toISOString());
            formData.append('endDate', new Date($('#promo_endDate').val()).toISOString());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('bannerUrl', $('#promo_bannerUrl').val());

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

            document.getElementById('previewTitle').innerText = promo.name;
            document.getElementById('previewDesc').innerText = promo.description ?? '-';

            let discount =
                promo.discountType === 'PERCENTAGE' ?
                `Diskon ${promo.discountValue}%` :
                `Diskon Rp ${promo.discountValue.toLocaleString()}`;

            document.getElementById('previewDiscount').innerText = discount;

            document.getElementById('previewDate').innerText =
                `${new Date(promo.startDate).toLocaleString()} - ${new Date(promo.endDate).toLocaleString()}`;

            if (promo.bannerUrl) {
                document.getElementById('previewBanner').src = promo.bannerUrl;
                document.getElementById('previewBanner').classList.remove('d-none');
            } else {
                document.getElementById('previewBanner').classList.add('d-none');
            }
        }

        function editPromo(id) {
            $.get(`/promo-customer/${id}`, function(res) {
                let p = res.data;

                $('#edit_id').val(id);
                $('#edit_storeId').val(p.storeId);
                $('#edit_promo_name').val(p.name);
                $('#edit_promo_description').val(p.description);
                $('#edit_promo_discountType').val(p.discountType);
                $('#edit_promo_discountValue').val(p.discountValue);
                $('#edit_promo_minPurchase').val(p.minPurchase);
                $('#edit_promo_maxDiscount').val(p.maxDiscount);

                $('#edit_promo_bannerUrl').val(p.bannerUrl);
                $('#edit_banner_preview').attr('src', p.bannerUrl || '');

                $('#edit_promo_startDate').val(p.startDate.slice(0, 16));
                $('#edit_promo_endDate').val(p.endDate.slice(0, 16));

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
                    storeId: $('#edit_storeId').val(),
                    name: $('#edit_promo_name').val(),
                    description: $('#edit_promo_description').val(),
                    discountType: $('#edit_promo_discountType').val(),
                    discountValue: $('#edit_promo_discountValue').val(),
                    minPurchase: cleanNumber($('#edit_promo_minPurchase').val()),
                    maxDiscount: cleanNumber($('#edit_promo_maxDiscount').val()),
                    startDate: new Date($('#edit_promo_startDate').val()).toISOString(),
                    endDate: new Date($('#edit_promo_endDate').val()).toISOString(),
                    bannerUrl: $('#edit_promo_bannerUrl').val(),
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
