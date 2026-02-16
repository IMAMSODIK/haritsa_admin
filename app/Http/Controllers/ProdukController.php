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

            // attach multiple photos
            if ($r->hasFile('photos')) {
                foreach ($r->file('photos') as $file) {
                    $http->attach(
                        'photos',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    );
                }
            }

            $response = $http->post(env('API_END_POINT') . '/products', [
                'storeId'     => (string) $r->storeId,
                'sku'         => (string) $r->sku,
                'name'        => (string) $r->name,
                'description' => (string) $r->description,
                'categoryId'  => (string) $r->category,
                'brandId'     => (string) $r->brand,
                'price'       => (int) $r->price,
                'promoPrice'  => (int) $r->promoPrice,
                'stock'       => (int) $r->stock,
                'version'     => (string) $r->version,
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

    public function getCategories(Request $request)
    {
        $search = $request->search ?? '';

        $response = Http::withToken(session('accessToken'))
            ->get(env('API_END_POINT') . '/categories', [
                'search' => $search
            ]);

        if ($response->failed()) {
            return response()->json([
                'data' => [],
                'message' => 'Gagal mengambil data kategori'
            ], 500);
        }

        return response()->json($response->json());
    }

    public function getBrands(Request $request)
    {
        $search = $request->search ?? '';

        $response = Http::withToken(session('accessToken'))
            ->get(env('API_END_POINT') . '/brands', [
                'search' => $search
            ]);

        if ($response->failed()) {
            return response()->json([
                'data' => [],
                'message' => 'Gagal mengambil data brand'
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

            if ($response->failed()) {
                return response()->json(['server' => 'Gagal mengambil produk'], 500);
            }

            return response()->json(['data' => $response->json()]);
        } catch (\Exception $e) {
            return response()->json(['debug' => $e->getMessage()], 500);
        }
    }

    // Update produk
    public function update(Request $r, $id)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->patch(env('API_END_POINT') . "/products/$id", [
                    'storeId'     => (string) $r->storeId,
                    'sku'         => (string) $r->sku,
                    'name'        => (string) $r->name,
                    'description' => (string) $r->description,
                    'categoryId'  => (string) $r->category,
                    'brandId'     => (string) $r->brand,
                    'price'       => (int) $r->price,
                    'promoPrice'  => (int) $r->promoPrice,
                    'stock'       => (int) $r->stock,
                    'version'     => (string) $r->version,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal update produk'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Produk berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyPhoto($photo_id)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->delete(env('API_END_POINT') . "/products/photos/$photo_id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal hapus foto'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function addPhotos(Request $r, $id)
    {
        try {

            $http = Http::withToken(session('accessToken'));

            if ($r->hasFile('photos')) {
                foreach ($r->file('photos') as $file) {
                    $http->attach(
                        'photos',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    );
                }
            }

            $response = $http->post(env('API_END_POINT') . "/products/$id/photos");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Upload foto gagal'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    // Delete produk
    public function destroy($id)
    {
        try {
            $response = Http::withToken(session('accessToken'))
                ->delete(env('API_END_POINT') . "/products/$id");

            if ($response->failed()) {
                return response()->json(['server' => $response->json()['message'] ?? 'Gagal hapus produk'], $response->status());
            }

            return response()->json(['success' => true, 'message' => $response->json()['message']]);
        } catch (\Exception $e) {
            return response()->json(['debug' => $e->getMessage()], 500);
        }
    }
}
