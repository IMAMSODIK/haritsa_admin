@extends('layouts.template')

@section('own_style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* main card */
        .profile-card {
            background: white;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e9f2;
        }

        /* header */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #1f3a5f;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 22px;
        }

        .role-badge {
            margin-top: 6px;
            font-size: 14px;
            background: #eef3fc;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            color: #1f3a5f;
        }

        /* grid info */
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-box {
            background: #fafbfd;
            border: 1px solid #e6ebf3;
            padding: 18px;
            border-radius: 12px;
        }

        .info-box label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6c7e9e;
            letter-spacing: 0.5px;
        }

        .info-box .value {
            font-size: 16px;
            font-weight: 600;
            margin-top: 4px;
        }

        .value.success {
            color: #1e7e34;
        }

        /* responsive */
        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
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
            <div class="dashboard-profile">

                <div class="profile-card">

                    <!-- header -->
                    <div class="profile-header">
                        <div class="avatar-circle">
                            <i class="fas fa-user"></i>
                        </div>

                        <div>
                            <h2>Dimas Pramudya</h2>
                            <div class="role-badge">Product Designer · Senior</div>
                        </div>
                    </div>

                    <!-- grid info -->
                    <div class="profile-grid">

                        <div class="info-box">
                            <label>Username</label>
                            <div class="value">@dimasprm</div>
                        </div>

                        <div class="info-box">
                            <label>Role</label>
                            <div class="value">Product Designer</div>
                        </div>

                        <div class="info-box">
                            <label>Nomor HP</label>
                            <div class="value">
                                +62 812-3456-7890
                            </div>
                        </div>

                        <div class="info-box">
                            <label>Status</label>
                            <div class="value success">
                                ✔ Terverifikasi
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection

@section('own_script')
@endsection
