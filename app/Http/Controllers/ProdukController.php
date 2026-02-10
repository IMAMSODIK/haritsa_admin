<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProdukController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/products');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data produk');
            }

            return view('produk.index', [
                'pageTitle' => 'Daftar Produk',
                'produks' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server produk tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {
            $http = Http::withToken(session('accessToken'));

            $response = $http->post(env('API_END_POINT') . '/products', [
                'storeId'     => (string) $r->storeId,
                'sku'         => (string) $r->sku,
                'name'        => (string) $r->name,
                'description' => (string) $r->description,
                'category'    => (string) $r->category,
                'price'       => (int) $r->price,
                'promoPrice'  => (int) $r->promoPrice,
                'stock'       => (int) $r->stock,
                'version'     => (string) $r->version,
                'photos'      => $r->photos, // array of URL
            ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal simpan produk'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Produk berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }


    public function getStore(Request $request)
    {
        $search = $request->search ?? '';

        $response = Http::withToken(session('accessToken'))
            ->get(env('API_END_POINT') . '/stores', [
                'search' => $search
            ]);

        if ($response->failed()) {
            return response()->json([
                'data' => [],
                'message' => 'Gagal mengambil data store'
            ], 500);
        }

        return response()->json($response->json());
    }

    // Get produk by ID
public function show($id)
{
    try {
        $response = Http::withToken(session('accessToken'))
                        ->get(env('API_END_POINT') . "/products/$id");

        if($response->failed()){
            return response()->json(['server' => 'Gagal mengambil produk'], 500);
        }

        return response()->json(['data' => $response->json()]);
    } catch (\Exception $e){
        return response()->json(['debug'=>$e->getMessage()],500);
    }
}

// Update produk
public function update(Request $r, $id)
{
    try {
        $response = Http::withToken(session('accessToken'))
                        ->patch(env('API_END_POINT') . "/products/$id", [
                            'storeId'    => $r->storeId,
                            'sku'        => $r->sku,
                            'name'       => $r->name,
                            'description'=> $r->description,
                            'category'   => $r->category,
                            'price'      => (int) $r->price,
                            'promoPrice' => (int) $r->promoPrice,
                            'stock'      => (int) $r->stock,
                            'version'    => $r->version,
                            'photos'     => $r->photos,
                        ]);

        if($response->failed()){
            return response()->json(['server'=>$response->json()['message'] ?? 'Gagal update produk'], $response->status());
        }

        return response()->json(['success'=>true, 'message'=>$response->json()['message']]);
    } catch (\Exception $e){
        return response()->json(['debug'=>$e->getMessage()],500);
    }
}

// Delete produk
public function destroy($id)
{
    try {
        $response = Http::withToken(session('accessToken'))
                        ->delete(env('API_END_POINT') . "/products/$id");

        if($response->failed()){
            return response()->json(['server'=>$response->json()['message'] ?? 'Gagal hapus produk'], $response->status());
        }

        return response()->json(['success'=>true, 'message'=>$response->json()['message']]);
    } catch (\Exception $e){
        return response()->json(['debug'=>$e->getMessage()],500);
    }
}

}
