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
                    <i class="fa fa-plus"></i> Tambah Survey Layanan
                </button>
            </div>
        </div>

        <div class="row g-4">

            @forelse ($surveys as $survey)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 survey-card" style="cursor:pointer"
                        onclick="editSurvey('{{ $survey['id'] }}')">

                        {{-- Thumbnail --}}
                        <div class="position-relative">
                            @if (!empty($survey['thumbnailUrl']))
                                <img src="{{ $survey['thumbnailUrl'] }}" class="card-img-top"
                                    style="height:200px;object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height:200px;">
                                    <span class="text-muted">No Thumbnail</span>
                                </div>
                            @endif

                            {{-- Badge youtube --}}
                            @if ($survey['videoUrl'])
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    YouTube
                                </span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">

                            {{-- Title --}}
                            <h5 class="card-title mb-1">
                                {{ $survey['title'] }}
                            </h5>

                            {{-- Description --}}
                            <p class="text-muted small mb-2">
                                {{ \Illuminate\Support\Str::words(strip_tags($survey['content'] ?? '-'), 25) }}
                            </p>

                            {{-- Created --}}
                            <div class="small text-muted mb-3">
                                Dibuat {{ date('d M Y', strtotime($survey['createdAt'])) }}
                            </div>

                            <div class="mt-auto"></div>

                            {{-- Buttons --}}
                            <button class="btn btn-outline-primary w-100"
                                onclick='event.stopPropagation(); location.href="/artikel-parenting/{{ $survey['id'] }}/preview"'>
                                Preview
                            </button>

                            <button class="btn btn-outline-danger w-100 mt-2"
                                onclick="deletePromo('{{ $survey['id'] }}', this)">
                                Hapus
                            </button>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada Survey Layanan
                    </div>
                </div>
            @endforelse

        </div>


    </div>

    <div class="modal fade" id="addArticleModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Tambah Survey Layanan</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addArticleForm">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Survey</label>
                            <input type="text" class="form-control" id="survey_title" placeholder="Masukkan judul survey"
                                maxlength="200" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Survey</label>
                            <textarea class="form-control" id="survey_description" placeholder="Masukkan deskripsi survey"></textarea>
                        </div>

                        <hr>
                        <h6>Pertanyaan Survey</h6>

                        <div id="questionsContainer"></div>

                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addQuestion()">
                            + Tambah Pertanyaan
                        </button>


                        <div id="articleAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">
                            Simpan Survey
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

    <div class="modal fade" id="editSurveyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Survey</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editSurveyForm">

                    <input type="hidden" id="edit_id">

                    <div class="modal-body">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Judul Survey</label>
                            <input type="text" class="form-control" id="edit_title">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description"></textarea>
                        </div>

                        <!-- STATUS -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_isActive">
                            <label class="form-check-label">Survey Aktif</label>
                        </div>

                        <hr>

                        <h6>Pertanyaan Survey</h6>
                        <div id="edit_questions"></div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEditQuestion()">
                            + Tambah Pertanyaan
                        </button>

                        <div id="editAlert"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">
                            Update Survey
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <template id="questionTemplate">
        <div class="question-card border rounded p-3 mt-3">

            <div class="mb-2">
                <label>Pertanyaan</label>
                <input type="text" class="form-control question-text">
            </div>

            <div class="mb-2">
                <label>Tipe</label>
                <select class="form-select question-type">
                    <option value="TEXT">Text</option>
                    <option value="SINGLE_CHOICE">Single Choice</option>
                </select>
            </div>

            <div class="options-container d-none">
                <label>Opsi Jawaban</label>
                <div class="options"></div>
                <button type="button" class="btn btn-sm btn-secondary mt-2 add-option">
                    + Tambah Opsi
                </button>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input question-required" type="checkbox">
                <label class="form-check-label">Wajib diisi</label>
            </div>

            <button type="button" class="btn btn-sm btn-danger mt-2 remove-question">
                Hapus Pertanyaan
            </button>

        </div>
    </template>
@endsection

