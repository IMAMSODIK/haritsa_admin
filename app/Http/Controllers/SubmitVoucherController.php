<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class SubmitVoucherController extends Controller
{
    public function index()
    {
        try {
            return view('voucher.submit', [
                'pageTitle' => 'Submit Voucher'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server Voucher tidak bisa dihubungi');
        }
    }

    public function check($id, ApiService $api)
    {
        try {
            $result = $api->request('get', "/voucher-claims/{$id}/submit");

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function process($id, ApiService $api)
    {
        try {

            $result = $api->request('post', "/voucher-claims/{$id}/submit");

            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'message' => 'Voucher berhasil diproses'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
