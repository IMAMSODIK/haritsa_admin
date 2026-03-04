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
                <button class="btn btn-primary" style="margin-right: 5px" data-bs-toggle="modal"
                    data-bs-target="#modalGenerate">
                    <i class="fa fa-plus"></i> Generate Voucher
                </button>
                <button class="btn btn-success" style="margin-right: 5px" data-bs-toggle="modal"
                    data-bs-target="#modalImport">
                    <i class="fa fa-upload"></i> Import Voucher
                </button>
                <button class="btn btn-warning" style="margin-right: 5px" data-bs-toggle="modal"
                    data-bs-target="#modalAssign">
                    <i class="fa fa-user-plus"></i> Assign Voucher
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
                                        <th class="text-center">Prefix</th>
                                        <th class="text-center">Kode</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-center">Sumber</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Pengguna</th>
                                        <th class="text-center">Gambar</th>
                                        {{-- <th class="text-center" style="width: 120px;">Aksi</th> --}}
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($vouchers as $i => $voucher)
                                        <tr>
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
                                                    class="badge {{ $voucher['isActive'] == 'GENERATED' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $voucher['isActive'] == 'Aktif' ? 'Aktif' : 'Non Aktif' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if (!empty($voucher['userVouchers']))
                                                    <button class="btn btn-sm btn-primary btn-view-users"
                                                        data-code="{{ $voucher['code'] }}"
                                                        data-users='@json($voucher['userVouchers'])'>
                                                        Lihat ({{ count($voucher['userVouchers']) }})
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        Belum Ada
                                                    </button>
                                                @endif
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
                                            <td colspan="5" class="text-center text-muted">
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

    <div class="modal fade" id="modalVoucherUsers" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Pengguna Voucher: <span id="voucherCodeTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Assigned At</th>
                            </tr>
                        </thead>
                        <tbody id="voucherUserTableBody"></tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalGenerate">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Voucher</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addUserForm">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Quantity *</label>
                            <input type="number" name="quantity" class="form-control"
                                placeholder="Masukkan jumlah voucher yang akan di generate" min="1" max="5000"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Prefix (Optional)</label>
                            <input type="text" name="prefix" class="form-control" placeholder="Contoh: HRS">
                        </div>

                        <div class="mb-3">
                            <label>Panjang Kode (Optional)</label>
                            <input type="number" name="codeLength" class="form-control" min="4" max="20"
                                placeholder="Masukkan panjang kode voucher">
                        </div>

                        <div class="mb-3">
                            <label>Nominal *</label>
                            <input type="text" name="nominal" id="nominalInput" class="form-control" required
                                placeholder="Rp 0">
                        </div>

                        <div class="mb-3">
                            <label>Image (Optional)</label>
                            <input type="file" name="image" id="voucherImage" class="form-control"
                                accept="image/png,image/jpeg,image/jpg">

                            <!-- Preview -->
                            <div class="mt-3 text-center">
                                <img id="imagePreview" src="" class="img-fluid rounded d-none"
                                    style="max-height:200px;">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button id="generateVoucher" class="btn btn-primary w-100">
                            Generate Voucher
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImport">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Import Voucher Excel</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="importVoucherForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <!-- DOWNLOAD TEMPLATE -->
                        <div class="mb-3 text-center">
                            <a href="{{ route('voucher.template') }}" class="btn btn-outline-primary w-100">
                                <i class="fa fa-download"></i> Download Template Excel
                            </a>
                        </div>

                        <hr>

                        <!-- FILE UPLOAD -->
                        <div class="mb-3">
                            <label>Upload File Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">
                                Format: .xlsx / .xls
                            </small>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success w-100" id="btnImportVoucher">
                            Import Voucher
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAssign">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Assign Voucher ke User</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="assignVoucherForm">
                    @csrf
                    <div class="modal-body">

                        <!-- PILIH USER -->
                        <div class="mb-3">
                            <label>User *</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary w-100" id="btnOpenUserModal">
                                    Pilih User
                                </button>
                            </div>

                            <div id="selectedUsersContainer" class="mt-2"></div>
                        </div>

                        <hr>

                        <!-- MODE SELECT -->
                        <div class="mb-3">
                            <label>Mode Assign</label>
                            <select id="assignMode" class="form-control">
                                <option value="generate">Generate by Prefix & Nominal</option>
                                <option value="existing">Assign Existing Voucher</option>
                            </select>
                        </div>

                        <!-- GENERATE MODE -->
                        <div id="generateSection">
                            <div class="mb-3">
                                <label>Prefix</label>
                                <input type="text" name="prefix" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Nominal</label>
                                <input type="text" name="nominal_2" id="nominalInput2" class="form-control">
                            </div>
                        </div>

                        <!-- EXISTING MODE -->
                        <div id="existingSection" class="d-none">
                            <div class="mb-3">
                                <label>Voucher *</label>
                                <button type="button" class="btn btn-outline-primary w-100" id="btnOpenVoucherModal">
                                    Pilih Voucher
                                </button>

                                <div id="selectedVoucherContainer" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Quantity Per User</label>
                            <input type="number" name="quantityPerUser" class="form-control" min="1"
                                value="1" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning w-100" id="btnAssignVoucher">
                            Assign Voucher
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUserList" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Pilih User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered" id="userTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nama</th>
                                <th>No. Handphone</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnUseSelectedUsers">
                        Gunakan User Terpilih
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVoucherList" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Pilih Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <table id="voucherTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="40"></th>
                                <th>Code</th>
                                <th>Prefix</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody id="voucherTableBody"></tbody>
                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnUseSelectedVoucher">
                        Gunakan Voucher
                    </button>
                </div>

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

        $('#nominalInput2').on('input', function() {

            let value = $(this).val().replace(/[^0-9]/g, '');

            if (!value) {
                $(this).val('');
                return;
            }

            $(this).val(new Intl.NumberFormat('id-ID').format(value));

        });

        $('#assignMode').on('change', function() {

            if (this.value === 'generate') {
                $('#generateSection').removeClass('d-none');
                $('#existingSection').addClass('d-none');
            } else {
                $('#generateSection').addClass('d-none');
                $('#existingSection').removeClass('d-none');
            }

        });

        $('#voucherImage').on('change', function() {

            const file = this.files[0];
            const preview = $('#imagePreview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                    preview.removeClass('d-none');
                }

                reader.readAsDataURL(file);
            } else {
                preview.addClass('d-none');
                preview.attr('src', '');
            }

        });

        $('#nominalInput').on('input', function() {

            let value = this.value.replace(/\D/g, '');

            if (value === '') {
                this.value = '';
                return;
            }

            let formatted = new Intl.NumberFormat('id-ID').format(value);

            this.value = 'Rp ' + formatted;

        });
    </script>

    <script>
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $('#generateVoucher');
            btn.prop('disabled', true).text('Generating...');

            let nominalInput = $('#nominalInput');
            let rawValue = nominalInput.val().replace(/\D/g, '');
            nominalInput.val(rawValue);

            let form = document.getElementById('addUserForm');
            let fd = new FormData(form);

            $.ajax({
                url: '/vouchers/generate',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    $("#modalGenerate").modal('hide');
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Generate Voucher');

                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Gagal generate voucher',
                        'error'
                    );
                }
            });
        });
    </script>

    <script>
        $('#importVoucherForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $('#btnImportVoucher');
            btn.prop('disabled', true).text('Mengimport...');

            let fd = new FormData(this);

            $.ajax({
                url: '/vouchers/import',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Import Voucher');

                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Gagal import voucher',
                        'error'
                    );
                }
            });
        });
    </script>

    <script>
        let newSelected = [];
        let selectedUsers = [];
        let allVouchers = [];
        let selectedVouchers = [];
        let userTable;
        let voucherTable;


        // Buka modal voucher
        $('#btnOpenVoucherModal').click(function() {

            $('#modalAssign').modal('hide');
            $('#modalVoucherList').modal('show');

            loadVouchers();

        });


        // Load voucher dari server
        function loadVouchers() {

            if ($.fn.DataTable.isDataTable('#voucherTable')) {
                $('#voucherTable').DataTable().destroy();
            }

            voucherTable = $('#voucherTable').DataTable({
                ajax: {
                    url: '/vouchers/list',
                    dataSrc: function(json) {
                        return json.data ?? json;
                    }
                },
                columns: [{
                        data: null,
                        render: function(data) {

                            let checked = selectedVouchers.some(v => v.id === data.id) ?
                                'checked' :
                                '';

                            return `
                        <input type="checkbox"
                            class="voucher-checkbox"
                            value="${data.id}"
                            data-code="${data.code}"
                            ${checked}>
                    `;
                        },
                        orderable: false
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'prefix'
                    },
                    {
                        data: 'nominal',
                        render: function(data) {
                            return 'Rp ' + Number(data).toLocaleString();
                        }
                    }
                ]
            });

        }


        // Render table
        function renderVoucherTable() {

            let html = '';

            allVouchers.forEach(v => {

                let checked = selectedVouchers.some(s => s.id === v.id) ?
                    'checked' :
                    '';

                html += `
            <tr>
                <td>
                    <input type="checkbox"
                           class="voucher-checkbox"
                           value="${v.id}"
                           data-code="${v.code}"
                           ${checked}>
                </td>
                <td>${v.code}</td>
                <td>${v.prefix ?? '-'}</td>
                <td>Rp ${Number(v.nominal).toLocaleString()}</td>
            </tr>
        `;

            });

            $('#voucherTableBody').html(html);

        }


        // Klik gunakan voucher
        $('#btnUseSelectedVoucher').click(function() {

            $('.voucher-checkbox:checked').each(function() {

                let id = $(this).val();

                if (!selectedVouchers.some(v => v.id === id)) {

                    selectedVouchers.push({
                        id: id,
                        code: $(this).data('code')
                    });

                }

            });

            renderSelectedVoucher();

            $('#modalVoucherList').modal('hide');
            $('#modalAssign').modal('show');

        });


        // Render badge di modal assign
        function renderSelectedVoucher() {

            let html = '';

            selectedVouchers.forEach(v => {

                html += `
            <span class="badge bg-success me-1">
                ${v.code}
                <span style="cursor:pointer"
                      onclick="removeVoucher('${v.id}')">
                      &times;
                </span>
            </span>
        `;

            });

            $('#selectedVoucherContainer').html(html);

        }


        // Remove voucher
        function removeVoucher(id) {

            selectedVouchers = selectedVouchers.filter(v => v.id !== id);
            renderSelectedVoucher();

        }

        // Buka modal user
        $('#btnOpenUserModal').click(function() {

            $('#modalAssign').modal('hide');
            $('#modalUserList').modal('show');

            loadUsers();

        });


        // Load users dari endpoint
        function loadUsers() {

            if ($.fn.DataTable.isDataTable('#userTable')) {
                $('#userTable').DataTable().destroy();
            }

            userTable = $('#userTable').DataTable({
                ajax: {
                    url: '/get-users',
                    dataSrc: function(json) {
                        return json.data ?? json;
                    }
                },
                columns: [{
                        data: null,
                        render: function(data) {

                            let checked = selectedUsers.some(u => u.id === data.id) ?
                                'checked' :
                                '';

                            return `
                        <input type="checkbox"
                            class="user-checkbox"
                            value="${data.id}"
                            data-username="${data.username}"
                            data-phone="${data.phone}"
                            ${checked}>
                    `;
                        },
                        orderable: false
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'phone'
                    }
                ]
            });

        }

        $(document).on('change', '.user-checkbox', function() {

            let id = $(this).val();
            let username = $(this).data('username');
            let phone = $(this).data('phone');

            if ($(this).is(':checked')) {

                if (!selectedUsers.some(u => u.id === id)) {
                    selectedUsers.push({
                        id,
                        username,
                        phone
                    });
                }

            } else {

                selectedUsers = selectedUsers.filter(u => u.id !== id);

            }

        });

        $(document).on('change', '.voucher-checkbox', function() {

            let id = $(this).val();
            let code = $(this).data('code');

            if ($(this).is(':checked')) {

                if (!selectedVouchers.some(v => v.id === id)) {
                    selectedVouchers.push({
                        id,
                        code
                    });
                }

            } else {

                selectedVouchers = selectedVouchers.filter(v => v.id !== id);

            }

        });


        // Klik gunakan user
        $('#btnUseSelectedUsers').click(function() {

            let newSelected = [];

            $('.user-checkbox:checked').each(function() {

                newSelected.push({
                    id: $(this).val(),
                    username: $(this).data('username'),
                    phone: $(this).data('phone')
                });

            });

            // Gabungkan dengan yang lama TANPA DUPLIKAT
            newSelected.forEach(user => {

                if (!selectedUsers.some(u => u.id === user.id)) {
                    selectedUsers.push(user);
                }

            });

            renderSelectedUsers();

            $('#modalUserList').modal('hide');
            $('#modalAssign').modal('show');

        });

        function removeUser(id) {

            selectedUsers = selectedUsers.filter(u => u.id !== id);
            renderSelectedUsers();

        }


        // Tampilkan user terpilih di modal assign
        function renderSelectedUsers() {

            let html = '';

            selectedUsers.forEach(user => {

                html += `
            <span class="badge bg-primary me-1">
                ${user.username}
                <span style="cursor:pointer;"
                      onclick="removeUser('${user.id}')">
                    &times;
                </span>
            </span>
        `;

            });

            $('#selectedUsersContainer').html(html);

        }
    </script>

    <script>
        // load saat modal dibuka
        $('#modalAssign').on('shown.bs.modal', function() {
            loadUsers();
        });

        $('#assignVoucherForm').on('submit', function(e) {

            e.preventDefault();

            let btn = $('#btnAssignVoucher');
            btn.prop('disabled', true).text('Assigning...');

            let mode = $('#assignMode').val();

            // =============================
            // VALIDASI USER
            // =============================

            if (selectedUsers.length === 0) {
                btn.prop('disabled', false).text('Assign Voucher');
                Swal.fire('Warning', 'Pilih minimal 1 user', 'warning');
                return;
            }

            let payload = {
                userIds: selectedUsers.map(u => u.id),
                quantityPerUser: parseInt($('[name="quantityPerUser"]').val())
            };

            // =============================
            // MODE GENERATE
            // =============================

            if (mode === 'generate') {

                let prefix = $('#assignVoucherForm [name="prefix"]').val()?.trim();
                let nominalRaw = $('#assignVoucherForm [name="nominal_2"]').val()?.trim();

                if (!prefix || prefix.length === 0) {
                    btn.prop('disabled', false).text('Assign Voucher');
                    Swal.fire('Warning', 'Prefix wajib diisi', 'warning');
                    return;
                }

                if (!nominalRaw || nominalRaw.length === 0) {
                    btn.prop('disabled', false).text('Assign Voucher');
                    Swal.fire('Warning', 'Nominal wajib diisi', 'warning');
                    return;
                }
                
                payload.prefix = prefix;
                payload.nominal = parseInt(
                    nominalRaw.toString().replace(/[^0-9]/g, '')
                );

            }

            // =============================
            // MODE EXISTING
            // =============================

            if (mode === 'existing') {

                if (selectedVouchers.length === 0) {
                    btn.prop('disabled', false).text('Assign Voucher');
                    Swal.fire('Warning', 'Pilih minimal 1 voucher', 'warning');
                    return;
                }

                payload.voucherIds = selectedVouchers.map(v => v.id);

            }

            // =============================
            // SUBMIT AJAX
            // =============================

            $.ajax({
                url: '/vouchers/assign',
                method: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {

                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => location.reload());

                },
                error: function(xhr) {

                    btn.prop('disabled', false).text('Assign Voucher');

                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Gagal assign voucher',
                        'error'
                    );

                }
            });

        });

        $(document).on('click', '.btn-view-users', function() {

            let users = $(this).data('users');
            let code = $(this).data('code');

            $('#voucherCodeTitle').text(code);

            let html = '';

            users.forEach((item, index) => {

                html += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.user?.username ?? '-'}</td>
                <td>
                    <span class="badge bg-info">
                        ${item.status}
                    </span>
                </td>
                <td>${new Date(item.assignedAt).toLocaleString()}</td>
            </tr>
        `;

            });

            $('#voucherUserTableBody').html(html);

            $('#modalVoucherUsers').modal('show');

        });
    </script>
@endsection
