@extends('layouts.template')

@section('own_style')
    <style>
        .podcast-card {
            transition: all .2s ease;
        }

        .podcast-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPodcastModal">
                    <i class="fa fa-plus"></i> Tambah Podcast
                </button>
            </div>
        </div>

        <div class="row g-4">

            @forelse ($podcasts as $podcast)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 podcast-card" style="cursor:pointer"
                        onclick="editPodcast('{{ $podcast['id'] }}')">

                        {{-- Thumbnail --}}
                        <div class="position-relative">
                            @if (!empty($podcast['thumbnail']))
                                <img src="{{ $podcast['thumbnail'] }}" class="card-img-top"
                                    style="height:200px;object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height:200px;">
                                    <span class="text-muted">No Thumbnail</span>
                                </div>
                            @endif

                            {{-- Badge youtube --}}
                            @if ($podcast['videoUrl'])
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    YouTube
                                </span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">

                            {{-- Title --}}
                            <h5 class="card-title mb-1">
                                {{ $podcast['title'] }}
                            </h5>

                            {{-- Description --}}
                            <p class="text-muted small mb-2">
                                {{ $podcast['description'] ?? '-' }}
                            </p>

                            {{-- Moderator --}}
                            <div class="small text-muted mb-2">
                                <i class="fa fa-microphone"></i>
                                {{ $podcast['moderator'] ?? '-' }}
                            </div>

                            {{-- Created --}}
                            <div class="small text-muted mb-3">
                                Dibuat {{ date('d M Y', strtotime($podcast['createdAt'])) }}
                            </div>

                            <div class="mt-auto"></div>

                            {{-- Buttons --}}
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                                data-bs-target="#previewPromoModal"
                                onclick='event.stopPropagation(); previewPodcast(@json($podcast))'>
                                Preview
                            </button>

                            <button class="btn btn-outline-danger w-100 mt-2"
                                onclick="deletePromo('{{ $podcast['id'] }}', this)">
                                Hapus
                            </button>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada Podcast
                    </div>
                </div>
            @endforelse

        </div>


    </div>

    <div class="modal fade" id="addPodcastModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Tambah Podcast Parenting</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addPodcastForm" enctype="multipart/form-data">

                    <div class="modal-body">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Judul Podcast</label>
                            <input type="text" class="form-control" id="podcast_title" placeholder="Masukkan judul podcast" maxlength="200" required>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="podcast_description" placeholder="Masukkan deskripsi podcast" maxlength="255" required></textarea>
                        </div>

                        <!-- MODERATOR -->
                        <div class="mb-3">
                            <label class="form-label">Moderator</label>
                            <input type="text" class="form-control" id="podcast_moderator" placeholder="Masukkan moderator podcast" maxlength="255" required>
                        </div>

                        <!-- VIDEO URL -->
                        <div class="mb-3">
                            <label class="form-label">Link Video YouTube</label>
                            <input type="text" class="form-control" id="podcast_videoUrl"
                                placeholder="https://youtube.com/watch?v=..." oninput="generateThumbnail()">
                        </div>

                        <div class="mb-3 text-center">
                            <img id="thumbPreview" src="" style="max-width:100%;display:none;border-radius:8px;">
                            <input type="hidden" id="podcast_thumbnailUrl">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thumbnail Video</label>
                            <input type="file" class="form-control" id="banner" accept="image/*">

                            <!-- PREVIEW -->
                            <div id="thumbnailPreview" style="margin-top:12px; display:none;">
                                <img id="thumbnailPreviewImg"
                                    style="width:100%; max-height:420px; object-fit:cover; border-radius:10px; border:1px solid #ddd;">
                            </div>
                        </div>

                        <div id="podcastAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100" type="submit">
                            Simpan Podcast
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="previewPodcastModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="previewTitle"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">

                    <img id="previewThumbnail" class="img-fluid rounded mb-3" style="max-height:250px;object-fit:cover;">

                    <p id="previewDesc"></p>

                    <div class="mb-2">
                        🎙 Moderator: <span id="previewModerator"></span>
                    </div>

                    <a id="previewVideo" class="btn btn-danger btn-sm mb-2" target="_blank">
                        ▶ Tonton Video
                    </a>

                    <br>

                    <small id="previewDate" class="text-muted"></small>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editPodcastModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Podcast</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editPodcastForm">

                    <input type="hidden" id="edit_id">
                    <input type="hidden" id="edit_thumbnail">

                    <div class="modal-body">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control" id="edit_title" placeholder="Masukkan judul podcast">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description" placeholder="Masukkan deskripsi podcast"></textarea>
                        </div>

                        <!-- MODERATOR -->
                        <div class="mb-3">
                            <label class="form-label">Moderator</label>
                            <input type="text" class="form-control" id="edit_moderator" placeholder="Masukkan moderator podcast">
                        </div>

                        <!-- VIDEO -->
                        <div class="mb-3">
                            <label class="form-label">Link YouTube</label>
                            <input type="text" class="form-control" id="edit_videoUrl"
                                oninput="generateEditThumbnail()">
                        </div>

                        <!-- THUMB PREVIEW -->
                        <div class="text-center">
                            <img id="edit_thumbnail_preview" class="img-fluid rounded"
                                style="max-height:200px;display:none;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thumbnail Video</label>
                            <input type="file" class="form-control" id="edit_banner" accept="image/*">

                            <!-- PREVIEW -->
                            <div id="thumbnailEditPreview" style="margin-top:12px; display:none;">
                                <img id="thumbnailEditPreviewImg"
                                    style="width:100%; max-height:420px; object-fit:cover; border-radius:10px; border:1px solid #ddd;">
                            </div>
                        </div>

                        <div id="editAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100" type="submit">
                            Update Podcast
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
        $('#banner').on('change', function() {
            let file = this.files[0];

            if (!file) return;

            let reader = new FileReader();

            reader.onload = function(e) {
                $('#thumbnailPreviewImg').attr('src', e.target.result);
                $('#thumbnailPreview').fadeIn(200);
            };

            reader.readAsDataURL(file);
        });

        $('#edit_banner').on('change', function() {
            let file = this.files[0];

            if (!file) return;

            let reader = new FileReader();

            reader.onload = function(e) {
                $('#thumbnailEditPreviewImg').attr('src', e.target.result);
                $('#thumbnailEditPreview').fadeIn(200);
            };

            reader.readAsDataURL(file);
        });

        function generateThumbnail() {
            let url = $('#podcast_videoUrl').val();
            let videoId = extractYouTubeId(url);

            if (!videoId) {
                $('#thumbPreview').hide();
                return;
            }

            let thumb = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;

            $('#thumbPreview')
                .attr('src', thumb)
                .show();

            $('#podcast_thumbnailUrl').val(thumb);
        }

        function extractYouTubeId(url) {
            let regExp =
                /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;

            let match = url.match(regExp);
            return (match && match[2].length === 11) ?
                match[2] :
                null;
        }

        function generateEditThumbnail() {
            let url = $('#edit_videoUrl').val();
            let id = extractYouTubeId(url);

            if (!id) {
                $('#edit_thumbnail_preview').hide();
                return;
            }

            let thumb = `https://img.youtube.com/vi/${id}/hqdefault.jpg`;

            $('#edit_thumbnail_preview')
                .attr('src', thumb)
                .show();

            $('#edit_thumbnail').val(thumb);
        }

        $('#addPodcastForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            let formData = new FormData();

            let thumbnail = $('#banner')[0].files[0];
            if (thumbnail) {
                formData.append('thumbnail', thumbnail);
            }

            formData.append('title', $('#podcast_title').val());
            formData.append('description', $('#podcast_description').val());
            formData.append('moderator', $('#podcast_moderator').val());
            formData.append('videoUrl', $('#podcast_videoUrl').val());
            formData.append('score', $('#podcast_score').val());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '/podcast',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                    btn.prop('disabled', false).text('Simpan Podcast');
                },

                error: function(xhr) {
                    $('#podcastAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.message || 'Gagal membuat podcast'}
                </div>
            `);
            btn.prop('disabled', false).text('Simpan Podcast');
                }
            });
        });
    </script>

    <script>
        function previewPodcast(p) {
            console.log(p);
            $('#previewTitle').text(p.title);
            $('#previewDesc').text(p.description);
            $('#previewScore').text(p.score);
            $('#previewModerator').text(p.moderator);

            $('#previewThumbnail')
                .attr('src', p.thumbnail || '')
                .toggle(!!p.thumbnail);

            if (p.videoUrl) {
                $('#previewVideo')
                    .attr('href', p.videoUrl)
                    .show();
            } else {
                $('#previewVideo').hide();
            }

            let date = new Date(p.createdAt);
            $('#previewDate').text(
                'Dibuat pada ' + date.toLocaleDateString('id-ID')
            );

            $('#previewPodcastModal').modal('show');
        }

        function editPodcast(id) {
            $.get(`/podcast/${id}`, function(res) {

                let p = res.data;

                $('#edit_id').val(p.id);
                $('#edit_title').val(p.title);
                $('#edit_description').val(p.description);
                $('#edit_moderator').val(p.moderator);
                $('#edit_videoUrl').val(p.videoUrl);
                $('#edit_score').val(p.score);
                $('#edit_thumbnail').val(p.thumbnail);

                if (p.thumbnail) {
                    $('#edit_thumbnail_preview')
                        .attr('src', p.thumbnail)
                        .show();

                    $("#thumbnailEditPreview").show();
                    $("#thumbnailEditPreviewImg").attr('src', p.thumbnail);
                }

                $('#editPodcastModal').modal('show');
            });
        }


        $('#editPodcastForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            let formData = new FormData();

            let thumbnail = $('#edit_banner')[0].files[0];
            if (thumbnail) {
                formData.append('thumbnail', thumbnail);
            }

            formData.append('title', $('#edit_title').val());
            formData.append('description', $('#edit_description').val());
            formData.append('moderator', $('#edit_moderator').val());
            formData.append('videoUrl', $('#edit_videoUrl').val());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));


            $.ajax({
                url: `/podcast/${$('#edit_id').val()}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PATCH'
                },
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                    btn.prop('disabled', false).text('Update Podcast');
                },

                error: function(xhr) {
                    $('#editAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal update podcast'}
                </div>
            `);
            btn.prop('disabled', false).text('Update Podcast');
                }
            });
        });


        function deletePromo(id, btn) {
            event.stopPropagation();

            Swal.fire({
                title: 'Yakin ingin menghapus Podcast?',
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
                        url: `/podcast/${id}`,
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
                            Swal.fire('Gagal!', xhr.responseJSON?.server || 'Gagal menghapus Podcast',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
