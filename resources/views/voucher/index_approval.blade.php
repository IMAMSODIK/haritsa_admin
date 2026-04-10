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
                        <li class="breadcrumb-item"><a href="/dashboard">
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
                <button id="btnApprove" class="btn btn-primary" style="margin-right: 5px; display:none;">
                    <i class="fa fa-check-circle"></i> Approve Voucher
                </button>
                <button id="btnTolak" class="btn btn-danger" style="margin-right: 5px; display:none;">
                    <i class="fa fa-times-circle"></i> Batal Approve Voucher
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
                                        <th class="text-center" style="width:40px;">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                        <th class="text-center" style="width: 60px;">No</th>
                                        <th class="text-center">Prefix</th>
                                        <th class="text-center">Kode</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-center">Sumber</th>
                                        <th class="text-center">Status Approval</th>
                                        <th class="text-center">Tanggal Kadaluarsa</th>
                                        <th class="text-center">Gambar</th>
                                        {{-- <th class="text-center" style="width: 120px;">Aksi</th> --}}
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($vouchers as $i => $voucher)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="row-check" value="{{ $voucher['id'] }}">
                                            </td>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td class="text-center">{{ $voucher['prefix'] }}</td>
                                            <td class="text-center">{{ $voucher['code'] }}</td>
                                            <td class="text-end">
                                                Rp {{ number_format($voucher['nominal'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $voucher['source'] == 'GENERATED' ? 'bg-info' : 'bg-warning' }}">
                                                    {{ $voucher['source'] == 'GENERATED' ? 'GENERATED' : 'IMPORT' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $voucher['isApproved'] ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $voucher['isApproved'] ? 'Sudah Approval' : 'Belum Approval' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($voucher['expiryDate'])->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($voucher['imageUrl'])
                                                    <img src="{{ $voucher['imageUrl'] }}" width="150px" alt="">
                                                @else
                                                    <img src="https://jkfenner.com/wp-content/uploads/2019/11/default.jpg"
                                                        width="150px" alt="">
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                Tidak ada data voucher
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
@endsection

@section('own_script')
    <script>
        $(document).ready(function() {

            let table = $('#dataTable').DataTable();

            let selectedIds = new Set();
            let isCheckAll = false;

            // ===============================
            // CHECK ALL
            // ===============================
            $('#checkAll').on('click', function() {

                isCheckAll = $(this).prop('checked');

                if (isCheckAll) {

                    // ambil semua checkbox dari semua halaman
                    table.rows().every(function() {

                        let node = this.node();
                        let checkbox = $(node).find('.row-check');

                        if (checkbox.length) {
                            let id = checkbox.val();
                            selectedIds.add(id);
                            checkbox.prop('checked', true);
                        }

                    });

                } else {

                    selectedIds.clear();
                    $('.row-check').prop('checked', false);

                }

                toggleApproveButton();
            });

            // ===============================
            // CHECKBOX PER ROW
            // ===============================
            $(document).on('change', '.row-check', function() {

                let id = $(this).val();

                if ($(this).prop('checked')) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                    isCheckAll = false;
                    $('#checkAll').prop('checked', false);
                }

                toggleApproveButton();
            });

            // ===============================
            // SAAT PINDAH HALAMAN DATATABLE
            // ===============================
            table.on('draw', function() {

                $('.row-check').each(function() {

                    let id = $(this).val();

                    if (isCheckAll) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', selectedIds.has(id));
                    }

                });

            });

            // ===============================
            // SHOW / HIDE BUTTON
            // ===============================
            function toggleApproveButton() {

                if (selectedIds.size > 0) {
                    $('#btnApprove').show();
                    $('#btnTolak').show();
                } else {
                    $('#btnApprove').hide();
                    $('#btnTolak').hide();
                }

            }

            // ===============================
            // APPROVE
            // ===============================
            $('#btnApprove').click(function() {

                let payload = {
                    status: true,
                    voucherId: Array.from(selectedIds)
                };

                if (payload.voucherId.length === 0) {
                    Swal.fire('Warning', 'Pilih voucher dulu', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Approve Voucher?',
                    text: payload.voucherId.length + ' voucher akan di approve',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Approve'
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/voucher/approval',
                        method: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(payload),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        beforeSend: function() {
                            $('#btnApprove').prop('disabled', true);
                        },

                        success: function() {
                            Swal.fire(
                                'Berhasil!',
                                'Voucher berhasil di approve',
                                'success'
                            ).then(() => location.reload());
                        },

                        error: function(err) {
                            console.log(err);
                            Swal.fire('Error', 'Terjadi kesalahan', 'error');
                            $('#btnApprove').prop('disabled', false);
                        }

                    });

                });

            });

            // ===============================
            // TOLAK / BATAL APPROVE
            // ===============================
            $('#btnTolak').click(function() {

                let payload = {
                    status: false,
                    voucherId: Array.from(selectedIds)
                };

                if (payload.voucherId.length === 0) {
                    Swal.fire('Warning', 'Pilih voucher dulu', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Batalkan Approve?',
                    text: payload.voucherId.length + ' voucher akan dibatalkan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Batalkan'
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/voucher/approval',
                        method: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(payload),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        beforeSend: function() {
                            $('#btnTolak').prop('disabled', true);
                        },

                        success: function() {
                            Swal.fire(
                                'Berhasil!',
                                'Approval voucher dibatalkan',
                                'success'
                            ).then(() => location.reload());
                        },

                        error: function(err) {
                            console.log(err);
                            Swal.fire('Error', 'Terjadi kesalahan', 'error');
                            $('#btnTolak').prop('disabled', false);
                        }

                    });

                });

            });

        });
    </script>
@endsection
