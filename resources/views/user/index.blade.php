@extends('layouts.template')

@section('own_style')
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
                            <table id="dataTable" class="table table-striped table-hover" style="width:100%">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th>Username</th>
                                        <th>No. Handphone</th>
                                        <th>Role</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php $index = 1; @endphp


                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="addUserModal">
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
                            <select id="userRole" class="form-control" required></select>
                        </div>

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" id="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>No HP</label>
                            <input type="text" id="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" id="password" class="form-control" required>
                            <small class="text-muted">
                                Minimal 8 karakter, huruf & angka
                            </small>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function validPassword(pw) {
            return pw.length >= 8 &&
                /[A-Za-z]/.test(pw) &&
                /[0-9]/.test(pw);
        }

        $('#addUserModal').on('show.bs.modal', function() {

            // reset form
            $('#addUserForm')[0].reset();
            $('#userRole').html('<option>Loading...</option>');

            $.get('/users/roles', function(res) {

                let select = $('#userRole');
                select.empty();
                select.append('<option value="">Pilih Role</option>');

                res.data.forEach(role => {
                    select.append(
                        `<option value="${role.id}">
                    ${role.name} - ${role.description}
                </option>`
                    );
                });

            }).fail(function() {
                Swal.fire('Error', 'Gagal mengambil role', 'error');
            });

        });


        $('#addUserForm').submit(function(e) {
            e.preventDefault();

            let roleId = $('#userRole').val();

            if (!roleId) {
                Swal.fire('Error', 'Pilih role dulu', 'warning');
                return;
            }

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

                    Swal.fire('Berhasil', res.message, 'success')
                        .then(() => location.reload());

                },
                error: function(xhr) {

                    Swal.fire(
                        'Gagal',
                        xhr.responseJSON?.message || 'Server error',
                        'error'
                    );
                }
            });

        });
    </script>
@endsection
