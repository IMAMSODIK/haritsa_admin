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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuizModal">
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
                            @if (!empty($article['thumbnail']))
                                <img src="{{ $article['thumbnail'] }}" class="card-img-top"
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
                            <button class="btn btn-outline-primary w-100" onclick='event.stopPropagation();'>
                                Sematkan Kuis
                            </button>

                            <button class="btn btn-outline-danger w-100 mt-2"
                                onclick="deletePromo('{{ $article['id'] }}', this)">
                                Hapus Kuis
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

    <div class="modal fade" id="addQuizModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Tambah Kuis Parenting</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addQuizForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Judul Kuis</label>
                            <input type="text" class="form-control" name="title" maxlength="200" required>
                        </div>

                        <!-- VIDEO -->
                        <div class="mb-3">
                            <label class="form-label">Video URL (Optional)</label>
                            <input type="text" class="form-control" name="videoUrl">
                        </div>

                        <!-- START & END -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Start At</label>
                                <input type="datetime-local" class="form-control" name="startAt" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>End At</label>
                                <input type="datetime-local" class="form-control" name="endAt" required>
                            </div>
                        </div>

                        <!-- DURATION -->
                        <div class="mb-3">
                            <label>Durasi (Menit)</label>
                            <input type="number" class="form-control" name="durationMin" min="10" required>
                        </div>

                        <!-- THUMBNAIL -->
                        <div class="mb-3">
                            <label>Thumbnail</label>
                            <input type="file" class="form-control" name="thumbnail"
                                accept="image/png,image/jpeg,image/jpg">
                        </div>

                        <hr>

                        <!-- QUESTIONS -->
                        <h6>Daftar Soal</h6>

                        <div id="questionsContainer"></div>

                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addQuestion()">
                            + Tambah Soal
                        </button>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success w-100">
                            Simpan Kuis
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
                            <input type="text" class="form-control" id="edit_videoUrl"
                                oninput="generateEditThumbnail()">
                        </div>

                        <!-- THUMB PREVIEW -->
                        <div class="text-center">
                            <img id="edit_thumbnail_preview" class="img-fluid rounded"
                                style="max-height:200px;display:none;">
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
        let questionIndex = 0;

        function addQuestion() {

            const qIndex = questionIndex++;

            const html = `
        <div class="card mt-3 question-block">
            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <h6>Soal</h6>
                    <button type="button" class="btn btn-sm btn-danger"
                        onclick="this.closest('.question-block').remove()">
                        Hapus
                    </button>
                </div>

                <input type="text"
                    class="form-control mb-2 question-text"
                    name="questions[${qIndex}][text]"
                    placeholder="Tulis soal..."
                    required>

                <input type="number"
                    class="form-control mb-3 question-score"
                    name="questions[${qIndex}][score]"
                    placeholder="Score"
                    min="0"
                    required>

                <div id="options-${qIndex}"></div>

                <button type="button"
                    class="btn btn-sm btn-info"
                    onclick="addOption(${qIndex})">
                    + Tambah Jawaban
                </button>

            </div>
        </div>
    `;

            document.getElementById('questionsContainer')
                .insertAdjacentHTML('beforeend', html);
        }

        function addOption(qIndex) {

            const container = document.getElementById(`options-${qIndex}`);
            const optionCount = container.children.length;

            const html = `
                <div class="input-group mb-2 option-block">
                    <input type="text"
                        class="form-control"
                        name="questions[${qIndex}][options][${optionCount}][text]"
                        placeholder="Teks jawaban..."
                        required>

                    <div class="input-group-text">
                        <input type="radio"
                            name="correct_${qIndex}"
                            onclick="setCorrect(${qIndex}, ${optionCount})">
                    </div>

                    <button type="button"
                        class="btn btn-danger"
                        onclick="this.parentElement.remove()">
                        X
                    </button>

                    <input type="hidden"
                        name="questions[${qIndex}][options][${optionCount}][isCorrect]"
                        value="false">
                </div>
                `;

            container.insertAdjacentHTML('beforeend', html);
        }

        function setCorrect(qIndex, optionIndex) {

            const hiddenInputs =
                document.querySelectorAll(`#options-${qIndex} input[type=hidden]`);

            hiddenInputs.forEach((input, index) => {
                input.value = index === optionIndex ? "true" : "false";
            });
        }
    </script>

    <script>
        $('#addQuizForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            let form = document.getElementById('addQuizForm');
            let fd = new FormData(form);

            // 🔥 KONVERSI datetime-local KE ISO 8601
            let startAt = new Date(fd.get('startAt')).toISOString();
            let endAt = new Date(fd.get('endAt')).toISOString();

            fd.set('startAt', startAt);
            fd.set('endAt', endAt);

            // 🔥 BANGUN ULANG QUESTIONS MENJADI JSON
            let questions = [];

            $('.question-block').each(function() {

                let text = $(this).find('.question-text').val();
                let scoreVal = $(this).find('.question-score').val();

                let score = parseInt(scoreVal);
                if (isNaN(score)) score = 0;

                let options = [];

                $(this).find('.option-block').each(function() {

                    let optText = $(this).find('.option-text').val();
                    let isCorrect = $(this).find('.option-is-correct').val() === "true";

                    options.push({
                        text: optText,
                        isCorrect: isCorrect
                    });

                });

                questions.push({
                    text: text,
                    score: score,
                    options: options
                });

            });

            fd.delete('questions');
            fd.append('questions', JSON.stringify(questions));

            $.ajax({
                url: '/kuis-parenting',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Simpan Kuis');

                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Gagal menyimpan kuis',
                        'error'
                    );
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

                if (a.thumbnail) {
                    $('#edit_thumbnail_preview')
                        .attr('src', a.thumbnail)
                        .show();
                }

                // rich text content
                editQuill.root.innerHTML = a.content || '';

                $('#editArtikelModal').modal('show');
            });
        }

        $('#editArtikelForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Updating...');

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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Artikel berhasil diupdate',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(() => location.reload(), 2000);
                },

                error: function(xhr) {
                    btn.prop('disabled', false).text('Update Artikel');

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
