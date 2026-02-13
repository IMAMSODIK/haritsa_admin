<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PromoVideoController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/promos');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data promo');
            }

            $promos = array_filter($result['data'] ?? [], function ($promo) {
                return ($promo['type'] ?? null) === 'VIDEO';
            });

            return view('promo.index_video', [
                'pageTitle' => 'Daftar Promo Video',
                'promos' => $promos
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server promo tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->post(env('API_END_POINT') . '/promos', [
                    'name' => (string) $r->name,
                    'description' => (string) $r->description,
                    'type' => "VIDEO",
                    'thumbnailUrl' => (string) $r->thumbnailUrl,
                    'videoUrl' => (string) $r->videoUrl
                ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal simpan promo'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message']
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->get(env('API_END_POINT') . "/promos/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => 'Gagal mengambil data promo'
                ], 500);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $r, $id)
    {
        try {
            $http = Http::withToken(session('accessToken'));

            $response = $http->patch(env('API_END_POINT') . "/promos/$id", [
                'name'        => (string) $r->name,
                'description' => (string) $r->description,
                'videoUrl'    => (string) $r->videoUrl,
                'thumbnailUrl'   => (string) $r->thumbnail
            ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal update video promo'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Video promo berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $response = Http::withToken(session('accessToken'))
                ->delete(env('API_END_POINT') . "/promos/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal menghapus promo'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Promo berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
