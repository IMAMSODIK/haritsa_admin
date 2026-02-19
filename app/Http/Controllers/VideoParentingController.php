<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VideoParentingController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/parenting/article');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data Artikel');
            }

            $result = collect($result['data'] ?? [])
                ->filter(fn($item) => !empty($item['videoUrl']))
                ->values()
                ->toArray();

            return view('parenting.video_parenting', [
                'pageTitle' => 'Daftar Video Parenting',
                'videos' => $result ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Video Parenting tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {
            $http = Http::withToken(session('accessToken'));

            if ($r->hasFile('thumbnail')) {
                $http = $http->attach(
                    'thumbnail',
                    file_get_contents($r->file('thumbnail')->getRealPath()),
                    $r->file('thumbnail')->getClientOriginalName()
                );
            }

            $response = $http->post(env('API_END_POINT') . "/parenting/article", [
                'title'       => (string) $r->title,
                'content'     => (string) $r->content,
                'moderator'   => (string) $r->moderator,
                'type'        => (string) 'VIDEO',
                'videoUrl'    => (string) $r->videoUrl,
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
                ->get(env('API_END_POINT') . "/parenting/article/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal mengambil data arikel'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'data' => $response->json()['data'] ?? null
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'server' => 'Server artikel tidak bisa dihubungi',
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
                return back()->with('error', 'Gagal mengambil data Video Parenting');
            }

            $result = $response->json();

            return view('parenting.preview_video', [
                'pageTitle' => 'Video Parenting',
                'article' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Video Parenting tidak bisa dihubungi');
        }
    }

    public function update(Request $r, $id)
    {
        try {
            $http = Http::withToken(session('accessToken'));

            if ($r->hasFile('thumbnail')) {
                $http = $http->attach(
                    'thumbnail',
                    file_get_contents($r->file('thumbnail')->getRealPath()),
                    $r->file('thumbnail')->getClientOriginalName()
                );
            }

            $response = $http->patch(env('API_END_POINT') . "/parenting/article/$id", [
                'title'       => (string) $r->title,
                'content'     => (string) $r->content,
                'moderator'   => (string) $r->moderator,
                'videoUrl'    => (string) $r->videoUrl,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal update artikel'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Artikel berhasil diupdate'
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
