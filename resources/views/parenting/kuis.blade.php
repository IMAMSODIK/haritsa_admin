@extends('layouts.template')

@section('own_style')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        .article-card {
            transition: all 0.25s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .article-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .article-card img {
            transition: transform 0.35s ease;
        }

        .article-card:hover img {
            transform: scale(1.05);
        }

        .article-card .position-relative::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            transition: 0.3s;
        }

        .article-card:hover .position-relative::after {
            background: rgba(0, 0, 0, 0.1);
        }

        .article-card button {
            transition: all .2s;
        }

        .article-card button:hover {
            transform: scale(1.03);
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
                    <i class="fa fa-plus"></i> Tambah Kuis
                </button>
            </div>
        </div>

        <div class="row g-4">

            @forelse ($kuises as $kuis)
                @php
                    $questionCount = count($kuis['questions'] ?? []);
                @endphp

                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 article-card" style="cursor:pointer"
                        onclick="editArtikel('{{ $kuis['id'] }}')">

                        <div class="position-relative">

                            {{-- THUMBNAIL --}}
                            @if (!empty($kuis['thumbnail']))
                                <img src="{{ $kuis['thumbnail'] }}" class="card-img-top"
                                    style="height:200px;object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height:200px;">
                                    <span class="text-muted">No Thumbnail</span>
                                </div>
                            @endif

                            {{-- VIDEO BADGE --}}
                            @if (!empty($kuis['videoUrl']))
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    Video
                                </span>
                            @endif

                        </div>

                        <div class="card-body d-flex flex-column">

                            {{-- TITLE --}}
                            <h5 class="card-title mb-2">
                                {{ $kuis['title'] }}
                            </h5>

                            {{-- DURASI --}}
                            <div class="small text-muted mb-2">
                                ⏱ Durasi: <strong>{{ $kuis['durationMin'] }} menit</strong>
                            </div>

                            {{-- TOTAL POINT --}}
                            <div class="small text-muted mb-2">
                                ⭐ Total Point:
                                <strong>{{ $kuis['scoreTotal'] }}</strong>
                            </div>

                            {{-- PERIODE --}}
                            <div class="small text-muted mb-3">
                                📅
                                {{ \Carbon\Carbon::parse($kuis['startAt'])->format('d M Y H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($kuis['endAt'])->format('d M Y H:i') }}
                            </div>

                            {{-- QUESTION STATUS --}}
                            <div class="mb-3">

                                @if ($questionCount == 0)
                                    <div class="alert alert-warning p-2 small text-center">
                                        Belum ada pertanyaan
                                    </div>
                                @else
                                    <div class="alert alert-success p-2 small text-center">
                                        {{ $questionCount }} Pertanyaan tersedia
                                    </div>
                                @endif

                            </div>

                            <div class="mt-auto"></div>

                            {{-- BUTTONS --}}
                            @if ($questionCount == 0)
                                <button class="btn btn-primary w-100" onclick="openQuestionModal('{{ $kuis['quizId'] }}')">
                                    Buat Pertanyaan
                                </button>
                            @else
                                <button class="btn btn-outline-primary w-100"
                                    onclick="event.stopPropagation(); lihatPertanyaan('{{ $kuis['id'] }}')">
                                    Lihat Pertanyaan
                                </button>
                            @endif

                            <button class="btn btn-outline-danger w-100 mt-2"
                                onclick="event.stopPropagation(); deleteKuis('{{ $kuis['id'] }}', this)">
                                Hapus Kuis
                            </button>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada Kuis
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
                            <input type="text" class="form-control" name="title" maxlength="200" required
                                placeholder="Masukkan judul kuis">
                        </div>

                        <!-- VIDEO -->
                        <div class="mb-3">
                            <label class="form-label">Video URL (Optional)</label>
                            <input type="text" class="form-control" name="videoUrl" id="videoUrl"
                                placeholder="http://youtube.com/video">

                            <div class="mt-2 text-center">
                                <img id="youtubePreview" src="https://placehold.co/600x340?text=YouTube+Thumbnail"
                                    class="img-fluid rounded border" style="max-height:200px; object-fit:cover;">
                            </div>
                        </div>


                        <!-- START & END -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Waktu Mulai</label>
                                <input type="datetime-local" class="form-control" name="startAt" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Waktu Selesai</label>
                                <input type="datetime-local" class="form-control" name="endAt" required>
                            </div>
                        </div>

                        <!-- DURATION -->
                        <div class="mb-3">
                            <label>Durasi (Menit)</label>
                            <input type="number" class="form-control" name="durationMin" min="10" required
                                placeholder="Durasi pengerjaan kuis">
                        </div>

                        <!-- THUMBNAIL -->
                        <div class="mb-3">
                            <label>Thumbnail</label>
                            <input type="file" class="form-control" name="thumbnail"
                                accept="image/png,image/jpeg,image/jpg" id="thumbnailInput">

                            <div class="mt-2 text-center">
                                <img id="thumbnailPreview" src="https://placehold.co/600x340?text=Thumbnail+Preview"
                                    class="img-fluid rounded border" style="max-height:200px; object-fit:cover;">
                            </div>
                        </div>

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

    <div class="modal fade" id="questionModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Atur Pertanyaan Quiz</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="quizId">

                    <div id="questionsContainer"></div>

                    <button class="btn btn-sm btn-primary mt-2" onclick="addQuestion()">
                        + Tambah Pertanyaan
                    </button>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success w-100" onclick="submitQuestions()">
                        Simpan Pertanyaan
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="lihatQuestionModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Daftar Pertanyaan</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="listQuestions"></div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editArtikelModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Kuis</h5>
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
        let qIndex = 0;

        function openQuestionModal(id) {
            $('#quizId').val(id)
            $('#questionsContainer').html('')
            qIndex = 0

            $('#questionModal').modal('show')
        }

        function addQuestion() {

            let qId = Date.now()

            let html = `
            <div class="card mt-3 question-block">

                <div class="card-body">

                <div class="d-flex justify-content-between mb-2">
                    <strong>Pertanyaan</strong>
                    <button type="button" class="btn btn-sm btn-danger"
                        onclick="$(this).closest('.question-block').remove()">
                        Hapus
                    </button>
                </div>

                <input class="form-control mb-2 question-text"
                    placeholder="Tulis pertanyaan">

                <input type="number"
                    class="form-control mb-3 question-score"
                    placeholder="Score">

                <div class="options" data-radio="correct${qId}"></div>

                <button type="button" class="btn btn-sm btn-info"
                    onclick="addOption(this)">
                    + Tambah Jawaban
                </button>

                </div>
            </div>
            `

            $('#questionsContainer').append(html)
        }

        function addOption(btn) {

            let container = $(btn).closest('.card-body').find('.options')
            let radioName = container.data('radio')

            let html = `
                <div class="input-group mb-2 option-block">

                    <input class="form-control option-text"
                        placeholder="Jawaban">

                    <div class="input-group-text">
                        <input type="radio" name="${radioName}" class="correct-radio">
                    </div>

                    <button type="button" class="btn btn-danger"
                        onclick="$(this).parent().remove()">
                        X
                    </button>

                </div>
                `

            container.append(html)
        }

        function collectQuestions() {

            let questions = []

            $('#questionsContainer .question-block').each(function() {

                let text = $(this).find('.question-text').val()?.trim()
                let score = parseInt($(this).find('.question-score').val())

                let options = []

                $(this).find('.option-block').each(function() {

                    let optionText = $(this).find('.option-text').val()?.trim()
                    let isCorrect = $(this).find('.correct-radio').is(':checked')

                    if (optionText) {
                        options.push({
                            text: optionText,
                            isCorrect: isCorrect
                        })
                    }

                })

                if (text && options.length) {
                    questions.push({
                        text: text,
                        score: score || 0,
                        options: options
                    })
                }

            })

            return questions
        }

        function submitQuestions() {

            let quizId = $('#quizId').val()
            let questions = collectQuestions()

            if (!questions.length) {
                Swal.fire('Error', 'Belum ada pertanyaan', 'error')
                return
            }

            let successCount = 0
            let failCount = 0
            let total = questions.length

            Swal.fire({
                title: 'Menyimpan pertanyaan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            })

            questions.forEach(q => {

                $.ajax({
                    url: `/kuis-parenting/${quizId}/soal`,
                    method: "POST",
                    contentType: "application/json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify({
                        text: q.text,
                        score: q.score,
                        options: q.options
                    }),

                    success: function(res) {

                        $("#questionModal").modal('hide');
                        successCount++
                        checkFinish()
                        setTimeout(() => {
                            location.reload()
                        }, 1500);
                    },

                    error: function(xhr) {

                        console.log("ERROR:", xhr.responseJSON)
                        failCount++
                        checkFinish()

                    }
                })

            })


            function checkFinish() {

                if (successCount + failCount === total) {

                    Swal.fire({
                        icon: failCount ? 'warning' : 'success',
                        title: 'Selesai'
                    })

                }

            }

        }

        function lihatPertanyaan(quizId) {

            $('#listQuestions').html(`
        <div class="text-center p-4">
            <div class="spinner-border"></div>
        </div>
    `)

            $('#lihatQuestionModal').modal('show')

            $.ajax({

                url: `/kuis-parenting/${quizId}/get-soal`,
                method: "GET",

                success: function(res) {

                    let html = ''
                    let questions = res.data.questions ?? []

                    if (questions.length === 0) {

                        $('#listQuestions').html(`
                    <div class="text-center text-muted">
                        Belum ada pertanyaan
                    </div>
                `)

                        return
                    }

                    questions.forEach((q, index) => {

                        html += `
                <div class="card mb-3">

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-2">
                            <strong>Pertanyaan ${index + 1}</strong>
                            <span class="badge bg-primary">
                                Score ${q.score}
                            </span>
                        </div>

                        <p>${q.text}</p>

                        <ul class="list-group">
                `

                        q.options.forEach(opt => {

                            html += `
                        <li class="list-group-item">
                            ${opt.text}
                        </li>
                    `

                        })

                        html += `
                        </ul>

                    </div>

                </div>
                `
                    })

                    $('#listQuestions').html(html)

                },

                error: function() {

                    $('#listQuestions').html(`
                <div class="alert alert-danger">
                    Gagal mengambil data pertanyaan
                </div>
            `)

                }

            })

        }

        function getYoutubeID(url) {

            let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            let match = url.match(regExp);

            return (match && match[2].length === 11) ? match[2] : null;
        }

        $('#videoUrl').on('keyup change', function() {

            let url = $(this).val();
            let videoId = getYoutubeID(url);

            if (videoId) {

                let thumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;

                $('#youtubePreview').attr('src', thumbnail);

            } else {

                $('#youtubePreview').attr(
                    'src',
                    'https://placehold.co/600x340?text=YouTube+Thumbnail'
                );

            }

        });

        $('#thumbnailInput').on('change', function(e) {

            let file = e.target.files[0];

            if (!file) return;

            let reader = new FileReader();

            reader.onload = function(event) {

                $('#thumbnailPreview').attr('src', event.target.result);

            }

            reader.readAsDataURL(file);

        });

        $('#thumbnailInput').on('change', function() {
            $('#youtubePreview').hide();
        });

        function toISOWithOffset(dateStr) {

            const d = new Date(dateStr);
            const tzOffset = -d.getTimezoneOffset();
            const diff = tzOffset >= 0 ? "+" : "-";

            const pad = n => String(Math.floor(Math.abs(n))).padStart(2, "0");

            return d.getFullYear() +
                "-" + pad(d.getMonth() + 1) +
                "-" + pad(d.getDate()) +
                "T" + pad(d.getHours()) +
                ":" + pad(d.getMinutes()) +
                ":" + pad(d.getSeconds()) +
                diff + pad(tzOffset / 60) +
                ":" + pad(tzOffset % 60);
        }

        $('#addQuizForm').on('submit', function(e) {

            e.preventDefault();

            let fd = new FormData();

            fd.append('title', $('input[name=title]').val());
            fd.append('videoUrl', $('input[name=videoUrl]').val());
            fd.append('startAt', toISOWithOffset($('input[name=startAt]').val()));
            fd.append('endAt', toISOWithOffset($('input[name=endAt]').val()));
            fd.append('durationMin', $('input[name=durationMin]').val());
            fd.append('_token', $('meta[name="csrf-token"]').attr('content'));

            let thumb = $('input[name=thumbnail]')[0].files[0];

            if (thumb) {
                fd.append('thumbnail', thumb);
            }

            $.ajax({
                url: '/kuis-parenting',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    $("#addQuizModal").modal('hide');
                    Swal.fire(
                        'Berhasil',
                        res.message,
                        'success'
                    ).then(() => location.reload());

                },
                error: function(xhr) {

                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Gagal menyimpan',
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

        function deleteKuis(id, btn) {
            event.stopPropagation();

            Swal.fire({
                title: 'Yakin ingin menghapus Kuis?',
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
                        url: `/kuis-parenting/${id}`,
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
                            Swal.fire('Gagal!', xhr.responseJSON?.server || 'Gagal menghapus Kuis',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
