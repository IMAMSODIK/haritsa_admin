<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StoreController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/stores');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data toko');
            }

            return view('store.index', [
                'pageTitle' => 'Daftar Store',
                'stores' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server store tidak bisa dihubungi');
        }
    }

    public function store(Request $r, ApiService $api)
    {
        $r->validate([
            'name' => 'required',
            'location' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable',
            'phone' => 'nullable',
        ]);

        try {

            $payload = [
                "name" => $r->name,
                "location" => $r->location,
                "latitude" => (float)$r->latitude,
                "longitude" => (float)$r->longitude,
                "description" => $r->description,
                "phone" => $r->phone,
                "isActive" => true
            ];

            $result = $api->request('post', '/stores', $payload);

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal membuat store'
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
                'name' => $r->name,
                'location' => $r->location,
                'latitude' => (float) $r->latitude,
                'longitude' => (float) $r->longitude,
                'description' => $r->description,
                'phone' => $r->phone,
                'isActive' => $r->isActive === 'true' || $r->isActive === true,
            ];

            $response = Http::withToken($token)
                ->patch(env('API_END_POINT') . "/stores/{$id}", $payload);

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

            $result = $api->request('get', "/stores/$id");

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
                ->delete(env('API_END_POINT') . "/stores/{$id}");

            if ($response->failed()) {
                return response()->json([
                    'server' => 'Gagal menghapus store dari server'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Store berhasil dihapus'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
