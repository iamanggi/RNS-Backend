<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stok;
use Illuminate\Http\Request;

class StokController extends Controller
{
    /**
     * Tampilkan semua stok beserta summary
     */
    public function index(Request $request)
    {
        $query = Stok::with(['barang', 'user']);

        if ($request->barang_id) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $stok = $query->latest()->get();

        // Hitung summary
        $total_masuk = $stok->sum('jumlah'); // total semua stok
        $total_keluar = $stok->whereNotNull('tgl_keluar')->sum('jumlah'); // total stok keluar
        $total_sisa = $total_masuk - $total_keluar; // total stok sekarang

        return response()->json([
            'data' => $stok,
            'summary' => [
                'total_masuk'  => $total_masuk,
                'total_keluar' => $total_keluar,
                'total_sisa'   => $total_sisa
            ]
        ]);
    }

    /**
     * Simpan stok baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'user_id'   => 'required|exists:users,id',
            'jumlah'    => 'required|integer|min:1',
            'tgl_masuk' => 'required|date',
            'tgl_keluar'=> 'nullable|date|after_or_equal:tgl_masuk',
            'harga'     => 'required|numeric|min:0'
        ]);

        $stok = Stok::create($validated);

        return response()->json([
            'message' => 'Stok berhasil ditambahkan',
            'data'    => $stok
        ], 201);
    }

    /**
     * Tampilkan detail stok
     */
    public function show($id)
    {
        $stok = Stok::with(['barang', 'user'])->findOrFail($id);

        return response()->json([
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
            'barang_id' => 'required|exists:barangs,id',
            'user_id'   => 'required|exists:users,id',
            'jumlah'    => 'required|integer|min:1',
            'tgl_masuk' => 'required|date',
            'tgl_keluar'=> 'nullable|date|after_or_equal:tgl_masuk',
            'harga'     => 'required|numeric|min:0'
        ]);

        $stok->update($validated);

        return response()->json([
            'message' => 'Stok berhasil diperbarui',
            'data'    => $stok
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
