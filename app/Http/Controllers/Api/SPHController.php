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

        $data['tanggal'] = $data['tanggal'] ?? Carbon::now()->toDateString();

        $tahun = date('Y', strtotime($data['tanggal']));
        $bulan = date('n', strtotime($data['tanggal']));

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

        $kodeSurat = 'SPH';
        $kodeDivisi = 'XRAY';
        $kodePerusahaan = 'RNS';

        $lastSph = SPH::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('nomor_sph', 'LIKE', "%/{$kodeSurat}/{$kodeDivisi}/{$kodePerusahaan}-%/$tahun")
            ->latest('id')
            ->first();

        if ($lastSph) {
            $lastNumber = (int) explode('/', $lastSph->nomor_sph)[0];
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        $nomorSurat = "{$newNumber}/{$kodeSurat}/{$kodeDivisi}/{$kodePerusahaan}-{$bulanRomawi}/{$tahun}";

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

    $data['tanggal'] = $data['tanggal'] ?? $sph->tanggal;

    unset($data['nomor_sph']);

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
