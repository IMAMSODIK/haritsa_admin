<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $token = session('accessToken');

        try {

            $response = Http::withToken($token)
                ->get(env('API_END_POINT') . '/auth/profile');

            $profile = $response->json();

            $data = [
                'pageTitle' => 'Dashboard',
                'username' => $profile['username'] ?? 'Guest',
            ];

            return view('dashboard.index', $data);
        } catch (\Exception $e) {

            session()->flush();

            return redirect()
                ->route('login')
                ->with('error', 'Sesi telah berakhir, silakan login ulang.');
        }
    }
}