@section('own_script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function addQuestion() {
            let template = document.getElementById('questionTemplate').content.cloneNode(true);
            let card = template.querySelector('.question-card');

            let typeSelect = template.querySelector('.question-type');
            let optionsContainer = template.querySelector('.options-container');
            let optionsDiv = template.querySelector('.options');

            // change type
            typeSelect.addEventListener('change', function() {
                if (this.value === 'SINGLE_CHOICE') {
                    optionsContainer.classList.remove('d-none');
                } else {
                    optionsContainer.classList.add('d-none');
                    optionsDiv.innerHTML = '';
                }
            });

            // add option
            template.querySelector('.add-option').addEventListener('click', function() {
                let input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control mt-1 option-input';
                input.placeholder = 'Opsi jawaban';
                optionsDiv.appendChild(input);
            });

            // remove question
            template.querySelector('.remove-question').addEventListener('click', function() {
                card.remove();
            });

            document.getElementById('questionsContainer').appendChild(template);
        }

        function renderEditQuestion(q = {}) {

            let html = `
    <div class="card mb-3 question-item">
        <div class="card-body">

            <input class="form-control mb-2 question-text"
                placeholder="Pertanyaan"
                value="${q.question || ''}">

            <select class="form-select mb-2 question-type"
                onchange="toggleOptions(this)">
                <option value="TEXT" ${q.type==='TEXT'?'selected':''}>Text</option>
                <option value="SINGLE_CHOICE" ${q.type==='SINGLE_CHOICE'?'selected':''}>
                    Single Choice
                </option>
            </select>

            <div class="options">
                ${(q.options || []).map(opt =>
                    `<input class="form-control mb-1 option-input" value="${opt}">`
                ).join('')}
            </div>

            <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                onclick="addOption(this)">
                + Tambah Opsi
            </button>

            <div class="form-check mt-2">
                <input type="checkbox" class="form-check-input question-required"
                    ${q.isRequired?'checked':''}>
                <label class="form-check-label">Wajib diisi</label>
            </div>

            <button type="button" class="btn btn-sm btn-danger mt-2"
                onclick="$(this).closest('.question-item').remove()">
                Hapus
            </button>

        </div>
    </div>`;

            $('#edit_questions').append(html);
        }

        function addEditQuestion() {
            renderEditQuestion();
        }

        function addOption(btn) {
            $(btn).siblings('.options')
                .append('<input class="form-control mb-1 option-input" placeholder="Opsi">');
        }

        function toggleOptions(select) {
            let card = $(select).closest('.card');
            let options = card.find('.options');

            if (select.value === 'SINGLE_CHOICE') {
                options.show();
            } else {
                options.hide();
            }
        }


        $('#addArticleForm').on('submit', function(e) {
            e.preventDefault();

            let questions = [];

            $('#questionsContainer .question-card').each(function() {
                let qText = $(this).find('.question-text').val();
                let qType = $(this).find('.question-type').val();
                let required = $(this).find('.question-required').is(':checked');

                let q = {
                    question: qText,
                    type: qType,
                    isRequired: required
                };

                if (qType === 'SINGLE_CHOICE') {
                    q.options = [];
                    $(this).find('.option-input').each(function() {
                        if ($(this).val()) q.options.push($(this).val());
                    });
                }

                questions.push(q);
            });

            let payload = {
                title: $('#survey_title').val(),
                description: $('#survey_description').val(),
                isActive: true,

                questions: questions
            };

            $.ajax({
                url: '/survey-layanan',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },

                error: function(xhr) {
                    $('#articleAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.message || 'Gagal membuat survey'}
                </div>
            `);
                }
            });
        });
    </script>

    <script>
        function editSurvey(id) {
            $.get(`/survey-layanan/${id}`, function(res) {

                let s = res.data;

                $('#edit_id').val(s.id);
                $('#edit_title').val(s.title);
                $('#edit_description').val(s.description);
                $('#edit_isActive').prop('checked', s.isActive);

                $('#edit_questions').html('');

                s.questions.forEach(q => renderEditQuestion(q));

                $('#editSurveyModal').modal('show');
            });
        }


        $('#editSurveyForm').on('submit', function(e) {
            e.preventDefault();

            let questions = [];

            $('#edit_questions .question-item').each(function() {

                let q = {
                    question: $(this).find('.question-text').val(),
                    type: $(this).find('.question-type').val(),
                    isRequired: $(this).find('.question-required').is(':checked')
                };

                if (q.type === 'SINGLE_CHOICE') {
                    q.options = [];
                    $(this).find('.option-input').each(function() {
                        q.options.push($(this).val());
                    });
                }

                questions.push(q);
            });

            $.ajax({
                url: `/survey-layanan/${$('#edit_id').val()}`,
                method: 'PATCH',
                data: {
                    title: $('#edit_title').val(),
                    description: $('#edit_description').val(),
                    isActive: $('#edit_isActive').is(':checked'),
                    questions: JSON.stringify(questions),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: res => {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },

                error: xhr => {
                    $('#editAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.server || 'Gagal update survey'}
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
