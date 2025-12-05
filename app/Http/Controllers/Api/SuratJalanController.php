<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    public function index()
    {
        $data = SuratJalan::latest()->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'alamat_penerima' => 'required|string|max:500',
            'telp_penerima' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'nama_barang_jasa' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'jumlah' => 'required|numeric|min:0',
            'nama_pengirim' => 'required|string|max:255', 
        ]);

        if (!empty($validated['tanggal'])) {
            $validated['tanggal'] = date('Y-m-d', strtotime($validated['tanggal']));
        } else {
            $validated['tanggal'] = date('Y-m-d'); 
        }

        $sj = SuratJalan::create($validated);

        return response()->json([
            'message' => 'Surat jalan berhasil dibuat',
            'data' => $sj
        ], 201);
    }

    public function show($id)
    {
        $sj = SuratJalan::findOrFail($id);
        return response()->json($sj);
    }

    public function update(Request $request, $id)
    {
        $sj = SuratJalan::findOrFail($id);

        $validated = $request->validate([
            'nama_penerima' => 'sometimes|string|max:255',
            'alamat_penerima' => 'sometimes|string|max:500',
            'telp_penerima' => 'sometimes|string|max:20',
            'keterangan' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'nama_barang_jasa' => 'sometimes|string|max:255',
            'qty' => 'sometimes|integer|min:1',
            'jumlah' => 'sometimes|numeric|min:0',
            'nama_pengirim' => 'sometimes|string|max:255', 
        ]);

        $sj->update($validated);

        return response()->json([
            'message' => 'Data surat jalan berhasil diperbarui',
            'data' => $sj
        ]);
    }

    public function destroy($id)
    {
        $sj = SuratJalan::findOrFail($id);
        $sj->delete();

        return response()->json(['message' => 'Data surat jalan berhasil dihapus']);
    }
}
