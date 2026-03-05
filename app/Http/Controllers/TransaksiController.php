<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $userResult = $api->request('get', '/transactions');

            $transaksi = $userResult['data'] ?? [];
            dd($transaksi);

            return view('transaksi.index', [
                'pageTitle' => 'Daftar Transaksi',
                'transaksi' => $transaksi
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Transaksi tidak bisa dihubungi');
        }
    }
}
