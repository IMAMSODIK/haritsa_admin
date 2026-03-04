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
                    {{-- <div class="card h-100 shadow-sm border-0 survey-card" style="cursor:pointer"
                        data-survey='@json($survey)' onclick="openEditSurvey(this)"> --}}
                    <div class="card h-100 shadow-sm border-0 survey-card" style="cursor:pointer"
                        data-survey='@json($survey)'>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">
                                {{ $survey['title'] }}
                            </h5>
                            <p class="text-muted small mb-2">
                                {{ \Illuminate\Support\Str::words(strip_tags($survey['description'] ?? '-'), 25) }}
                            </p>
                            <div class="small text-muted mb-3">
                                Dibuat {{ date('d M Y', strtotime($survey['createdAt'])) }}
                            </div>

                            <div class="mt-auto"></div>

                            <button class="btn btn-outline-primary w-100"
                                onclick="event.stopPropagation(); previewSurvey({{ json_encode($survey) }})">
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

                        <div class="mb-3">
                            <label class="form-label">Trigger Survey</label>
                            <select class="form-select" id="trigger_type" required>
                                <option value="AFTER_TRANSACTION">After Transaction</option>
                                <option value="AFTER_VOUCHER_CLAIM">After Voucher Claim</option>
                                <option value="PERIOD">Period</option>
                                <option value="EVENT">Event</option>
                            </select>
                        </div>

                        <div id="periodFields" class="row d-none">
                            <div class="col-md-6">
                                <label>Periode Mulai</label>
                                <input type="datetime-local" class="form-control" id="period_start">
                            </div>
                            <div class="col-md-6">
                                <label>Periode Selesai</label>
                                <input type="datetime-local" class="form-control" id="period_end">
                            </div>
                        </div>

                        <div id="eventField" class="mt-3 d-none">
                            <label>Event Code</label>
                            <input type="text" class="form-control" id="event_code">
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

    <div class="modal fade" id="previewSurveyModal">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="previewSurveyTitle"></h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- description -->
                    <p id="previewSurveyDesc" class="text-muted mb-3"></p>

                    <!-- meta -->
                    <div class="small text-muted mb-3">
                        Status:
                        <span id="previewSurveyStatus" class="badge"></span>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Pertanyaan Survey</h6>

                    <div id="previewSurveyQuestions"></div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editSurveyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Edit Survey</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editSurveyForm">

                    <div class="modal-body">

                        <input type="hidden" id="edit_survey_id">

                        <div class="mb-3">
                            <label>Judul</label>
                            <input type="text" class="form-control" id="edit_survey_title">
                        </div>

                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" id="edit_survey_description"></textarea>
                        </div>

                        <!-- trigger, period, event sama seperti modal create -->
                        <!-- questionsContainer versi edit -->
                        <div id="editQuestionsContainer"></div>

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
                <label>Kategori</label>
                <input type="text" class="form-control question-category">
            </div>

            <div class="mb-2">
                <label>Pertanyaan</label>
                <input type="text" class="form-control question-text">
            </div>

            <div class="mb-2">
                <label>Tipe</label>
                <select class="form-select question-type">
                    <option value="LIKERT">Likert</option>
                    <option value="TEXT">Text</option>
                </select>
            </div>

            <div class="likert-container">
                <label>Skala Likert</label>
                <input type="number" class="form-control likert-scale" value="5" min="2" max="10">
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

    <template id="editQuestionTemplate">
        <div class="question-card border rounded p-3 mb-3">

            <div class="mb-2">
                <label>Kategori</label>
                <input type="text" class="form-control question-category">
            </div>

            <div class="mb-2">
                <label>Pertanyaan</label>
                <input type="text" class="form-control question-text">
            </div>

            <div class="mb-2">
                <label>Tipe</label>
                <select class="form-select question-type">
                    <option value="TEXT">Text</option>
                    <option value="LIKERT">Likert</option>
                </select>
            </div>

            <div class="mb-2 likert-container">
                <label>Skala Likert</label>
                <input type="number" class="form-control likert-scale" min="2" max="10" value="5">
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input question-required">
                <label class="form-check-label">Wajib</label>
            </div>

            <button type="button" class="btn btn-sm btn-danger mt-2 remove-question">
                Hapus
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

        function formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleString('id-ID');
        }

        $('#trigger_type').on('change', function() {
            let type = this.value;

            $('#periodFields').addClass('d-none');
            $('#eventField').addClass('d-none');

            if (type === 'PERIOD') {
                $('#periodFields').removeClass('d-none');
            }

            if (type === 'EVENT') {
                $('#eventField').removeClass('d-none');
            }
        });

        function addQuestion() {
            let template = document.getElementById('questionTemplate').content.cloneNode(true);
            let card = template.querySelector('.question-card');

            let typeSelect = template.querySelector('.question-type');
            let likertContainer = template.querySelector('.likert-container');

            typeSelect.addEventListener('change', function() {
                if (this.value === 'LIKERT') {
                    likertContainer.style.display = 'block';
                } else {
                    likertContainer.style.display = 'none';
                }
            });

            template.querySelector('.remove-question').addEventListener('click', function() {
                card.remove();
            });

            document.getElementById('questionsContainer').appendChild(template);
        }

        function addEditQuestion(q = {}) {
            let template = document
                .getElementById('editQuestionTemplate')
                .content.cloneNode(true);

            let container = document.getElementById('editQuestionsContainer');

            let card = template.querySelector('.question-card');
            let category = template.querySelector('.question-category');
            let text = template.querySelector('.question-text');
            let type = template.querySelector('.question-type');
            let required = template.querySelector('.question-required');
            let likertContainer = template.querySelector('.likert-container');
            let likertScale = template.querySelector('.likert-scale');

            // SET DATA
            category.value = q.category || '';
            text.value = q.question || '';
            type.value = q.type || 'TEXT';
            required.checked = q.isRequired || false;

            function toggleLikert() {
                if (type.value === 'LIKERT') {
                    likertContainer.style.display = 'block';
                } else {
                    likertContainer.style.display = 'none';
                }
            }

            toggleLikert();

            if (q.type === 'LIKERT') {
                likertScale.value = q.likertScale || 5;
            }

            type.addEventListener('change', toggleLikert);

            template.querySelector('.remove-question')
                .addEventListener('click', () => card.remove());

            container.appendChild(template);
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
                let type = $(this).find('.question-type').val();

                let q = {
                    category: $(this).find('.question-category').val(),
                    question: $(this).find('.question-text').val(),
                    type: type,
                    isRequired: $(this).find('.question-required').is(':checked')
                };

                if (type === 'LIKERT') {
                    q.likertScale = parseInt($(this).find('.likert-scale').val());
                }

                questions.push(q);
            });

            let payload = {
                title: $('#survey_title').val(),
                description: $('#survey_description').val(),
                isActive: true,
                triggerType: $('#trigger_type').val(),
                questions: questions
            };

            // PERIOD
            if (payload.triggerType === 'PERIOD') {
                payload.periodStartAt = new Date($('#period_start').val()).toISOString();
                payload.periodEndAt = new Date($('#period_end').val()).toISOString();
            }

            // EVENT
            if (payload.triggerType === 'EVENT') {
                payload.eventCode = $('#event_code').val();
            }

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
        function openEditSurvey(card) {

            let survey = $(card).data('survey');
            console.log(survey);

            if (!survey) return;

            $('#editSurveyForm')[0].reset();
            $('#editQuestionsContainer').empty();

            $('#edit_survey_id').val(survey.id);
            $('#edit_survey_title').val(survey.title);
            $('#edit_survey_description').val(survey.description);

            $('#edit_trigger_type')
                .val(survey.triggerType)
                .trigger('change');

            if (survey.triggerType === 'PERIOD') {
                $('#edit_period_start').val(formatForInput(survey.periodStartAt));
                $('#edit_period_end').val(formatForInput(survey.periodEndAt));
            }

            if (survey.triggerType === 'EVENT') {
                $('#edit_event_code').val(survey.eventCode);
            }

            if (survey.questions && survey.questions.length > 0) {
                survey.questions.forEach(q => {
                    addEditQuestion(q);
                });
            }

            $('#editSurveyModal').modal('show');
        }

        $('#editSurveyForm').on('submit', function(e) {
            e.preventDefault();

            let surveyId = $('#edit_survey_id').val();

            let questions = [];

            $('#editQuestionsContainer .question-card').each(function() {

                let type = $(this).find('.question-type').val();

                let q = {
                    category: $(this).find('.question-category').val(),
                    question: $(this).find('.question-text').val(),
                    type: type,
                    isRequired: $(this).find('.question-required').is(':checked')
                };

                if (type === 'LIKERT') {
                    q.likertScale = parseInt($(this).find('.likert-scale').val());
                }

                questions.push(q);
            });

            let payload = {
                title: $('#edit_survey_title').val(),
                description: $('#edit_survey_description').val(),
                isActive: true,
                triggerType: $('#edit_trigger_type').val(),
                questions: questions
            };

            if (payload.triggerType === 'PERIOD') {
                payload.periodStartAt = new Date($('#edit_period_start').val()).toISOString();
                payload.periodEndAt = new Date($('#edit_period_end').val()).toISOString();
            }

            if (payload.triggerType === 'EVENT') {
                payload.eventCode = $('#edit_event_code').val();
            }

            $.ajax({
                url: `/survey-layanan/${surveyId}`,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Gagal', xhr.responseJSON?.message || 'Update gagal', 'error');
                }
            });
        });

        function previewSurvey(survey) {

            // ===== BASIC INFO =====
            $('#previewSurveyTitle').text(survey.title || '-');
            $('#previewSurveyDesc').text(survey.description || '-');

            $('#previewSurveyStatus')
                .text(survey.isActive ? 'Aktif' : 'Nonaktif')
                .removeClass('bg-success bg-secondary')
                .addClass(survey.isActive ? 'bg-success' : 'bg-secondary');

            // ===== CLEAR QUESTIONS =====
            let container = $('#previewSurveyQuestions');
            container.empty();

            // ===== TRIGGER INFO =====
            let triggerInfo = '';

            switch (survey.triggerType) {
                case 'PERIOD':
                    triggerInfo = `
                <div class="small text-muted mb-3">
                    Trigger: PERIOD<br>
                    ${formatDate(survey.periodStartAt)} 
                    s/d 
                    ${formatDate(survey.periodEndAt)}
                </div>
            `;
                    break;

                case 'EVENT':
                    triggerInfo = `
                <div class="small text-muted mb-3">
                    Trigger: EVENT<br>
                    Event Code: ${survey.eventCode || '-'}
                </div>
            `;
                    break;

                default:
                    triggerInfo = `
                <div class="small text-muted mb-3">
                    Trigger: ${survey.triggerType || '-'}
                </div>
            `;
            }

            container.append(triggerInfo);

            if (!survey.questions || survey.questions.length === 0) {
                container.append(`<div class="text-muted">Tidak ada pertanyaan</div>`);
                $('#previewSurveyModal').modal('show');
                return;
            }

            // ===== RENDER QUESTIONS =====
            survey.questions.forEach((q, i) => {

                let card = `
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">

                <div class="fw-bold mb-1">
                    ${i + 1}. ${q.question || '-'}
                </div>

                <div class="small text-muted mb-2">
                    Kategori: ${q.category || '-'}
                    ${q.isRequired ? ' • Wajib diisi' : ''}
                </div>
        `;

                // ===== TEXT =====
                if (q.type === 'TEXT') {
                    card += `
                <div class="form-control bg-light text-muted">
                    Jawaban teks bebas
                </div>
            `;
                }

                // ===== LIKERT =====
                if (q.type === 'LIKERT') {

                    let scale = q.likertScale || 5;

                    card += `<div class="d-flex gap-3 flex-wrap">`;

                    for (let s = 1; s <= scale; s++) {
                        card += `
                    <span class="badge bg-light text-dark border">
                        ${s}
                    </span>
                `;
                    }

                    card += `</div>
                <div class="small text-muted mt-2">
                    Skala 1 - ${scale}
                </div>
            `;
                }

                card += `</div></div>`;

                container.append(card);
            });

            $('#previewSurveyModal').modal('show');
        }

        function deletePromo(id, btn) {
            event.stopPropagation();

            Swal.fire({
                title: 'Yakin ingin menghapus Survey?',
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
                        url: `/survey-layanan/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire('Terhapus!', res.message, 'success');
                            $(btn).closest('.col-md-4').remove();
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON?.server || 'Gagal menghapus Survey',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
