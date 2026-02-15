<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KategoriController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/categories');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data daftar Kategori');
            }

            return view('kategori.index', [
                'pageTitle' => 'Daftar Kategori',
                'kategories' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server Kategori tidak bisa dihubungi');
        }
    }

    public function store(Request $r, ApiService $api)
    {
        $r->validate([
            'name' => 'required',
        ]);

        try {

            $payload = [
                "name" => $r->name
            ];

            $result = $api->request('post', '/categories', $payload);

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan Kategori'
                ], 500);
            }

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $r, $id)
    {
        try {

            $token = session('accessToken');

            if (!$token) {
                return response()->json([
                    'status' => 'unauthorized'
                ], 401);
            }

            $payload = [
                'name' => $r->name
            ];

            $response = Http::withToken($token)
                ->patch(env('API_END_POINT') . "/categories/{$id}", $payload);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'server' => $response->body()
                ], 500);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id, ApiService $api)
    {
        try {

            $result = $api->request('get', "/categories/$id");

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->delete(env('API_END_POINT') . "/categories/{$id}");

            if ($response->failed()) {
                return response()->json([
                    'server' => 'Gagal menghapus Kategori dari server'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
