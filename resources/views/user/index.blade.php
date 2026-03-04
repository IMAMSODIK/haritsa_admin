@extends('layouts.template')

@section('own_style')
    <style>
        .strength-weak {
            color: red;
        }

        .strength-medium {
            color: orange;
        }

        .strength-strong {
            color: green;
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa fa-plus"></i> Tambah Pengguna
                </button>
            </div>
        </div>

        <div class="row g-4">
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="table-container table-responsive">
                            <table id="dataTable" class="table table-striped table-hover align-middle">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">No</th>
                                        <th class="text-center">Username</th>
                                        <th class="text-center">No. Handphone</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($users as $i => $user)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $user['username'] }}</td>
                                            <td class="text-center">{{ $user['phone'] }}</td>
                                            <td class="text-center">
                                                <span id="status-badge-{{ $user['id'] }}"
                                                    class="badge {{ $user['isActive'] ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $user['isActive'] ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2 flex-nowrap">

                                                    @if ($user['isActive'])
                                                        <button onclick="activate('{{ $user['id'] }}', this, false)"
                                                            class="btn btn-sm btn-warning d-flex align-items-center gap-1">
                                                            <i class="fa fa-ban"></i>
                                                            <span>Deactivate</span>
                                                        </button>
                                                    @else
                                                        <button onclick="activate('{{ $user['id'] }}', this, true)"
                                                            class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                                            <i class="fa fa-check-circle"></i>
                                                            <span>Activate</span>
                                                        </button>
                                                    @endif

                                                    {{-- <a href="{{ route('users.show', $user['id']) }}">
                                                        <button
                                                            class="btn btn-sm btn-info text-white d-flex align-items-center gap-1">
                                                            <i class="fa fa-info-circle"></i>
                                                            <span>Detail</span>
                                                        </button>
                                                    </a> --}}

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                Tidak ada data pengguna
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalTambah">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addUserForm">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Role</label>
                            <select id="userRole" class="form-control" required>
                                <option value="">-- Pilih Role --</option>

                                @foreach ($roles as $role)
                                    <option value="{{ $role['id'] }}">
                                        {{ $role['name'] }}
                                    </option>
                                @endforeach

                            </select>
                        </div>


                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" id="username" class="form-control" required
                                placeholder="Masukkan username">
                        </div>

                        <div class="mb-3">
                            <label>No HP</label>
                            <input type="text" id="phone" class="form-control" required
                                placeholder="Masukkan Nomor Handphone">
                        </div>

                        <div class="mb-3">
                            <label>Password</label>

                            <div class="input-group">
                                <input type="password" id="password" class="form-control" required
                                    placeholder="Masukkan password">

                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    👁
                                </button>
                            </div>

                            <small id="passwordStrength" class="text-muted"></small>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button id="btnSubmitUser" class="btn btn-primary w-100">
                            Tambah User
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('own_script')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                autoWidth: false
            });
        });
    </script>
    <script>
        function activate(id, btn, val) {

            Swal.fire({
                title: 'Deactivate user?',
                text: 'User akan dinonaktifkan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, deactivate',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then((result) => {

                if (!result.isConfirmed) return;

                let $btn = $(btn);
                $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: '/users/' + id + '/' + val,
                    method: 'put',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Status berhasil diubah',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        let badge = $('#status-badge-' + id);

                        if (val === false || val === "false") {
                            $btn.removeClass('btn-warning')
                                .addClass('btn-success')
                                .html('<i class="fa fa-check-circle"></i> <span>Activate</span>')
                                .attr('onclick', `activate('${id}', this, true)`);

                            badge.removeClass('bg-success')
                                .addClass('bg-danger')
                                .text('Nonaktif');

                        } else {
                            $btn.removeClass('btn-success')
                                .addClass('btn-warning')
                                .html('<i class="fa fa-ban"></i> <span>Deactivate</span>')
                                .attr('onclick', `activate('${id}', this, false)`);

                            badge.removeClass('bg-danger')
                                .addClass('bg-success')
                                .text('Aktif');
                        }

                        $btn.prop('disabled', false);
                    },
                    error: function(xhr) {

                        Swal.fire(
                            'Gagal',
                            xhr.responseJSON?.message || 'Server error',
                            'error'
                        );

                        $btn.prop('disabled', false)
                            .html('<i class="fa fa-ban"></i> <span>Deactivate</span>');
                    }
                });

            });
        }

        $('#password').on('input', function() {
            let val = $(this).val();
            let strength = $('#passwordStrength');

            if (val.length < 6) {
                strength.text('Lemah').attr('class', 'strength-weak');
            } else if (val.match(/[A-Z]/) && val.match(/[0-9]/)) {
                strength.text('Kuat').attr('class', 'strength-strong');
            } else {
                strength.text('Sedang').attr('class', 'strength-medium');
            }
        });

        $('#phone').on('input', function() {
            let val = $(this).val().replace(/\D/g, '');

            if (val.startsWith('0')) {
                val = '62' + val.substring(1);
            }

            $(this).val(val);
        });

        $('#togglePassword').click(function() {
            let input = $('#password');
            let type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
        });

        $('#username, #phone, #password').on('input', function() {
            if ($(this).val().length > 0) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });

        $('#addUserForm').submit(function(e) {
            e.preventDefault();

            let btn = $('#btnSubmitUser');
            let roleId = $('#userRole').val();

            if (!roleId) {
                Swal.fire('Error', 'Pilih role dulu', 'warning');
                return;
            }

            // 6. Loading spinner
            btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

            $.ajax({
                url: '/users/' + roleId,
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({
                    username: $('#username').val(),
                    phone: $('#phone').val(),
                    password: $('#password').val()
                }),
                success: function(res) {
                    $('#addUserForm')[0].reset();
                    $('#modalTambah').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(() => location.reload(), 1500);

                },
                error: function(xhr) {

                    Swal.fire(
                        'Gagal',
                        xhr.responseJSON?.message || 'Server error',
                        'error'
                    );
                },
                complete: function() {
                    btn.prop('disabled', false)
                        .text('Tambah User');
                }
            });
        });
    </script>
@endsection
