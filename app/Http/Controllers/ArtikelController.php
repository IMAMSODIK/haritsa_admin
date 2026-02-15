<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ArtikelController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/parenting/article');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data Artikel');
            }

            return view('parenting.artikel', [
                'pageTitle' => 'Daftar Artikel',
                'articles' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Artikel tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->post(env('API_END_POINT') . '/parenting/article', [
                    'title'       => (string) $r->title,
                    'content'     => (string) $r->content,
                    'moderator'   => (string) $r->moderator,
                    'videoUrl'    => (string) $r->videoUrl,
                    'score'       => (int) $r->score,
                    // 'thumbnailUrl' => (string) $r->thumbnailUrl
                ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal simpan Artikel'
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
                ->get(env('API_END_POINT') . "/survey/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal mengambil data survey'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'data' => $response->json()['data'] ?? null
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'server' => 'Server survey tidak bisa dihubungi',
                'debug' => $e->getMessage()
            ], 500);
        }
    }


    public function preview($id)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->get(env('API_END_POINT') . "/parenting/article/$id");

            if ($response->failed()) {
                return back()->with('error', 'Gagal mengambil data Artikel');
            }

            $result = $response->json();

            return view('parenting.preview_artikel', [
                'pageTitle' => 'Artikel Parenting',
                'article' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Artikel tidak bisa dihubungi');
        }
    }

    public function update(Request $r, $id)
    {
        try {

            $payload = [
                'title'       => $r->title,
                'description' => $r->description,
                'isActive'    => filter_var($r->isActive, FILTER_VALIDATE_BOOLEAN),
                'questions'   => json_decode($r->questions, true) ?? []
            ];

            $response = Http::withToken(session('accessToken'))
                ->patch(env('API_END_POINT') . "/survey/$id", $payload);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal update survey'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Survey berhasil diupdate'
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
                ->delete(env('API_END_POINT') . "/parenting/article/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal menghapus Artikel'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Artikel berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
