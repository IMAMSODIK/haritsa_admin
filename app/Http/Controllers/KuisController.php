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

            $result = $api->request('get', '/parenting/article');

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Gagal mengambil data Artikel');
            }

            return view('parenting.kuis', [
                'pageTitle' => 'Daftar Artikel',
                'articles' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Artikel tidak bisa dihubungi');
        }
    }

    public function store(Request $r)
{
    try {

        $multipart = [
            ['name' => 'title',       'contents' => (string) $r->title],
            ['name' => 'startAt',     'contents' => (string) $r->startAt],
            ['name' => 'endAt',       'contents' => (string) $r->endAt],
            ['name' => 'durationMin', 'contents' => (string) $r->durationMin],
        ];

        // OPTIONAL
        if ($r->filled('videoUrl')) {
            $multipart[] = [
                'name' => 'videoUrl',
                'contents' => (string) $r->videoUrl
            ];
        }

        // 🔥 DECODE QUESTIONS
        $questions = json_decode($r->questions, true);

        if (!$questions || count($questions) < 1) {
            return response()->json([
                'message' => 'Minimal 1 soal diperlukan'
            ], 422);
        }

        // 🔥 LOOP QUESTIONS MENJADI MULTIPART ARRAY
        foreach ($questions as $qIndex => $question) {

            $multipart[] = [
                'name' => "questions[$qIndex][text]",
                'contents' => (string) $question['text']
            ];

            $multipart[] = [
                'name' => "questions[$qIndex][score]",
                'contents' => (string) intval($question['score'])
            ];

            foreach ($question['options'] as $oIndex => $option) {

                $multipart[] = [
                    'name' => "questions[$qIndex][options][$oIndex][text]",
                    'contents' => (string) $option['text']
                ];

                $multipart[] = [
                    'name' => "questions[$qIndex][options][$oIndex][isCorrect]",
                    'contents' => $option['isCorrect'] ? 'true' : 'false'
                ];
            }
        }

        // 🔥 THUMBNAIL
        if ($r->hasFile('thumbnail')) {

            $file = $r->file('thumbnail');

            $multipart[] = [
                'name'     => 'thumbnail',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
                'headers'  => [
                    'Content-Type' => $file->getMimeType()
                ]
            ];
        }

        $response = Http::withToken(session('accessToken'))
            ->send('POST', env('API_END_POINT') . '/parenting/quiz', [
                'multipart' => $multipart
            ]);

        if ($response->failed()) {

            Log::error('Quiz API Error', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);

            return response()->json([
                'message' => $response->json()['message'] ?? 'Gagal menyimpan kuis'
            ], $response->status());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kuis berhasil dibuat'
        ]);

    } catch (\Exception $e) {

        Log::error('Quiz System Error: ' . $e->getMessage());

        return response()->json([
            'message' => 'Internal Server Error'
        ], 500);
    }
}
}
