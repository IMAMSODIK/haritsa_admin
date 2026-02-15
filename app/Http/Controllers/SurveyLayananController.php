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

            // if (!$result || ($result['status'] ?? null) !== 'success') {
            //     return back()->with('error', 'Gagal mengambil data Survey Layanan');
            // }

            return view('survey.index', [
                'pageTitle' => 'Survey Layanan',
                'surveys' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Survey tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
    {
        try {
            $payload = $r->all();

            $response = Http::withToken(session('accessToken'))
                ->acceptJson()
                ->post(env('API_END_POINT') . '/survey', [
                    'title'       => (string) ($payload['title'] ?? ''),
                    'description' => (string) ($payload['description'] ?? ''),
                    'isActive'    => (bool) ($payload['isActive'] ?? true),
                    'questions'   => $payload['questions'] ?? []
                ]);

            if ($response->failed()) {
                return response()->json([
                    'server' => $response->json()['message'] ?? 'Gagal membuat survey'
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Survey berhasil dibuat'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
