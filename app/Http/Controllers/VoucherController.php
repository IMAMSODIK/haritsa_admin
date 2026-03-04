<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function index(ApiService $api)
    {
        try {
            $userResult = $api->request('get', '/vouchers');

            return view('voucher.index', [
                'pageTitle' => 'Daftar Voucher',
                'vouchers' => $userResult
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Data voucher tidak bisa dihubungi');
        }
    }

    public function generate(Request $r)
    {
        try {

            $multipart = [
                [
                    'name' => 'quantity',
                    'contents' => (string) intval($r->quantity)
                ],
                [
                    'name' => 'nominal',
                    'contents' => (string) intval($r->nominal)
                ],
            ];

            // OPTIONAL
            if ($r->filled('prefix')) {
                $multipart[] = [
                    'name' => 'prefix',
                    'contents' => (string) $r->prefix
                ];
            }

            if ($r->filled('codeLength')) {
                $multipart[] = [
                    'name' => 'codeLength',
                    'contents' => (string) intval($r->codeLength)
                ];
            }

            // IMAGE FILE
            if ($r->hasFile('image')) {

                $file = $r->file('image');

                $multipart[] = [
                    'name'     => 'image',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers'  => [
                        'Content-Type' => $file->getMimeType()
                    ]
                ];
            }

            $response = Http::withToken(session('accessToken'))
                ->send('POST', env('API_END_POINT') . '/vouchers/generate', [
                    'multipart' => $multipart
                ]);

            if ($response->failed()) {

                Log::error('Generate Voucher API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                return response()->json([
                    'message' => $response->json()['message'] ?? 'Gagal generate voucher'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil digenerate'
            ]);
        } catch (\Exception $e) {

            Log::error('Generate Voucher System Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'template_import_voucher.xlsx';
        $filePath = storage_path($fileName);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header columns
        $sheet->setCellValue('A1', 'voucher');
        $sheet->setCellValue('B1', 'prefix');
        $sheet->setCellValue('C1', 'nominal');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function import(Request $r)
    {
        try {

            if (!$r->hasFile('file')) {
                return response()->json([
                    'message' => 'File wajib diupload'
                ], 422);
            }

            $file = $r->file('file');

            $multipart = [
                [
                    'name'     => 'file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers'  => [
                        'Content-Type' => $file->getMimeType()
                    ]
                ]
            ];

            $response = Http::withToken(session('accessToken'))
                ->send('POST', env('API_END_POINT') . '/vouchers/import', [
                    'multipart' => $multipart
                ]);

            if ($response->failed()) {

                Log::error('Import Voucher API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                return response()->json([
                    'message' => $response->json()['message'] ?? 'Gagal import voucher'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diimport'
            ]);
        } catch (\Exception $e) {

            Log::error('Import Voucher System Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function assign(Request $request, ApiService $api)
    {
        try {

            $validated = $request->validate([
                'userIds' => 'required|array|min:1',
                'userIds.*' => 'required|string',
                'quantityPerUser' => 'required|integer|min:1',

                'prefix' => 'nullable|string',
                'nominal' => 'nullable|integer|min:1',

                'voucherIds' => 'nullable|array',
                'voucherIds.*' => 'string',
            ]);

            $response = $api->request('post', '/vouchers/assign', $validated);

            return response()->json([
                'message' => 'Voucher berhasil di-assign'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {

            Log::error('Assign Voucher Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Gagal assign voucher'
            ], 500);
        }
    }

    public function getVoucher(ApiService $api)
    {
        try {
            $result = $api->request('get', '/vouchers');

            $data = $result['data'] ?? $result;

            return response()->json($data);
        } catch (\Exception $e) {

            Log::error('Get Voucher List Error: ' . $e->getMessage());

            return response()->json([], 500);
        }
    }
}
