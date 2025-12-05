<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.unique' => 'Email sudah terpakai, silakan gunakan email lain.',
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'name.required' => 'Nama wajib diisi.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'admin',
            'status'   => 'pending',
        ]);

        return response()->json([
            'message' => 'Pendaftaran berhasil. Tunggu persetujuan dari owner.',
            'user'    => $user,
        ]);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Akun belum disetujui oleh owner.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    /**
     * List semua user (owner only)
     */
    public function listAllUsers(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Hanya owner yang bisa melihat daftar user.'], 403);
        }

        $users = User::all();

        return response()->json([
            'message' => 'Daftar semua user berhasil diambil.',
            'data' => $users
        ]);
    }

    /**
     * Setujui admin (owner only)
     */
    public function approveAdmin(Request $request, $id)
    {
        $owner = $request->user();

        if (!$owner || $owner->role !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Hanya owner yang bisa menyetujui admin.'], 403);
        }

        $admin = User::findOrFail($id);

        if ($admin->role !== 'admin') {
            return response()->json(['message' => 'User bukan admin.'], 400);
        }

        $admin->status = 'active';
        $admin->save();

        return response()->json([
            'message' => "Admin {$admin->name} telah disetujui.",
            'data' => $admin
        ]);
    }

    /**
     * Tolak admin (owner only)
     */
    public function rejectAdmin(Request $request, $id)
    {
        $owner = $request->user();

        if (!$owner || $owner->role !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Hanya owner yang bisa menolak admin.'], 403);
        }

        $admin = User::findOrFail($id);

        if ($admin->role !== 'admin') {
            return response()->json(['message' => 'User bukan admin.'], 400);
        }

        $adminName = $admin->name;
        $admin->delete();

        return response()->json(['message' => "Admin {$adminName} telah ditolak dan dihapus."]);
    }
}
