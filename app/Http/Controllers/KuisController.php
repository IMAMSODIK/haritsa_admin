<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KuisController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/parenting/quiz');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data Kuis');
            }

            return view('parenting.kuis', [
                'pageTitle' => 'Daftar Kuis',
                'kuises' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Kuis tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {

            $http = Http::withToken(session('accessToken'));

            // attach file thumbnail jika ada
            if ($r->hasFile('thumbnail')) {

                $http = $http->attach(
                    'thumbnail',
                    file_get_contents($r->file('thumbnail')->getRealPath()),
                    $r->file('thumbnail')->getClientOriginalName()
                );
            }

            // kirim request ke API parenting/quiz
            $response = $http->post(env('API_END_POINT') . '/parenting/quiz', [
                'title' => (string) $r->title,
                'videoUrl' => (string) $r->videoUrl,
                'startAt' => (string) $r->startAt,
                'endAt' => (string) $r->endAt,
                'durationMin' => (int) $r->durationMin
            ]);

            // jika gagal
            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal membuat quiz'
                ], $response->status());
            }

            // jika berhasil
            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Quiz berhasil dibuat'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function storeQuestion(Request $r, $quizId)
    {
        try {

            $payload = $r->all();

            $response = Http::withToken(session('accessToken'))
                ->post(env('API_END_POINT') . "/parenting/quiz/$quizId/question", $payload);

            if ($response->failed()) {
                return response()->json([
                    'message' => $response->json()['message'] ?? 'Gagal menyimpan pertanyaan'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getQuestions($quizId)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->get(env('API_END_POINT') . "/parenting/quiz/$quizId");

            if ($response->failed()) {
                return response()->json([
                    'message' => 'Gagal mengambil pertanyaan'
                ], $response->status());
            }

            return response()->json(
                $response->json()
            );
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $response = Http::withToken(session('accessToken'))
                ->delete(env('API_END_POINT') . "/parenting/quiz/$id");

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal menghapus Kuis'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Kuis berhasil dihapus'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
