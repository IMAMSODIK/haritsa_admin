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
}
