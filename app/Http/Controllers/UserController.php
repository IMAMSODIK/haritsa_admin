<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(ApiService $api)
    {
        try {

            $userResult = $api->request('get', '/users');
            $roleResult = $api->request('get', '/users/roles');

            $users = $userResult['data'] ?? [];
            $roles = $roleResult['data'] ?? [];

            return view('user.index', [
                'pageTitle' => 'Daftar Pengguna',
                'users' => $users,
                'roles' => $roles
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

    public function show($id, ApiService $api)
    {
        try {

            $result = $api->request('get', "/users/$id");

            if (!$result || ($result['status'] ?? null) !== 'success') {
                return back()->with('error', 'Data user tidak ditemukan');
            }

            return view('user.detail', [
                'pageTitle' => 'Detail Pengguna',
                'user' => $result['data']
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server tidak bisa dihubungi');
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

            // VALIDASI
            $validated = $request->validate([
                'username' => 'required|string|min:3',
                'phone' => 'required|string|min:10',
                'password' => 'required|string|min:8'
            ]);

            // PAYLOAD ke API
            $payload = [
                'username' => $validated['username'],
                'phone' => $validated['phone'],
                'password' => $validated['password']
            ];

            $result = $api->request(
                'post',
                '/users/' . $roleId,
                $payload
            );

            // jika API gagal
            if (($result['status'] ?? null) !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Gagal menambah user'
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil ditambahkan'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Server tidak merespon'
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

    public function deactivate(ApiService $api, $id)
    {
        try {

            $result = $api->request('patch', "/users/$id/deactivate");

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dinonaktifkan'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal deactivate user'
            ], 500);
        }
    }


    public function profile(ApiService $api)
    {
        try {

            $result = $api->request('get', '/users');

            $users = $result['data'] ?? [];

            return view('profile.index', [
                'pageTitle' => 'Profile',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Server profile tidak bisa dihubungi');
        }
    }
}
