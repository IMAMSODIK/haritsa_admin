@extends('layouts.template')

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
                                    <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                    <i class="fa fa-plus"></i> Tambah Banner
                </button>
            </div>
        </div>

        <div class="row">

            @forelse($banners as $banner)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">

                        <img src="{{ $banner['fileUrl'] }}" class="card-img-top banner-preview"
                            style="height:200px; object-fit:cover; cursor:pointer;" data-img="{{ $banner['fileUrl'] }}">

                        <div class="card-body d-flex flex-column">

                            <small class="text-muted mb-3">
                                {{ \Carbon\Carbon::parse($banner['createdAt'])->format('d M Y') }}
                            </small>

                            <button type="button" class="btn btn-danger btn-sm w-100 btn-delete"
                                data-id="{{ $banner['id'] }}">
                                Hapus Banner
                            </button>


                        </div>

                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Tidak ada banner
                    </div>
                </div>
            @endforelse

        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">

                <div class="modal-header border-0">
                    <h6 class="modal-title text-white">Preview Banner</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid rounded shadow" style="max-height:80vh;">
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Yakin ingin menghapus banner ini?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <form method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addBannerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="bannerForm" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Banner</label>
                            <input type="text" class="form-control banner-name" placeholder="Nama banner 1">
                        </div>

                        <div class="mb-3">
                            <label>Upload Gambar</label>
                            <input type="file" class="form-control banner-file" multiple required>
                        </div>

                        <div id="bannerAlert"></div>

                        <button class="btn btn-success w-100">Upload Banner</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection

@section('own_script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    </script>

    <script>
        $('#bannerForm').on('submit', function(e) {
            e.preventDefault();

            let fileInput = $('.banner-file')[0];

            if (!fileInput.files.length) {
                $('#bannerAlert').html(
                    '<div class="alert alert-danger">Pilih gambar terlebih dahulu</div>'
                );
                return;
            }

            let formData = new FormData();
            formData.append('banner', fileInput.files[0]);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: "{{ route('banner.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {
                    $('#bannerAlert').html(
                        '<div class="alert alert-success">' + res.message + '</div>'
                    );
                    setTimeout(() => location.reload(), 1000);
                },

                error: function(xhr) {
                    let msg =
                        xhr.responseJSON?.debug ||
                        xhr.responseJSON?.server ||
                        xhr.responseJSON?.message ||
                        xhr.responseText ||
                        "Upload gagal";

                    $('#bannerAlert').html(
                        `<div class="alert alert-danger">${msg}</div>`
                    );
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '.banner-preview', function() {

            let imgUrl = $(this).data('img');

            $('#modalImage').attr('src', imgUrl);

            let modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        });
    </script>

    <script>
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Hapus banner?',
                text: 'Banner akan dihapus permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/banner/' + encodeURIComponent(id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire('Terhapus!', res.message, 'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', xhr.responseJSON?.message || 'Server error',
                                'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
