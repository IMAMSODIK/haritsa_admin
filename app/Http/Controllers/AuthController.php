<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginCheck(Request $r)
    {
        $validated = $r->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
            'notificationToken' => 'nullable|string',
        ]);

        try {

            $response = Http::post(env('API_END_POINT') . '/users/login', $validated);

            if ($response->failed()) {
                return redirect()
                    ->route('login')
                    ->withInput()
                    ->with('error', 'Nomor Handphone atau password salah');
            }

            $data = $response->json();

            session([
                'accessToken' => $data['data']['accessToken'],
                'refreshToken' => $data['data']['refreshToken'],
                'role' => $data['data']['role'],
            ]);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {

            return redirect()
                ->route('login')
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function logout()
    {
        $refreshToken = session('refreshToken');

        try {
            Http::post(env('API_END_POINT') . '/users/logout', [
                'refreshToken' => $refreshToken
            ]);
        } catch (\Exception $e) {
            
        }

        session()->flush();

        return redirect()
            ->route('login')
            ->with('success', 'Logout berhasil');
    }
}
