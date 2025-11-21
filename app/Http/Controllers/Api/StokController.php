<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stok;
use Illuminate\Http\Request;

class StokController extends Controller
{
    /**
     * Tampilkan semua stok
     */
    public function index(Request $request)
    {
        $stok = Stok::with('user')
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Data stok berhasil diambil.',
            'data' => $stok
        ]);
    }

    /**
     * Simpan stok baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'foto'        => 'nullable|image|max:2048',
            'video'       => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:10240',
            'harga'       => 'required|numeric|min:0',
            'jumlah'      => 'required|integer|min:1',
            'tgl_masuk'   => 'required|date',
            'tgl_keluar'  => 'nullable|date|after_or_equal:tgl_masuk',
            'user_id'     => 'required|exists:users,id',
        ]);

        // Upload file foto/video jika ada
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('stok/foto', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('stok/video', 'public');
        }

        $stok = Stok::create($validated);

        return response()->json([
            'message' => 'Stok berhasil ditambahkan',
            'data' => $stok->load('user')
        ], 201);
    }

    /**
     * Tampilkan detail stok
     */
    public function show($id)
    {
        $stok = Stok::with('user')->findOrFail($id);

        return response()->json([
            'message' => 'Detail stok berhasil diambil.',
            'data' => $stok
        ]);
    }

    /**
     * Update stok
     */
    public function update(Request $request, $id)
    {
        $stok = Stok::findOrFail($id);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'foto'        => 'nullable|image|max:2048',
            'video'       => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:10240',
            'harga'       => 'required|numeric|min:0',
            'jumlah'      => 'required|integer|min:1',
            'tgl_masuk'   => 'required|date',
            'tgl_keluar'  => 'nullable|date|after_or_equal:tgl_masuk',
            'user_id'     => 'required|exists:users,id',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('stok/foto', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('stok/video', 'public');
        }

        $stok->update($validated);

        return response()->json([
            'message' => 'Stok berhasil diperbarui',
            'data' => $stok->load('user')
        ]);
    }

    /**
     * Hapus stok
     */
    public function destroy($id)
    {
        $stok = Stok::findOrFail($id);
        $stok->delete();

        return response()->json([
            'message' => 'Stok berhasil dihapus'
        ]);
    }
}
