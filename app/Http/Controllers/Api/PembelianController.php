<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembelianController extends Controller
{
    // Tampilkan semua pembelian
    public function index(Request $request)
    {
        $status = $request->status; // 'belum_lunas', 'cicilan', 'lunas'
        $query = Pembelian::with('items')->latest();

        if ($status) {
            $query->where('status_pembayaran', $status);
        }

        return response()->json($query->get());
    }

    // Simpan pembelian baru beserta items
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_order'           => 'required|unique:pembelians,no_order',
            'penerima_nama'      => 'required|string',
            'penerima_alamat'    => 'required|string',
            'penerima_telepon'   => 'nullable|string',
            'tgl_transaksi'      => 'required|date',
            'status_pengiriman'  => 'required|in:dikirim,menunggu,cicilan',
            'status_pembayaran'  => 'required|in:cicilan,lunas,belum_lunas',
            'total_cicilan'      => 'nullable|numeric',
            'sisa_cicilan'       => 'nullable|numeric',
            'grand_total'        => 'required|numeric',
            'items'              => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.jumlah'     => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric',
            'items.*.total_harga'  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::create([
                'no_order'          => $request->no_order,
                'penerima_nama'     => $request->penerima_nama,
                'penerima_alamat'   => $request->penerima_alamat,
                'penerima_telepon'  => $request->penerima_telepon,
                'tgl_transaksi'     => $request->tgl_transaksi,
                'status_pengiriman' => $request->status_pengiriman,
                'status_pembayaran' => $request->status_pembayaran,
                'total_cicilan'     => $request->total_cicilan ?? 0,
                'sisa_cicilan'      => $request->sisa_cicilan ?? 0,
                'grand_total'       => $request->grand_total,
            ]);

            foreach ($request->items as $item) {
                PembelianItem::create([
                    'pembelian_id' => $pembelian->id,
                    'nama_barang'  => $item['nama_barang'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga'  => $item['total_harga'],
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Pembelian berhasil disimpan',
                'data' => $pembelian->load('items')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyimpan pembelian', 'detail' => $e->getMessage()], 500);
        }
    }

    // Tampilkan detail pembelian
    public function show($id)
    {
        $pembelian = Pembelian::with('items')->find($id);
        if (!$pembelian) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($pembelian);
    }

    // Update pembelian & items
    public function update(Request $request, $id)
    {
        $pembelian = Pembelian::find($id);
        if (!$pembelian) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'penerima_nama'      => 'required|string',
            'penerima_alamat'    => 'required|string',
            'penerima_telepon'   => 'nullable|string',
            'tgl_transaksi'      => 'required|date',
            'status_pengiriman'  => 'required|in:dikirim,menunggu,cicilan',
            'status_pembayaran'  => 'required|in:cicilan,lunas,belum_lunas',
            'total_cicilan'      => 'nullable|numeric',
            'sisa_cicilan'       => 'nullable|numeric',
            'grand_total'        => 'required|numeric',
            'items'              => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.jumlah'     => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric',
            'items.*.total_harga'  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $pembelian->update([
                'penerima_nama'     => $request->penerima_nama,
                'penerima_alamat'   => $request->penerima_alamat,
                'penerima_telepon'  => $request->penerima_telepon,
                'tgl_transaksi'     => $request->tgl_transaksi,
                'status_pengiriman' => $request->status_pengiriman,
                'status_pembayaran' => $request->status_pembayaran,
                'total_cicilan'     => $request->total_cicilan ?? 0,
                'sisa_cicilan'      => $request->sisa_cicilan ?? 0,
                'grand_total'       => $request->grand_total,
            ]);

            // Hapus items lama & simpan items baru
            PembelianItem::where('pembelian_id', $pembelian->id)->delete();
            foreach ($request->items as $item) {
                PembelianItem::create([
                    'pembelian_id' => $pembelian->id,
                    'nama_barang'  => $item['nama_barang'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga'  => $item['total_harga'],
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Pembelian berhasil diupdate',
                'data' => $pembelian->load('items')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal update pembelian', 'detail' => $e->getMessage()], 500);
        }
    }

    // Hapus pembelian & items
    public function destroy($id)
    {
        $pembelian = Pembelian::find($id);
        if (!$pembelian) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        try {
            $pembelian->delete();
            return response()->json(['message' => 'Pembelian berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus pembelian', 'detail' => $e->getMessage()], 500);
        }
    }
}
