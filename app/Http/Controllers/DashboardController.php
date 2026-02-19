<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/dashboard');
            $banner = $api->request('get', '/banner');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Terjadi kesalahan saat mengambil data dashboard');
            }

            return view('dashboard.index', [
                'pageTitle' => 'Dashboard',
                'data' => [
                    'statistik' => $result['data'] ?? [],
                    'banners' => $banner['data'] ?? []
                ]
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Terjadi kesalahan saat mengambil data dashboard');
        }
    }
}
