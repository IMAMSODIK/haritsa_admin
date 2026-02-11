<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $result = $api->request('get', '/users');

            return view('user.index', [
                'pageTitle' => 'Daftar Pengguna'
            ]);
        } catch (\Exception $e) {

            return back()->with('error', 'Server pengguna tidak bisa dihubungi');
        }
    }

    public function loadUser(ApiService $api, Request $request)
    {
        try {

            $params = [
                'page' => $request->page ?? 1,
                'search' => $request->search ?? null,
            ];

            $result = $api->request('get', '/users', $params);

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Server tidak bisa dihubungi'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET USER BY ID
    |--------------------------------------------------------------------------
    */

    public function show(ApiService $api, $id)
    {
        try {

            $result = $api->request('get', '/users/' . $id);

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data user'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET ROLES
    |--------------------------------------------------------------------------
    */

    public function roles(ApiService $api)
    {
        try {

            $result = $api->request('get', '/users/roles');

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil role'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */

    public function store(ApiService $api, Request $request, $roleId)
    {
        try {

            $payload = $request->only([
                'username',
                'phone',
                'password'
            ]);

            $result = $api->request(
                'post',
                '/users/' . $roleId,
                $payload
            );

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambah user'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    public function update(ApiService $api, Request $request, $id)
    {
        try {

            $payload = $request->only([
                'username',
                'phone'
            ]);

            $result = $api->request(
                'patch',
                '/users/' . $id,
                $payload
            );

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal update user'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function destroy(ApiService $api, $id)
    {
        try {

            $result = $api->request(
                'delete',
                '/users/' . $id
            );

            return response()->json($result);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal hapus user'
            ], 500);
        }
    }
}
