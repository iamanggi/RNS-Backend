<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * Tampilkan semua barang 
     */
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->merek) {
            $query->where('merek', $request->merek);
        }

        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $barangs = $query->latest()->get();

        return response()->json([
            'data' => $barangs
        ]);
    }

    /**
     * Simpan barang baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:barangs,sku',
            'kategori'      => 'required|string|max:255',
            'merek'         => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tanggal'       => 'nullable|date',
            'harga_jual'    => 'required|numeric|min:0',
            'jumlah'        => 'nullable|integer|min:0',
            'stok_tersedia' => 'nullable|integer|min:0',
            'satuan'        => 'required|in:pcs,kg,box,liter',
            'panjang'       => 'nullable|numeric|min:0',
            'lebar'         => 'nullable|numeric|min:0',
            'tinggi'        => 'nullable|numeric|min:0',
            'berat'         => 'nullable|numeric|min:0',
            'foto'          => 'nullable|image|max:2048',
            'video'         => 'nullable|mimes:mp4,mov,avi|max:51200',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('barang/foto', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('barang/video', 'public');
        }

        $barang = Barang::create($validated);

        return response()->json([
            'message' => 'Barang berhasil ditambahkan',
            'data'    => $barang
        ], 201);
    }

    /**
     * Tampilkan detail barang
     */
    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return response()->json([
            'data' => $barang
        ]);
    }

    /**
     * Update barang
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:barangs,sku,' . $barang->id,
            'kategori'      => 'required|string|max:255',
            'merek'         => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tanggal'       => 'nullable|date',
            'harga_jual'    => 'required|numeric|min:0',
            'jumlah'        => 'nullable|integer|min:0',
            'stok_tersedia' => 'nullable|integer|min:0',
            'satuan'        => 'required|in:pcs,kg,box,liter',
            'panjang'       => 'nullable|numeric|min:0',
            'lebar'         => 'nullable|numeric|min:0',
            'tinggi'        => 'nullable|numeric|min:0',
            'berat'         => 'nullable|numeric|min:0',
            'foto'          => 'nullable|image|max:2048',
            'video'         => 'nullable|mimes:mp4,mov,avi|max:51200',
        ]);

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $validated['foto'] = $request->file('foto')->store('barang/foto', 'public');
        }

        if ($request->hasFile('video')) {
            if ($barang->video) {
                Storage::disk('public')->delete($barang->video);
            }
            $validated['video'] = $request->file('video')->store('barang/video', 'public');
        }

        $barang->update($validated);

        return response()->json([
            'message' => 'Barang berhasil diperbarui',
            'data'    => $barang
        ]);
    }

    /**
     * Hapus barang
     */
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }
        if ($barang->video) {
            Storage::disk('public')->delete($barang->video);
        }

        $barang->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus'
        ]);
    }
}
