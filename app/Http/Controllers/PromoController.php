<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PromoController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/promos');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data promo');
            }

            return view('promo.index', [
                'pageTitle' => 'Daftar Promo',
                'promos' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server promo tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {

            $http = Http::withToken(session('accessToken'));

            if ($r->hasFile('banner')) {
                $http = $http->attach(
                    'banner',
                    file_get_contents($r->file('banner')->getRealPath()),
                    $r->file('banner')->getClientOriginalName()
                );
            }

            $response = Http::withToken(session('accessToken'))
                ->post(env('API_END_POINT') . '/promos', [
                    'storeId' => (string) $r->storeId,
                    'name' => (string) $r->name,
                    'description' => (string) $r->description,
                    'discountType' => (string) $r->discountType,
                    'discountValue' => (int) $r->discountValue,
                    'minPurchase' => (int) $r->minPurchase,
                    'maxDiscount' => (int) $r->maxDiscount,
                    'bannerUrl' => (string) $r->bannerUrl,
                    'startDate' => (string) $r->startDate,
                    'endDate' => (string) $r->endDate,
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
            // Ambil token dari session
            $http = Http::withToken(session('accessToken'));

            // Kirim data ke API
            $response = $http->patch(env('API_END_POINT') . "/promos/$id", [
                'storeId'       => (string) $r->storeId,
                'name'          => (string) $r->name,
                'description'   => (string) $r->description,
                'discountType'  => (string) $r->discountType,
                'discountValue' => (int) $r->discountValue,
                'minPurchase'   => (int) $r->minPurchase,
                'maxDiscount'   => (int) $r->maxDiscount,
                'bannerUrl'     => (string) $r->bannerUrl,
                'startDate'     => (string) $r->startDate,
                'endDate'       => (string) $r->endDate,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal update promo'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Promo berhasil diupdate'
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

        if($response->failed()){
            return response()->json([
                'server' => $response->json()['message'] ?? 'Gagal menghapus promo'
            ], $response->status());
        }

        return response()->json([
            'success' => true,
            'message' => $response->json()['message'] ?? 'Promo berhasil dihapus'
        ]);

    } catch (\Exception $e){
        return response()->json([
            'debug' => $e->getMessage()
        ], 500);
    }
}

}
