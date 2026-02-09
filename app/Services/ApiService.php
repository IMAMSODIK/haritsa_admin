<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    public function request($method, $endpoint, $payload = [])
    {
        $token = session('accessToken');

        $response = Http::withToken($token)
            ->$method(env('API_END_POINT') . $endpoint, $payload);

        if ($response->status() === 401) {
            if ($this->refreshToken()) {

                $newToken = session('accessToken');

                $response = Http::withToken($newToken)
                    ->$method(env('API_END_POINT') . $endpoint, $payload);
            }
        }

        return $response->json();
    }

    private function refreshToken()
    {
        $refreshToken = session('refreshToken');

        $response = Http::post(env('API_END_POINT') . '/users/refresh-token', [
            'refreshToken' => $refreshToken
        ]);

        if ($response->failed()) {
            session()->flush();
            return false;
        }

        $data = $response->json();

        session([
            'accessToken' => $data['data']['accessToken']
        ]);

        return true;
    }
}
