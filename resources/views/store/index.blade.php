@extends('layouts.template')

@section('own_style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
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
                    <i class="fa fa-plus"></i> Tambah Store
                </button>
            </div>
        </div>

        <div class="row">

            @forelse($stores as $store)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                {{ $store['name'] }}
                            </h5>

                            <p class="mb-1">
                                📍 {{ $store['location'] }}
                            </p>

                            <p class="mb-1">
                                📞 {{ $store['phone'] ?? '-' }}
                            </p>

                            <small class="text-muted mb-2">
                                {{ $store['description'] ?? '-' }}
                            </small>

                            <small class="text-muted mb-3">
                                Dibuat:
                                {{ \Carbon\Carbon::parse($store['createdAt'])->format('d M Y') }}
                            </small>

                            <span class="badge {{ $store['isActive'] ? 'bg-success' : 'bg-secondary' }} mb-3">
                                {{ $store['isActive'] ? 'Aktif' : 'Nonaktif' }}
                            </span>

                            <button class="btn btn-warning btn-sm" onclick="editStore('{{ $store['id'] }}')">
                                Edit
                            </button>


                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada store
                    </div>
                </div>
            @endforelse

        </div>
    </div>

    <div class="modal fade" id="addStoreModal">
        <div class="modal-dialog modal-lg">
            <form id="storeForm">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div id="storeAlert"></div>

                        <div class="mb-3">
                            <label>Nama Store</label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="Masukkan nama Store">
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea cols="30" rows="3" name="location" class="form-control" required value="Masukkan alamat"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Latitude</label>
                                <input type="text" name="latitude" id="lat" class="form-control" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Longitude</label>
                                <input type="text" name="longitude" id="lng" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Pilih Lokasi</label>
                            <div id="map" style="height:350px;"></div>
                        </div>

                        <div class="mb-3">
                            <label>Telepon</label>
                            <input type="text" name="phone" class="form-control"
                                placeholder="Masukkan Nomor Telphone Store">
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
                    <h5>Edit Store</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editStoreForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        <input type="hidden" id="edit_id">

                        <input class="form-control mb-2" id="edit_name" placeholder="Nama Store">
                        <input class="form-control mb-2" id="edit_location" placeholder="Alamat">

                        <div id="editMap" style="height:300px;" class="mb-3"></div>

                        <div class="row">
                            <div class="col">
                                <input class="form-control" id="edit_latitude" placeholder="Latitude">
                            </div>
                            <div class="col">
                                <input class="form-control" id="edit_longitude" placeholder="Longitude">
                            </div>
                        </div>

                        <textarea class="form-control mt-2" id="edit_description" placeholder="Deskripsi"></textarea>

                        <input class="form-control mt-2" id="edit_phone" placeholder="Telepon">

                        <select class="form-control mt-2" id="edit_active">
                            <option value="true">Aktif</option>
                            <option value="false">Nonaktif</option>
                        </select>

                        <div id="editAlert" class="mt-2"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">Update Store</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@section('own_script')
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>

    <script>
        let map, marker;

        // init leaflet map
        function initLeaflet() {
            const defaultLatLng = [-6.2088, 106.8456];

            map = L.map('map').setView(defaultLatLng, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker(defaultLatLng, {
                draggable: true
            }).addTo(map);

            // set input awal
            $('#lat').val(defaultLatLng[0]);
            $('#lng').val(defaultLatLng[1]);

            // klik map → update
            map.on('click', function(e) {
                const {
                    lat,
                    lng
                } = e.latlng;
                marker.setLatLng(e.latlng);
                $('#lat').val(lat.toFixed(6));
                $('#lng').val(lng.toFixed(6));
            });

            // drag marker → update
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                $('#lat').val(pos.lat.toFixed(6));
                $('#lng').val(pos.lng.toFixed(6));
            });
        }

        // init map saat modal muncul
        $('#addStoreModal').on('shown.bs.modal', function() {
            setTimeout(initLeaflet, 100);
        });
    </script>


    <script>
        $('#storeForm').on('submit', function(e) {
            e.preventDefault();
            $.post("{{ route('store.store') }}", $(this).serialize(), function(res) {
                location.reload();
            });
        });

        let editMap;
        let editMarker;

        function editStore(id) {

            $.get(`/stores/${id}`, function(res) {

                let store = res.data;

                let lat = parseFloat(store.latitude);
                let lng = parseFloat(store.longitude);

                if (isNaN(lat) || isNaN(lng)) {
                    lat = -6.2;
                    lng = 106.8;
                }

                $('#edit_id').val(store.id);
                $('#edit_name').val(store.name);
                $('#edit_location').val(store.location);
                $('#edit_latitude').val(lat);
                $('#edit_longitude').val(lng);
                $('#edit_description').val(store.description);
                $('#edit_phone').val(store.phone);
                $('#edit_active').val(store.isActive ? 'true' : 'false');

                let modal = new bootstrap.Modal(document.getElementById('editStoreModal'));
                modal.show();

                $('#editStoreModal').on('shown.bs.modal', function() {

                    if (editMap) editMap.remove();

                    editMap = L.map('editMap').setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
                        .addTo(editMap);

                    editMarker = L.marker([lat, lng]).addTo(editMap);

                    editMap.on('click', function(e) {

                        let lat = e.latlng.lat;
                        let lng = e.latlng.lng;

                        $('#edit_latitude').val(lat);
                        $('#edit_longitude').val(lng);

                        editMarker.setLatLng([lat, lng]);
                    });

                    setTimeout(() => editMap.invalidateSize(), 200);
                });

            }).fail(function(xhr) {

                alert("Gagal mengambil data store");
                console.log(xhr.responseText);

            });
        }

        $('#editStoreForm').on('submit', function(e) {
    e.preventDefault();

    let id = $('#edit_id').val();

    $.ajax({
        url: `/stores/${id}`,
        method: 'PATCH',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            name: $('#edit_name').val(),
            location: $('#edit_location').val(),
            latitude: $('#edit_latitude').val(),
            longitude: $('#edit_longitude').val(),
            description: $('#edit_description').val(),
            phone: $('#edit_phone').val(),
            isActive: $('#edit_active').val()
        },

        success: function(res) {
            $('#editAlert').html(
                '<div class="alert alert-success">Store berhasil diupdate</div>'
            );

            setTimeout(() => location.reload(), 1000);
        },

        error: function(xhr) {
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
@endsection
