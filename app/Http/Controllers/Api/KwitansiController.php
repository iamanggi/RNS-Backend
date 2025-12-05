<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kwitansi;
use Illuminate\Http\Request;

class KwitansiController extends Controller
{
    public function index()
    {
        return response()->json(Kwitansi::all());
    }

    public function store(Request $request)
    {
        // 1. Tambahkan 'status' dan 'tanggal' ke validasi
        $data = $request->validate([
            'nama_penerima' => 'required|string',
            'alamat_penerima' => 'required|string',
            'total_pembayaran' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'tanggal' => 'nullable|date',
        ]);

        // 2. Gunakan tanggal dari input user, jika kosong baru pakai hari ini
        $tanggal = $data['tanggal'] ?? now()->format('Y-m-d');

        $bulan = date('n', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ][$bulan];

        $kode = 'RNS/AKUN';

        $last = Kwitansi::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('nomor_kwitansi', 'LIKE', "%/{$kode}/{$bulanRomawi}/{$tahun}")
            ->latest('id')
            ->first();

        if ($last) {
            $lastNumber = (int) explode('/', $last->nomor_kwitansi)[0];
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        $nomor_kwitansi = "{$newNumber}/{$kode}/{$bulanRomawi}/{$tahun}";

        $total_bilangan = $this->terbilang($data['total_pembayaran']);

        $kwitansi = Kwitansi::create([
            'nomor_kwitansi' => $nomor_kwitansi,
            'tanggal' => $tanggal,
            'nama_penerima' => $data['nama_penerima'],
            'alamat_penerima' => $data['alamat_penerima'],
            'total_pembayaran' => $data['total_pembayaran'],
            'total_bilangan' => $total_bilangan,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return response()->json([
            'message' => 'Kwitansi berhasil dibuat',
            'data' => $kwitansi
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Kwitansi::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kwitansi = Kwitansi::findOrFail($id);
        
        // Tambahkan validasi untuk update juga
        $data = $request->validate([
            'nama_penerima' => 'nullable|string',
            'alamat_penerima' => 'nullable|string',
            'total_pembayaran' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'tanggal' => 'nullable|date',
        ]);

        if (isset($data['total_pembayaran'])) {
            $data['total_bilangan'] = $this->terbilang($data['total_pembayaran']);
        }

        $kwitansi->update($data);

        return response()->json([
            'message' => 'Kwitansi berhasil diperbarui',
            'data' => $kwitansi
        ]);
    }

    public function destroy($id)
    {
        Kwitansi::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    private function terbilang($number)
    {
        $formatter = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
        $terbilang = ucfirst($formatter->format($number));
        return "{$terbilang} rupiah";
    }
}
