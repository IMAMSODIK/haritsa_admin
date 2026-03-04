<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SurveyLayananController extends Controller
{
    public function index(ApiService $api)
    {
        try {
            $result = $api->request('get', '/survey');

            return view('survey.index', [
                'pageTitle' => 'Survey Layanan',
                'surveys' => $result ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Survey tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {

            // =========================
            // VALIDASI DASAR
            // =========================
            $validated = $r->validate([
                'title'         => 'required|string|max:200',
                'description'   => 'nullable|string',
                'isActive'      => 'required|boolean',
                'triggerType'   => 'required|in:AFTER_TRANSACTION,AFTER_VOUCHER_CLAIM,PERIOD,EVENT',

                'periodStartAt' => 'nullable|date',
                'periodEndAt'   => 'nullable|date',
                'eventCode'     => 'nullable|string',

                'questions'                 => 'required|array|min:1',
                'questions.*.category'      => 'required|string|max:100',
                'questions.*.question'      => 'required|string',
                'questions.*.type'          => 'required|in:LIKERT,TEXT',
                'questions.*.isRequired'    => 'required|boolean',
                'questions.*.likertScale'   => 'nullable|integer|min:2|max:10'
            ]);

            // =========================
            // VALIDASI KHUSUS TRIGGER
            // =========================
            if ($validated['triggerType'] === 'PERIOD') {

                if (!$r->periodStartAt || !$r->periodEndAt) {
                    return response()->json([
                        'message' => 'Periode mulai dan selesai wajib diisi untuk trigger PERIOD'
                    ], 422);
                }

                if (strtotime($r->periodEndAt) < strtotime($r->periodStartAt)) {
                    return response()->json([
                        'message' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai'
                    ], 422);
                }
            }

            if ($validated['triggerType'] === 'EVENT' && !$r->eventCode) {
                return response()->json([
                    'message' => 'Event code wajib diisi untuk trigger EVENT'
                ], 422);
            }

            // =========================
            // VALIDASI LIKERT
            // =========================
            foreach ($validated['questions'] as $q) {

                if ($q['type'] === 'LIKERT' && empty($q['likertScale'])) {
                    return response()->json([
                        'message' => 'Likert scale wajib diisi untuk pertanyaan tipe LIKERT'
                    ], 422);
                }
            }

            // =========================
            // PREPARE PAYLOAD KE API
            // =========================
            $payload = [
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? '',
                'isActive'    => $validated['isActive'],
                'triggerType' => $validated['triggerType'],
                'questions'   => $validated['questions'],
            ];

            if ($validated['triggerType'] === 'PERIOD') {
                $payload['periodStartAt'] = $r->periodStartAt;
                $payload['periodEndAt']   = $r->periodEndAt;
            }

            if ($validated['triggerType'] === 'EVENT') {
                $payload['eventCode'] = $r->eventCode;
            }

            // =========================
            // HIT API BACKEND
            // =========================
            $response = Http::withToken(session('accessToken'))
                ->acceptJson()
                ->post(env('API_END_POINT') . '/survey', $payload);

            if ($response->failed()) {
                return response()->json([
                    'message' => $response->json()['message'] ?? 'Gagal membuat survey'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Survey berhasil dibuat'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'debug'   => $e->getMessage()
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
                    'server' => 'Gagal ambil survey'
                ], $response->status());
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

            // =========================
            // VALIDASI DASAR
            // =========================
            $validated = $r->validate([
                'title'         => 'required|string|max:200',
                'description'   => 'nullable|string',
                'isActive'      => 'required|boolean',
                'triggerType'   => 'required|in:AFTER_TRANSACTION,AFTER_VOUCHER_CLAIM,PERIOD,EVENT',

                'periodStartAt' => 'nullable|date',
                'periodEndAt'   => 'nullable|date',
                'eventCode'     => 'nullable|string',

                'questions'                 => 'required|array|min:1',
                'questions.*.category'      => 'required|string|max:100',
                'questions.*.question'      => 'required|string',
                'questions.*.type'          => 'required|in:LIKERT,TEXT',
                'questions.*.isRequired'    => 'required|boolean',
                'questions.*.likertScale'   => 'nullable|integer|min:2|max:10'
            ]);

            // =========================
            // VALIDASI KHUSUS TRIGGER
            // =========================
            if ($validated['triggerType'] === 'PERIOD') {

                if (!$r->periodStartAt || !$r->periodEndAt) {
                    return response()->json([
                        'message' => 'Periode mulai dan selesai wajib diisi untuk trigger PERIOD'
                    ], 422);
                }

                if (strtotime($r->periodEndAt) < strtotime($r->periodStartAt)) {
                    return response()->json([
                        'message' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai'
                    ], 422);
                }
            }

            if ($validated['triggerType'] === 'EVENT' && !$r->eventCode) {
                return response()->json([
                    'message' => 'Event code wajib diisi untuk trigger EVENT'
                ], 422);
            }

            // =========================
            // VALIDASI LIKERT
            // =========================
            foreach ($validated['questions'] as $q) {

                if ($q['type'] === 'LIKERT' && empty($q['likertScale'])) {
                    return response()->json([
                        'message' => 'Likert scale wajib diisi untuk pertanyaan tipe LIKERT'
                    ], 422);
                }
            }

            // =========================
            // PREPARE PAYLOAD
            // =========================
            $payload = [
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? '',
                'isActive'    => $validated['isActive'],
                'triggerType' => $validated['triggerType'],
                'questions'   => $validated['questions'],
            ];

            if ($validated['triggerType'] === 'PERIOD') {
                $payload['periodStartAt'] = $r->periodStartAt;
                $payload['periodEndAt']   = $r->periodEndAt;
            }

            if ($validated['triggerType'] === 'EVENT') {
                $payload['eventCode'] = $r->eventCode;
            }

            // =========================
            // HIT API BACKEND
            // =========================
            $response = Http::withToken(session('accessToken'))
                ->acceptJson()
                ->put(env('API_END_POINT') . '/survey/' . $id, $payload);

            if ($response->failed()) {
                return response()->json([
                    'message' => $response->json()['message'] ?? 'Gagal update survey'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Survey berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'debug'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->delete(env('API_END_POINT') . "/survey/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal menghapus survey'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Survey berhasil dihapus'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
