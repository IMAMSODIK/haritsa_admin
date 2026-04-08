<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KategoriController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/categories');

            // if (!$result || ($result['status'] ?? null) !== 'success') {
            //     return back()->with('error', 'Gagal mengambil data daftar Kategori');
            // }

            return view('kategori.index', [
                'pageTitle' => 'Daftar Kategori',
                'kategories' => $result ?? []
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server Kategori tidak bisa dihubungi');
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'template_import_kategori.xlsx';
        $filePath = storage_path($fileName);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header columns
        $sheet->setCellValue('A1', 'Nama Kategori');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function import(Request $r, ApiService $api)
    {
        try {

            if (!$r->hasFile('file')) {
                return response()->json([
                    'message' => 'File wajib diupload'
                ], 422);
            }

            $file = $r->file('file');

            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // hapus header
            unset($rows[0]);

            $success = 0;
            $failed = 0;
            $errors = [];

            foreach ($rows as $index => $row) {

                $name = trim($row[0] ?? null);

                // skip kosong
                if (!$name) {
                    continue;
                }

                try {

                    $payload = [
                        "name" => $name
                    ];

                    $api->request('post', '/categories', $payload);

                    $success++;
                } catch (\Exception $e) {

                    $failed++;

                    $errors[] = [
                        'row' => $index + 1,
                        'name' => $name,
                        'error' => $e->getMessage()
                    ];

                    Log::error('Import Kategori Row Error', [
                        'row' => $index + 1,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Import selesai. Berhasil: $success, Gagal: $failed",
                'total_success' => $success,
                'total_failed' => $failed,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {

            Log::error('Import Kategori System Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function store(Request $r, ApiService $api)
    {
        $r->validate([
            'name' => 'required',
        ]);

        try {

            $payload = [
                "name" => $r->name
            ];

            $result = $api->request('post', '/categories', $payload);

            // if (!$result || ($result['status'] ?? null) !== 'success') {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Gagal menambahkan Kategori'
            //     ], 500);
            // }

            return response()->json([
                'status' => 1,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $result['data'] ?? null
            ]);
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
                'name' => $r->name
            ];

            $response = Http::withToken($token)
                ->patch(env('API_END_POINT') . "/categories/{$id}", $payload);

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

            $result = $api->request('get', "/categories/$id");

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
                ->delete(env('API_END_POINT') . "/categories/{$id}");

            if ($response->failed()) {
                return response()->json([
                    'server' => 'Gagal menghapus Kategori dari server'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
