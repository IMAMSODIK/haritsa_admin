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
                            @if (!empty($podcast['thumbnailUrl']))
                                <img src="{{ $podcast['thumbnailUrl'] }}" class="card-img-top"
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

                            {{-- Meta info --}}
                            <div class="small text-muted mb-2">
                                ⭐ Point: <strong>{{ $podcast['score'] ?? '-' }}</strong><br>
                            </div>

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
                            <input type="text" class="form-control" id="podcast_title" maxlength="200" required>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="podcast_description" maxlength="255" required></textarea>
                        </div>

                        <!-- MODERATOR -->
                        <div class="mb-3">
                            <label class="form-label">Moderator</label>
                            <input type="text" class="form-control" id="podcast_moderator" maxlength="255" required>
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

                        <!-- SCORE -->
                        <div class="mb-3">
                            <label class="form-label">Point</label>
                            <input type="number" class="form-control" id="podcast_score" min="0" required>
                        </div>

                        <div id="podcastAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">
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

                    <div class="fw-bold mb-2">
                        🎯 Point penting: <span id="previewScore"></span>
                    </div>

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
                            <input type="text" class="form-control" id="edit_title">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description"></textarea>
                        </div>

                        <!-- MODERATOR -->
                        <div class="mb-3">
                            <label class="form-label">Moderator</label>
                            <input type="text" class="form-control" id="edit_moderator">
                        </div>

                        <!-- VIDEO -->
                        <div class="mb-3">
                            <label class="form-label">Link YouTube</label>
                            <input type="text" class="form-control" id="edit_videoUrl"
                                oninput="generateEditThumbnail()">
                        </div>

                        <!-- SCORE -->
                        <div class="mb-3">
                            <label class="form-label">Score</label>
                            <input type="number" class="form-control" id="edit_score">
                        </div>

                        <!-- THUMB PREVIEW -->
                        <div class="text-center">
                            <img id="edit_thumbnail_preview" class="img-fluid rounded"
                                style="max-height:200px;display:none;">
                        </div>

                        <div id="editAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">
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

            let formData = new FormData();

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
                },

                error: function(xhr) {
                    $('#podcastAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.message || 'Gagal membuat podcast'}
                </div>
            `);
                }
            });
        });
    </script>

    <script>
        function previewPodcast(p) {

            $('#previewTitle').text(p.title);
            $('#previewDesc').text(p.description);
            $('#previewScore').text(p.score);
            $('#previewModerator').text(p.moderator);

            $('#previewThumbnail')
                .attr('src', p.thumbnailUrl || '')
                .toggle(!!p.thumbnailUrl);

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
                $('#edit_thumbnail').val(p.thumbnailUrl);

                if (p.thumbnailUrl) {
                    $('#edit_thumbnail_preview')
                        .attr('src', p.thumbnailUrl)
                        .show();
                }

                $('#editPodcastModal').modal('show');
            });
        }


        $('#editPodcastForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: `/podcast/${$('#edit_id').val()}`,
                method: 'PATCH',
                data: {
                    title: $('#edit_title').val(),
                    description: $('#edit_description').val(),
                    moderator: $('#edit_moderator').val(),
                    videoUrl: $('#edit_videoUrl').val(),
                    score: $('#edit_score').val(),
                    thumbnailUrl: $('#edit_thumbnail').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },

                error: function(xhr) {
                    $('#editAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal update podcast'}
                </div>
            `);
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
