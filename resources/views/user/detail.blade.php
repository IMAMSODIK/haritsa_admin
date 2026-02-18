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

        <div class="row g-4">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="mb-3">Detail Pengguna</h4>

                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Username</th>
                            <td>{{ $user['username'] }}</td>
                        </tr>

                        <tr>
                            <th>No HP</th>
                            <td>{{ $user['phone'] }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $user['isActive'] ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user['isActive'] ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <hr>

                    <h5>Profil Member</h5>

                    @php $p = $user['profile']; @endphp

                    <table class="table table-sm table-striped">
                        <tr>
                            <th>Kode</th>
                            <td>{{ $p['Kode'] }}</td>
                        </tr>
                        <tr>
                            <th>No Kartu</th>
                            <td>{{ $p['NoKartu'] }}</td>
                        </tr>
                        <tr>
                            <th>Nama Ibu</th>
                            <td>{{ $p['NamaIbu'] }}</td>
                        </tr>
                        <tr>
                            <th>Tempat Lahir</th>
                            <td>{{ $p['TptLahir'] }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>{{ date('d M Y', strtotime($p['TglLahir'])) }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $p['Alamat'] }}</td>
                        </tr>
                        <tr>
                            <th>Kota</th>
                            <td>{{ $p['Kota'] }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $p['Status'] }}</td>
                        </tr>
                        <tr>
                            <th>Point</th>
                            <td>{{ $p['PointAkhir'] }}</td>
                        </tr>
                        <tr>
                            <th>Agama</th>
                            <td>{{ $p['Agama'] }}</td>
                        </tr>
                    </table>

                    <a href="/users" class="btn btn-secondary mt-3">
                        ← Kembali
                    </a>

                </div>
            </div>

        </div>

    </div>
@endsection

@section('own_script')
@endsection
