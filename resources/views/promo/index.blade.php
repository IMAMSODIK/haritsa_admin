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
                        Belum ada Promo Reguler
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
                            <input class="form-control" id="promo_bannerUrl" placeholder="https://link-banner.jpg">
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
                            <input type="text" class="form-control" id="edit_promo_name" placeholder="Masukkan nama promo">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_promo_description" placeholder="Masukkan deskripsi promo"></textarea>
                        </div>

                        <!-- BANNER URL -->
                        <div class="mb-3">
                            <label class="form-label">Banner URL</label>
                            <input type="text" class="form-control" id="edit_promo_bannerUrl"
                                placeholder="https://link-banner.jpg">
                            <img id="edit_banner_preview" class="img-fluid mt-2" style="max-height:200px;" />
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

            formData.append('name', $('#promo_name').val());
            formData.append('description', $('#promo_description').val());
            formData.append('bannerUrl', $('#promo_bannerUrl').val());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '/promo-reguler',
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
        function previewPromo(promo) {

            document.getElementById('previewTitle').innerText = promo.name;
            document.getElementById('previewDesc').innerText = promo.description ?? '-';

            if (promo.bannerUrl) {
                document.getElementById('previewBanner').src = promo.bannerUrl;
                document.getElementById('previewBanner').classList.remove('d-none');
            } else {
                document.getElementById('previewBanner').classList.add('d-none');
            }
        }

        function editPromo(id) {
            $.get(`/promo-reguler/${id}`, function(res) {
                let p = res.data;

                $('#edit_id').val(id);
                $('#edit_promo_name').val(p.name);
                $('#edit_promo_description').val(p.description);
                
                $('#edit_promo_bannerUrl').val(p.bannerUrl);
                $('#edit_banner_preview').attr('src', p.bannerUrl || '');

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
                url: `/promo-reguler/${$('#edit_id').val()}`,
                method: 'patch',
                data: {
                    name: $('#edit_promo_name').val(),
                    description: $('#edit_promo_description').val(),
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
                        url: `/promo-reguler/${id}`,
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
