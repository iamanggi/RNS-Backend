<?php

namespace App\Http\Controllers\Api;

use App\Models\SPH;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SPHController extends Controller
{
    public function index()
    {
        return response()->json(SPH::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'nullable|date',
            'tempat' => 'nullable|string',
            'lampiran' => 'nullable|string',
            'hal' => 'nullable|string',
            'jabatan_tujuan' => 'nullable|string',
            'nama_perusahaan' => 'required|string',
            'detail_barang' => 'required|array',
            'total_keseluruhan' => 'required|numeric',
            'penandatangan' => 'required|string',
        ]);

        // ✅ Jika tanggal tidak diisi, gunakan tanggal hari ini
        $data['tanggal'] = $data['tanggal'] ?? Carbon::now()->toDateString();

        // Ambil tahun dan bulan dari tanggal
        $tahun = date('Y', strtotime($data['tanggal']));
        $bulan = date('n', strtotime($data['tanggal']));

        // Ubah bulan ke Romawi
        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ][$bulan];

        // Tetapkan bagian kode yang tetap
        $kodeSurat = 'SPH';
        $kodeDivisi = 'XRAY';
        $kodePerusahaan = 'RNS';

        // Cek nomor terakhir di tahun & bulan yang sama
        $lastSph = SPH::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('nomor_sph', 'LIKE', "%/{$kodeSurat}/{$kodeDivisi}/{$kodePerusahaan}-%/$tahun")
            ->latest('id')
            ->first();

        // Ambil urutan terakhir lalu tambah 1
        if ($lastSph) {
            $lastNumber = (int) explode('/', $lastSph->nomor_sph)[0];
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        // Format nomor: 03/SPH/XRAY/RNS-II/2025
        $nomorSurat = "{$newNumber}/{$kodeSurat}/{$kodeDivisi}/{$kodePerusahaan}-{$bulanRomawi}/{$tahun}";

        // Simpan ke database
        $data['nomor_sph'] = $nomorSurat;
        $sph = SPH::create($data);

        return response()->json([
            'message' => 'SPH berhasil dibuat',
            'nomor_sph' => $nomorSurat,
            'data' => $sph
        ], 201);
    }


    public function show($id)
    {
        return response()->json(SPH::findOrFail($id));
    }

    public function update(Request $request, $id)
{
    $sph = SPH::findOrFail($id);

    // Validasi data
    $data = $request->validate([
        'tanggal' => 'nullable|date',
        'tempat' => 'nullable|string',
        'lampiran' => 'nullable|string',
        'hal' => 'nullable|string',
        'jabatan_tujuan' => 'nullable|string',
        'nama_perusahaan' => 'nullable|string',
        'detail_barang' => 'nullable|array',
        'total_keseluruhan' => 'nullable|numeric',
        'penandatangan' => 'nullable|string',
    ]);

    // Jika tanggal tidak dikirim, gunakan tanggal lama
    $data['tanggal'] = $data['tanggal'] ?? $sph->tanggal;

    // Pastikan nomor_sph tidak diubah
    unset($data['nomor_sph']);

    // Update data ke database
    $sph->update($data);

    return response()->json([
        'message' => 'Data SPH berhasil diperbarui',
        'data' => $sph
    ]);
}


    public function destroy($id)
    {
        SPH::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
