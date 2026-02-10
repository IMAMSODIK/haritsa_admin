@extends('layouts.template')

@section('own_style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> --}}
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        .store-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            border-radius: 16px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            position: relative;
            cursor: pointer;
        }

        .store-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .store-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        }

        .store-card:hover::before {
            opacity: 1;
        }

        .store-card.active:hover::before {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .store-card.inactive {
            opacity: 0.85;
        }

        .store-card.inactive:hover {
            transform: translateY(-5px) scale(1.01);
        }

        /* Map Container */
        .map-container {
            height: 200px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .map-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 70%, rgba(0, 0, 0, 0.1) 100%);
            pointer-events: none;
        }

        .map-thumbnail {
            height: 100%;
            width: 100%;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .map-thumbnail .leaflet-container {
            border-radius: 0;
            height: 100% !important;
            width: 100% !important;
        }

        /* Card Body */
        .card-body {
            padding: 1.5rem;
            position: relative;
            background: white;
        }

        /* Store Header */
        .store-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .store-title {
            font-weight: 700;
            font-size: 1.25rem;
            color: #1e293b;
            line-height: 1.3;
            flex: 1;
            margin: 0;
        }

        .store-title:hover {
            color: #667eea;
        }

        /* Status Badge */
        .status-badge {
            padding: 0.4em 1em;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 20px;
            text-transform: uppercase;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .status-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .status-badge:hover::before {
            left: 100%;
        }

        .status-badge.active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-badge.inactive {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        /* Store Info */
        .store-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .store-info:hover {
            background: #f1f5f9;
            transform: translateX(5px);
        }

        .store-info-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
            color: #667eea;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .store-info-content {
            flex: 1;
        }

        .store-info-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .store-info-value {
            font-size: 0.9rem;
            color: #1e293b;
            font-weight: 500;
            word-break: break-word;
        }

        /* Description */
        .store-description-container {
            margin: 1.25rem 0;
            padding: 12px;
            background: linear-gradient(to right, #f8fafc, #ffffff);
            border-left: 3px solid #667eea;
            border-radius: 0 8px 8px 0;
        }

        .store-description {
            color: #475569;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Footer Card */
        .store-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .store-date {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.85rem;
        }

        .store-date-icon {
            color: #94a3b8;
        }

        .store-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border: none;
            border-radius: 8px;
            color: #64748b;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #667eea;
            color: white;
            transform: scale(1.1);
        }

        /* Empty State */
        .empty-state-container {
            padding: 4rem 2rem;
            text-align: center;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 20px;
            margin: 2rem 0;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }

        .empty-state-description {
            color: #64748b;
            font-size: 1rem;
            max-width: 400px;
            margin: 0 auto 1.5rem;
        }

        .empty-state-btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.2);
        }

        .empty-state-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .store-card {
                margin-bottom: 1.5rem;
            }

            .store-header {
                flex-direction: column;
                gap: 10px;
            }

            .status-badge {
                align-self: flex-start;
            }
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Custom Marker */
        .custom-store-marker {
            background: white;
            border-radius: 50%;
            padding: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .custom-store-marker i {
            color: #667eea;
            font-size: 18px;
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                    <i class="fa fa-plus"></i> Tambah Store
                </button>
            </div>
        </div>

        <div class="row g-4">
            @forelse($stores as $store)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card store-card {{ $store['isActive'] ? 'active' : 'inactive' }}"
                        onclick="editStore('{{ $store['id'] }}')" data-store-id="{{ $store['id'] }}">

                        <!-- Map Container -->
                        <div class="map-container">
                            <div id="map-{{ $store['id'] }}" class="map-thumbnail loading-shimmer"></div>
                            <div class="store-overlay-badge">
                                <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-3">
                                    <i class="fas fa-map-marker-alt me-1"></i> Lokasi
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Store Header -->
                            <div class="store-header">
                                <h3 class="store-title">{{ $store['name'] }}</h3>
                                <span class="status-badge {{ $store['isActive'] ? 'active' : 'inactive' }}">
                                    <i
                                        class="fas {{ $store['isActive'] ? 'fa-check-circle' : 'fa-pause-circle' }} me-1"></i>
                                    {{ $store['isActive'] ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <!-- Store Info -->
                            <div class="store-info">
                                <div class="store-info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="store-info-content">
                                    <div class="store-info-label">Lokasi</div>
                                    <div class="store-info-value">{{ $store['location'] }}</div>
                                </div>
                            </div>

                            <div class="store-info">
                                <div class="store-info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="store-info-content">
                                    <div class="store-info-label">Telepon</div>
                                    <div class="store-info-value">{{ $store['phone'] ?? 'Belum diatur' }}</div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if ($store['description'])
                                <div class="store-description-container">
                                    <div class="store-info-label mb-2">
                                        <i class="fas fa-align-left me-2"></i>Deskripsi
                                    </div>
                                    <p class="store-description">{{ $store['description'] }}</p>
                                </div>
                            @endif

                            <!-- Footer -->
                            <div class="store-footer">
                                <div class="store-date">
                                    <i class="fas fa-calendar-alt store-date-icon"></i>
                                    <span>Dibuat {{ \Carbon\Carbon::parse($store['createdAt'])->format('d M Y') }}</span>
                                </div>
                                {{-- <div class="store-actions">
                                    <button class="action-btn"
                                        onclick="event.stopPropagation(); editStore('{{ $store['id'] }}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn"
                                        onclick="event.stopPropagation(); previewStore('{{ $store['id'] }}')"
                                        title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada Store
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
                            <label>Deksripsi</label>
                            <textarea cols="30" rows="3" name="description" class="form-control" required
                                value="Masukkan Deksripsi toko"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea cols="30" rows="3" name="location" class="form-control" required value="Masukkan alamat toko"></textarea>
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

                        <div class="mb-3">
                            <label>Deksripsi</label>
                            <textarea cols="30" rows="3" id="edit_description" class="form-control" required
                                value="Masukkan Deksripsi toko"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea cols="30" rows="3" id="edit_location" class="form-control" required
                                value="Masukkan alamat toko"></textarea>
                        </div>

                        <div id="editMap" style="height:300px;" class="mb-3"></div>

                        <div class="row">
                            <div class="col">
                                <input class="form-control" id="edit_latitude" placeholder="Latitude">
                            </div>
                            <div class="col">
                                <input class="form-control" id="edit_longitude" placeholder="Longitude">
                            </div>
                        </div>

                        <input class="form-control mt-2" id="edit_phone" placeholder="Telepon">

                        <select class="form-control mt-2" id="edit_active">
                            <option value="true">Aktif</option>
                            <option value="false">Nonaktif</option>
                        </select>

                        <div id="editAlert" class="mt-2"></div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100 mb-1">Update Store</button>
                        <button type="button" class="btn btn-danger w-100" id="deleteStoreBtn">
                            Hapus Store
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@section('own_script')
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
            document.body.style.cursor = 'wait';

            $.get(`/stores/${id}`, function(res) {

                let store = res.data;

                let lat = parseFloat(store.latitude);
                let lng = parseFloat(store.longitude);

                if (isNaN(lat) || isNaN(lng)) {
                    lat = -6.2;
                    lng = 106.8;
                }

                console.log(store);

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

                $('#editStoreModal').one('shown.bs.modal', function() {

                    // cursor kembali normal saat modal sudah muncul
                    document.body.style.cursor = 'default';

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

                // kalau error, kursor juga balik normal
                document.body.style.cursor = 'default';

                alert("Gagal mengambil data store");
                console.log(xhr.responseText);

            });
        }

        $('#editStoreForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#edit_id').val();
            let btn = $('#editStoreForm button[type="submit"]');

            btn.prop('disabled', true).text('Updating...');
            document.body.style.cursor = 'wait';

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

                    document.body.style.cursor = 'default';

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Store berhasil diupdate',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });

                },

                error: function(xhr) {

                    document.body.style.cursor = 'default';
                    btn.prop('disabled', false).text('Update Store');

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
        // Fungsi untuk inisialisasi peta
        function initializeStoreMaps() {
            @foreach ($stores as $store)
                @if ($store['location'] && $store['latitude'] && $store['longitude'])
                    (function() {
                        const storeData = @json($store);
                        const mapId = 'map-' + storeData.id;
                        const mapElement = document.getElementById(mapId);

                        if (!mapElement) return;

                        // Hapus class loading setelah peta dimuat
                        mapElement.classList.remove('loading-shimmer');

                        const lat = parseFloat(storeData.latitude);
                        const lng = parseFloat(storeData.longitude);

                        // Buat peta dengan zoom yang sesuai
                        const map = L.map(mapId).setView([lat, lng], 15);

                        // Gunakan tile yang lebih menarik
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; OpenStreetMap, &copy; CARTO',
                            maxZoom: 19,
                            minZoom: 10
                        }).addTo(map);

                        // Buat custom icon marker
                        const storeIcon = L.divIcon({
                            className: 'custom-store-marker',
                            html: `<i class="fas fa-store" style="color: ${storeData.isActive ? '#10b981' : '#6b7280'};"></i>`,
                            iconSize: [36, 36],
                            iconAnchor: [18, 36],
                            popupAnchor: [0, -36]
                        });

                        // Tambahkan marker
                        const marker = L.marker([lat, lng], {
                            icon: storeIcon
                        }).addTo(map);

                        // Popup dengan informasi store
                        marker.bindPopup(`
                    <div style="min-width: 200px;">
                        <h5 style="margin: 0 0 8px 0; color: #1e293b;"><i class="fas fa-store me-2"></i>${storeData.name}</h5>
                        <p style="margin: 0 0 5px 0; color: #64748b;"><i class="fas fa-map-marker-alt me-2"></i>${storeData.location}</p>
                        <span class="badge ${storeData.isActive ? 'bg-success' : 'bg-secondary'}" style="font-size: 0.8em;">
                            ${storeData.isActive ? 'Aktif' : 'Nonaktif'}
                        </span>
                    </div>
                `);

                        // Nonaktifkan interaksi yang tidak perlu
                        map.scrollWheelZoom.disable();
                        map.dragging.disable();
                        map.touchZoom.disable();
                        map.doubleClickZoom.disable();
                        map.boxZoom.disable();
                        map.keyboard.disable();

                    })();
                @endif
            @endforeach
        }

        // Tunggu sampai DOM siap
        document.addEventListener('DOMContentLoaded', function() {
            // Delay sedikit untuk memastikan semua elemen sudah dirender
            setTimeout(initializeStoreMaps, 300);
        });

        // Fungsi untuk menambah store baru
        function addNewStore() {
            // Implementasi fungsi untuk menambah store baru
            window.location.href = '/stores/create';
        }

        // Fungsi untuk preview store
        function previewStore(storeId) {
            // Implementasi preview store
            console.log('Preview store:', storeId);
            // Buka modal atau redirect ke halaman preview
        }

        $('#deleteStoreBtn').on('click', function() {

            let id = $('#edit_id').val();

            // tutup modal sementara
            let modalEl = document.getElementById('editStoreModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            Swal.fire({
                title: 'Hapus store?',
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
                    url: `/stores/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function() {

                        document.body.style.cursor = 'default';

                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Store berhasil dihapus',
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
                            text: 'Tidak bisa menghapus store'
                        });

                        console.log(xhr.responseText);
                    }
                });

            });

        });
    </script>
@endsection
