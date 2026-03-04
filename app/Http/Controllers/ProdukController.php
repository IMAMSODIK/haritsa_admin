<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $isAllStores = filter_var($r->isAllStores, FILTER_VALIDATE_BOOLEAN);

            $storeIds = [];
            if (!$isAllStores) {
                if ($r->filled('storeIds') && is_array($r->storeIds)) {
                    $storeIds = $r->storeIds;
                } elseif ($r->filled('storeId')) {
                    $storeIds = [$r->storeId];
                } else {
                    return response()->json(['message' => 'Minimal satu toko harus dipilih'], 422);
                }
            }

            $multipart = [
                ['name' => 'sku',         'contents' => (string) $r->sku],
                ['name' => 'name',        'contents' => (string) $r->name],
                ['name' => 'isAllStores', 'contents' => $isAllStores ? 'true' : 'false'],
                ['name' => 'isActive',    'contents' => 'true'],
                ['name' => 'price',       'contents' => (string) $r->price],
            ];

            $optionalFields = ['description', 'brandId', 'categoryId', 'promoPrice', 'stock'];
            foreach ($optionalFields as $field) {
                if ($r->filled($field)) {
                    $multipart[] = [
                        'name'     => $field,
                        'contents' => (string) $r->{$field}
                    ];
                }
            }

            foreach ($storeIds as $id) {
                $multipart[] = [
                    'name'     => 'storeIds',
                    'contents' => (string) $id
                ];
            }

            if ($r->hasFile('photos')) {
                $photos = $r->file('photos');
                if (!is_array($photos)) {
                    $photos = [$photos];
                }

                foreach ($photos as $photo) {
                    $multipart[] = [
                        'name'     => 'photos',
                        'contents' => fopen($photo->getRealPath(), 'r'),
                        'filename' => $photo->getClientOriginalName(),
                        'headers'  => [
                            'Content-Type' => $photo->getMimeType()
                        ]
                    ];
                }
            }

            $response = Http::withToken(session('accessToken'))
                ->send('POST', env('API_END_POINT') . '/products', [
                    'multipart' => $multipart
                ]);

            if ($response->failed()) {
                Log::error('API Product Error', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                return response()->json([
                    'message' => $response->json()['message'] ?? 'Gagal menyimpan ke API pusat.'
                ], $response->status());
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Product berhasil dibuat',
                'data'    => $response->json()
            ]);
        } catch (\Exception $e) {
            Log::error('System Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'error'   => $e->getMessage()
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

            $isAllStores = filter_var($r->isAllStores, FILTER_VALIDATE_BOOLEAN);

            $storeIds = [];

            if (!$isAllStores) {

                if ($r->filled('storeIds') && is_array($r->storeIds)) {
                    $storeIds = $r->storeIds;
                } elseif ($r->filled('storeId')) {
                    $storeIds = [$r->storeId];
                } else {
                    return response()->json([
                        'message' => 'Minimal satu toko harus dipilih'
                    ], 422);
                }
            }

            $multipart = [
                ['name' => 'sku',         'contents' => (string) $r->sku],
                ['name' => 'name',        'contents' => (string) $r->name],
                ['name' => 'isAllStores', 'contents' => $isAllStores ? 'true' : 'false'],
                ['name' => 'price',       'contents' => (string) $r->price],
            ];

            $optionalFields = [
                'description',
                'brandId',
                'categoryId',
                'promoPrice',
                'stock'
            ];

            foreach ($optionalFields as $field) {
                if ($r->filled($field)) {
                    $multipart[] = [
                        'name'     => $field,
                        'contents' => (string) $r->{$field}
                    ];
                }
            }

            foreach ($storeIds as $sid) {
                $multipart[] = [
                    'name'     => 'storeIds',
                    'contents' => (string) $sid
                ];
            }

            $response = Http::withToken(session('accessToken'))
                ->send('PATCH', env('API_END_POINT') . "/products/$id", [
                    'multipart' => $multipart
                ]);

            if ($response->failed()) {

                Log::error('Update Product API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                return response()->json([
                    'message' => $response->json()['message'] ?? 'Gagal update produk'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate'
            ]);
        } catch (\Exception $e) {

            Log::error('System Update Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Internal Server Error'
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
