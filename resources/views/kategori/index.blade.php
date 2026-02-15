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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                    <i class="fa fa-plus"></i> Tambah Kategori Produk
                </button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="table-container table-responsive">
                    <table id="dataTable" class="table table-striped table-hover" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Kategori</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $index = 1; @endphp

                            @foreach ($kategories as $kategorie)
                                <tr>
                                    <td class="text-center align-middle">{{ $index++ }}</td>

                                    <td class="align-middle">{{ $kategorie->name }}</td>

                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-danger btn-sm delete" onclick="editStore('{{ $kategorie->id }}')">
                                                Hapus
                                            </button>
                                            <button class="btn btn-danger btn-sm delete" id="deleteStoreBtn" data-id="{{ $kategorie->id }}">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="addStoreModal">
        <div class="modal-dialog modal-lg">
            <form id="storeForm">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div id="storeAlert"></div>

                        <div class="mb-3">
                            <label>Nama Kategori</label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="Masukkan nama Kategori">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editStoreModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Edit Kategori</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editStoreForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        <input type="hidden" id="edit_id">

                        <input class="form-control mb-2" id="edit_name" placeholder="Nama Store">

                        <div id="editAlert" class="mt-2"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100 mb-1">Update Kategori</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@section('own_script')
    <script>
        $('#storeForm').on('submit', function(e) {
            e.preventDefault();
            $.post("{{ route('kategori.store') }}", $(this).serialize(), function(res) {
                location.reload();
            });
        });

        function editStore(id) {
            document.body.style.cursor = 'wait';

            $.get(`/categories/${id}`, function(res) {

                let kat = res.data;

                $('#edit_id').val(kat.id);
                $('#edit_name').val(kat.name);

                let modal = new bootstrap.Modal(document.getElementById('editStoreModal'));
                modal.show();

                $('#editStoreModal').one('shown.bs.modal', function() {
                    document.body.style.cursor = 'default';
                });

            }).fail(function(xhr) {
                document.body.style.cursor = 'default';

                alert("Gagal mengambil data Kategori");
            });
        }

        $('#editStoreForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#edit_id').val();
            let btn = $('#editStoreForm button[type="submit"]');

            btn.prop('disabled', true).text('Updating...');
            document.body.style.cursor = 'wait';

            $.ajax({
                url: `/categories/${id}`,
                method: 'PATCH',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: $('#edit_name').val(),
                },

                success: function(res) {

                    document.body.style.cursor = 'default';

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kategori berhasil diupdate',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });

                },

                error: function(xhr) {

                    document.body.style.cursor = 'default';
                    btn.prop('disabled', false).text('Update Kategori');

                    let msg =
                        xhr.responseJSON?.debug ||
                        xhr.responseJSON?.server ||
                        xhr.responseText ||
                        "Update gagal";

                    $('#editAlert').html(
                        `<div class="alert alert-danger">${msg}</div>`
                    );
                }
            });
        });
    </script>

    <script>
        $('#deleteStoreBtn').on('click', function() {

            let id = $(this).data('id');

            // tutup modal sementara
            let modalEl = document.getElementById('editStoreModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            Swal.fire({
                title: 'Hapus Brand?',
                text: 'Data tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) {
                    modal.show();
                    return;
                }

                // cursor loading
                document.body.style.cursor = 'wait';

                $.ajax({
                    url: `/categories/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function() {

                        document.body.style.cursor = 'default';

                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Kategori berhasil dihapus',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });

                    },

                    error: function(xhr) {

                        document.body.style.cursor = 'default';

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Tidak bisa menghapus Kategori',
                        });
                    }
                });

            });

        });
    </script>
@endsection
