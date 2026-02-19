<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PodcastController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/parenting/podcast');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data Podcast');
            }

            return view('parenting.podcast', [
                'pageTitle' => 'Daftar Podcast',
                'podcasts' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server Podcast tidak bisa dihubungi');
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

            $response = $http->post(env('API_END_POINT') . '/parenting/podcast', [
                'title' => (string) $r->title,
                'description' => (string) $r->description,
                'moderator' => (string) $r->moderator,
                'videoUrl' => (string) $r->videoUrl,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal simpan Podcast'
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
                ->get(env('API_END_POINT') . "/parenting/podcast/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => 'Gagal mengambil data Podcast'
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

            if ($r->hasFile('thumbnail')) {
                $http = $http->attach(
                    'thumbnail',
                    file_get_contents($r->file('thumbnail')->getRealPath()),
                    $r->file('thumbnail')->getClientOriginalName()
                );
            }

            $response = $http->patch(env('API_END_POINT') . "/parenting/podcast/$id", [
                'title'        => (string) $r->title,
                'description'  => (string) $r->description,
                'moderator'    => (string) $r->moderator,
                'videoUrl'     => (string) $r->videoUrl,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal update video Podcast'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Video Podcast berhasil diupdate'
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
                ->delete(env('API_END_POINT') . "/parenting/podcast/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal menghapus Podcast'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Podcast berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
