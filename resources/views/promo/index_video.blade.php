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
                        onclick="editVideoPromo('{{ $promo['id'] }}')">

                        {{-- Banner --}}
                        @if (!empty($promo['thumbnailUrl']))
                            <img src="{{ $promo['thumbnailUrl'] }}" class="card-img-top"
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
                    <h5>Tambah Promo Video</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addPromoForm">

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Nama Promo</label>
                            <input class="form-control" id="promo_name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="promo_description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Video URL</label>
                            <input class="form-control" id="promo_videoUrl" placeholder="https://youtube.com/watch?v=xxxx">

                            <div id="videoPreview" class="mt-3 d-none text-center">
                                <img id="ytThumbnail" class="img-fluid rounded shadow" style="max-height:250px;">
                            </div>

                        </div>

                        <div id="promoAlert"></div>

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

                    <div class="text-center mt-2">
                        <img id="fallbackThumb" class="img-fluid rounded shadow" style="cursor:pointer;">
                        <div class="fw-bold mt-1">▶ Tonton di YouTube</div>
                    </div>

                    <p id="previewDesc"></p>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="editVideoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Promo Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editVideoForm">
                    <input type="hidden" id="edit_video_id">

                    <div class="modal-body">

                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">Nama Promo</label>
                            <input type="text" class="form-control" id="edit_video_name">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_video_description"></textarea>
                        </div>

                        <!-- VIDEO URL -->
                        <div class="mb-3">
                            <label class="form-label">URL Video</label>
                            <input type="text" class="form-control" id="edit_video_url"
                                placeholder="https://youtube.com/...">
                        </div>

                        <!-- THUMBNAIL PREVIEW -->
                        <div class="mb-3 text-center">
                            <img id="edit_video_thumbnail" class="img-fluid rounded shadow d-none"
                                style="max-height:200px;">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            Update Video Promo
                        </button>
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

        function extractYouTubeID(url) {
            if (!url) return null;

            const regExp =
                /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;

            const match = url.match(regExp);

            return (match && match[2].length === 11) ?
                match[2] :
                null;
        }

        function getYoutubeThumbnail(url) {
            const reg = /(?:youtube\.com.*(?:\?|&)v=|youtu\.be\/)([^&#]+)/;
            const match = url.match(reg);
            return match ? `https://img.youtube.com/vi/${match[1]}/hqdefault.jpg` : null;
        }

        function updateThumbnailPreview(url) {
            const thumb = getYoutubeThumbnail(url);

            if (thumb) {
                $('#edit_video_thumbnail')
                    .attr('src', thumb)
                    .removeClass('d-none');
            } else {
                $('#edit_video_thumbnail')
                    .addClass('d-none');
            }
        }

        $('#edit_video_url').on('input', function() {
            updateThumbnailPreview($(this).val());
        });


        let generatedThumbnail = null;
        $('#promo_videoUrl').on('input', function() {
            const url = $(this).val().trim();
            const videoId = extractYouTubeID(url);

            if (videoId) {
                generatedThumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;

                $('#ytThumbnail').attr('src', generatedThumbnail);
                $('#videoPreview').removeClass('d-none');
            } else {
                generatedThumbnail = null;
                $('#videoPreview').addClass('d-none');
            }
        });


        $('#addPromoForm').on('submit', function(e) {
            e.preventDefault();

            let payload = {
                name: $('#promo_name').val(),
                description: $('#promo_description').val(),
                type: 'VIDEO',
                videoUrl: $('#promo_videoUrl').val(),
                thumbnailUrl: generatedThumbnail
            };

            $.ajax({
                url: '/promo-video',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

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

    <script>
        function previewPromo(promo) {

            document.getElementById('previewTitle').innerText = promo.name;
            document.getElementById('previewDesc').innerText = promo.description ?? '-';

            const videoId = extractYouTubeID(promo.videoUrl);

            if (!videoId) return;

            const embedUrl =
                `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&mute=1`;

            const thumb =
                `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;

            $('#previewVideo').attr('src', embedUrl);
            $('#previewVideoWrapper').removeClass('d-none');

            // fallback thumbnail click
            $('#fallbackThumb')
                .attr('src', thumb)
                .off('click')
                .on('click', () => window.open(promo.videoUrl, '_blank'));
        }


        $('#previewPromoModal').on('hidden.bs.modal', function() {
            $('#previewVideo').attr('src', '');
        });

        function editVideoPromo(id) {
            $.get(`/promo-video/${id}`, function(res) {

                let p = res.data;

                $('#edit_video_id').val(id);
                $('#edit_video_name').val(p.name ?? '');
                $('#edit_video_description').val(p.description ?? '');
                $('#edit_video_url').val(p.videoUrl ?? '');

                updateThumbnailPreview(p.videoUrl);

                $('#editVideoModal').modal('show');
            });
        }


        // Preview banner saat link diubah
        $('#edit_promo_bannerUrl').on('input', function() {
            let url = $(this).val();
            $('#edit_banner_preview').attr('src', url);
        });

        // Submit form update
        $('#editVideoForm').on('submit', function(e) {
            e.preventDefault();

            const url = $('#edit_video_url').val();
            const thumbnail = getYoutubeThumbnail(url);

            $.ajax({
                url: `/promo-video/${$('#edit_video_id').val()}`,
                method: 'patch',
                data: {
                    name: $('#edit_video_name').val(),
                    description: $('#edit_video_description').val(),
                    videoUrl: url,
                    thumbnail: thumbnail,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    $('#promoAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal update video promo'}
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
