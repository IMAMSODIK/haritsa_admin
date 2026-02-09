<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BannerController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/banner');

            if (!$result || $result['status'] !== 'success') {
                return redirect()
                    ->back()
                    ->with('error', 'Gagal mengambil data banner');
            }

            return view('banner.index', [
                'pageTitle' => 'Daftar Banner',
                'banners' => $result['data']
            ]);
        } catch (\Exception $e) {

            return redirect()
                ->route('dashboard')
                ->with('error', 'Server banner tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        $r->validate([
            'banner' => 'required|image|max:10240',
        ]);

        try {

            $token = session('accessToken');

            if (!$token) {
                return response()->json(['status' => 'unauthorized'], 401);
            }

            $file = $r->file('banner');

            $response = Http::withToken($token)
                ->attach(
                    'banner',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post(env('API_END_POINT') . '/banner');

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

    public function destroy(ApiService $api, $id)
    {
        try {

            $result = $api->request('delete', "/banner/" . urlencode($id));

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal menghapus banner');
            }

            return back()->with('success', 'Banner berhasil dihapus');
        } catch (\Exception $e) {

            return back()->with('error', 'Server tidak bisa dihubungi');
        }
    }
}
