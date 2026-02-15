@extends('layouts.template')

@section('own_style')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                    <i class="fa fa-plus"></i> Tambah Artikel
                </button>
            </div>
        </div>

        <div class="row g-4">

            @forelse ($articles as $article)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 article-card" style="cursor:pointer"
                        onclick="editArtikel('{{ $article['id'] }}')">

                        {{-- Thumbnail --}}
                        <div class="position-relative">
                            @if (!empty($article['thumbnailUrl']))
                                <img src="{{ $article['thumbnailUrl'] }}" class="card-img-top"
                                    style="height:200px;object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height:200px;">
                                    <span class="text-muted">No Thumbnail</span>
                                </div>
                            @endif

                            {{-- Badge youtube --}}
                            @if ($article['videoUrl'])
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    YouTube
                                </span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">

                            {{-- Title --}}
                            <h5 class="card-title mb-1">
                                {{ $article['title'] }}
                            </h5>

                            {{-- Description --}}
                            <p class="text-muted small mb-2">
                                {{ \Illuminate\Support\Str::words(strip_tags($article['content'] ?? '-'), 25) }}
                            </p>

                            {{-- Meta info --}}
                            <div class="small text-muted mb-2">
                                ⭐ Point: <strong>{{ $article['score'] ?? '-' }}</strong><br>
                            </div>

                            {{-- Moderator --}}
                            <div class="small text-muted mb-2">
                                <i class="fa fa-microphone"></i>
                                {{ $article['moderator'] ?? '-' }}
                            </div>

                            {{-- Created --}}
                            <div class="small text-muted mb-3">
                                Dibuat {{ date('d M Y', strtotime($article['createdAt'])) }}
                            </div>

                            <div class="mt-auto"></div>

                            {{-- Buttons --}}
                            <button class="btn btn-outline-primary w-100"
                                onclick='event.stopPropagation(); location.href="/artikel-parenting/{{ $article["id"] }}/preview"'>
                                Preview
                            </button>

                            <button class="btn btn-outline-danger w-100 mt-2"
                                onclick="deletePromo('{{ $article['id'] }}', this)">
                                Hapus
                            </button>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada Artikel
                    </div>
                </div>
            @endforelse

        </div>


    </div>

    <div class="modal fade" id="addArticleModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Tambah Artikel Parenting</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addArticleForm">

                    <div class="modal-body">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Judul Artikel</label>
                            <input type="text" class="form-control" id="article_title" maxlength="200" required>
                        </div>

                        <!-- CONTENT (RICH TEXT) -->
                        <div class="mb-3">
                            <label class="form-label">Isi Artikel</label>
                            <div id="editor" style="height:200px;"></div>
                        </div>

                        <!-- MODERATOR -->
                        <div class="mb-3">
                            <label class="form-label">Moderator</label>
                            <input type="text" class="form-control" id="article_moderator" maxlength="255" required>
                        </div>

                        <!-- VIDEO -->
                        <div class="mb-3">
                            <label class="form-label">Link YouTube (optional)</label>
                            <input type="text" class="form-control" id="article_videoUrl"
                                oninput="generateThumbnailArticle()">
                        </div>

                        <!-- Thumbnail preview -->
                        <div class="mb-3 text-center">
                            <img id="articleThumb" style="max-width:100%;display:none;border-radius:8px;">
                            <input type="hidden" id="article_thumbnailUrl">
                        </div>

                        <!-- SCORE -->
                        <div class="mb-3">
                            <label class="form-label">Point</label>
                            <input type="number" class="form-control" id="article_score" min="0" required>
                        </div>

                        <div id="articleAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">
                            Simpan Artikel
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="previewArticleModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="previewTitle"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <img id="previewThumbnail" class="img-fluid rounded mb-3"
                        style="max-height:250px;object-fit:cover;width:100%;">

                    <!-- meta -->
                    <div class="mb-2 text-muted small">
                        🎙 Moderator: <span id="previewModerator"></span> |
                        🎯 Point: <span id="previewScore"></span>
                    </div>

                    <!-- content -->
                    <div id="previewContent" style="line-height:1.6;font-size:15px;"></div>

                    <!-- video -->
                    <div class="mt-3 text-center">
                        <a id="previewVideo" class="btn btn-danger btn-sm" target="_blank">
                            ▶ Tonton Video
                        </a>
                    </div>

                    <div class="mt-3 text-muted small text-end">
                        <span id="previewDate"></span>
                    </div>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="editArtikelModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Artikel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editArtikelForm">

                    <input type="hidden" id="edit_id">

                    <div class="modal-body">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control" id="edit_title">
                        </div>

                        <!-- CONTENT -->
                        <div class="mb-3">
                            <label class="form-label">Isi Artikel</label>
                            <div id="edit_editor" style="height:200px;"></div>
                        </div>

                        <!-- MODERATOR -->
                        <div class="mb-3">
                            <label class="form-label">Moderator</label>
                            <input type="text" class="form-control" id="edit_moderator">
                        </div>

                        <!-- VIDEO -->
                        <div class="mb-3">
                            <label class="form-label">Link Video</label>
                            <input type="text" class="form-control" id="edit_videoUrl">
                        </div>

                        <!-- SCORE -->
                        <div class="mb-3">
                            <label class="form-label">Score</label>
                            <input type="number" class="form-control" id="edit_score">
                        </div>

                        <!-- STATUS -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_isActive">
                            <label class="form-check-label">Aktif</label>
                        </div>

                        <div id="editAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">
                            Update Artikel
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
        let quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Tulis artikel di sini...'
        });

        let editQuill = new Quill('#edit_editor', {
            theme: 'snow'
        });
    </script>
    <script>
        function extractYouTubeId(url) {
            let regExp =
                /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;

            let match = url.match(regExp);
            return (match && match[2].length === 11) ?
                match[2] :
                null;
        }

        function generateThumbnailArticle() {
            let url = $('#article_videoUrl').val();
            let id = extractYouTubeId(url);

            if (!id) {
                $('#articleThumb').hide();
                return;
            }

            let thumb = `https://img.youtube.com/vi/${id}/hqdefault.jpg`;

            $('#articleThumb').attr('src', thumb).show();
            $('#article_thumbnailUrl').val(thumb);
        }


        $('#addArticleForm').on('submit', function(e) {
            e.preventDefault();

            let content = quill.root.innerHTML;

            $.ajax({
                url: '/artikel-parenting-parenting',
                method: 'POST',
                data: {
                    title: $('#article_title').val(),
                    content: content,
                    moderator: $('#article_moderator').val(),
                    videoUrl: $('#article_videoUrl').val(),
                    score: $('#article_score').val(),
                    thumbnailUrl: $('#article_thumbnailUrl').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },

                error: function(xhr) {
                    $('#articleAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal membuat artikel'}
                </div>
            `);
                }
            });
        });
    </script>

    <script>
        function previewArticleModal(a) {

            $('#previewTitle').text(a.title);
            $('#previewModerator').text(a.moderator);
            $('#previewScore').text(a.score);

            // rich text content
            $('#previewContent').html(a.content);

            $('#previewThumbnail')
                .attr('src', a.thumbnailUrl || '')
                .toggle(!!a.thumbnailUrl);

            if (a.videoUrl) {
                $('#previewVideo')
                    .attr('href', a.videoUrl)
                    .show();
            } else {
                $('#previewVideo').hide();
            }

            let date = new Date(a.createdAt);
            $('#previewDate').text(
                'Dibuat pada ' + date.toLocaleDateString('id-ID')
            );

            $('#previewArticleModal').modal('show');
        }

        function editArtikel(id) {
            $.get(`/artikel-parenting/${id}`, function(res) {

                let a = res.data;

                $('#edit_id').val(a.id);
                $('#edit_title').val(a.title);
                $('#edit_moderator').val(a.moderator);
                $('#edit_videoUrl').val(a.videoUrl);
                $('#edit_score').val(a.score);
                $('#edit_isActive').prop('checked', a.isActive ?? true);

                // rich text content
                editQuill.root.innerHTML = a.content || '';

                $('#editArtikelModal').modal('show');
            });
        }

        $('#editArtikelForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: `/artikel-parenting/${$('#edit_id').val()}`,
                method: 'PATCH',
                data: {
                    title: $('#edit_title').val(),
                    content: editQuill.root.innerHTML,
                    moderator: $('#edit_moderator').val(),
                    videoUrl: $('#edit_videoUrl').val(),
                    score: $('#edit_score').val(),
                    isActive: $('#edit_isActive').is(':checked'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },

                error: function(xhr) {
                    $('#editAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal update artikel'}
                </div>
            `);
                }
            });
        });


        function deletePromo(id, btn) {
            event.stopPropagation();

            Swal.fire({
                title: 'Yakin ingin menghapus Artikel?',
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
                        url: `/artikel-parenting/${id}`,
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
                            Swal.fire('Gagal!', xhr.responseJSON?.server || 'Gagal menghapus Artikel',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
