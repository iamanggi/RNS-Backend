<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Pembelian;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json(Invoice::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal_invoice' => 'nullable|date',
            'nama_penerima' => 'required|string',
            'pembelian_id' => 'nullable|exists:pembelians,id',
            'items' => 'nullable|array',
            'items.*.nama_barang' => 'required_without:pembelian_id|string',
            'items.*.qty' => 'required_without:pembelian_id|integer',
            'items.*.harga_satuan' => 'required_without:pembelian_id|numeric',
        ]);

        $tanggal = $data['tanggal_invoice'] ?? now()->format('Y-m-d');
        $bulan = date('n', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
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

        $kode = 'INV-SPH';
        $lastInvoice = Invoice::whereYear('tanggal_invoice', $tahun)
            ->whereMonth('tanggal_invoice', $bulan)
            ->where('nomor_invoice', 'LIKE', "%/{$kode}/{$bulanRomawi}/{$tahun}")
            ->latest('id')->first();

        $newNumber = $lastInvoice
            ? str_pad(((int)explode('/', $lastInvoice->nomor_invoice)[0]) + 1, 2, '0', STR_PAD_LEFT)
            : '01';

        $nomorInvoice = "{$newNumber}/{$kode}/{$bulanRomawi}/{$tahun}";

        $invoice = Invoice::create([
            'tanggal_invoice' => $tanggal,
            'nomor_invoice' => $nomorInvoice,
            'nama_penerima' => $data['nama_penerima'],
            'pembelian_id' => $data['pembelian_id'] ?? null,
            'total_pembayaran' => 0, // Will be updated
        ]);

        $totalPembayaran = 0;

        if (!empty($data['pembelian_id'])) {
            $pembelian = Pembelian::with('items')->findOrFail($data['pembelian_id']);
            foreach ($pembelian->items as $item) {
                $invoice->items()->create([
                    'nama_barang' => $item->nama_barang,
                    'qty' => $item->jumlah,
                    'harga_satuan' => $item->harga_satuan,
                    'total_harga' => $item->total_harga,
                ]);
                $totalPembayaran += $item->total_harga;
            }
        } elseif (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $totalHarga = $item['qty'] * $item['harga_satuan'];
                $invoice->items()->create([
                    'nama_barang' => $item['nama_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $totalHarga,
                ]);
                $totalPembayaran += $totalHarga;
            }
        }

        $invoice->update(['total_pembayaran' => $totalPembayaran]);

        return response()->json([
            'message' => 'Invoice berhasil dibuat',
            'nomor_invoice' => $nomorInvoice,
            'data' => $invoice->load('items')
        ], 201);
    }


    public function show($id)
    {
        return response()->json(Invoice::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        $data = $request->validate([
            'tanggal_invoice' => 'nullable|date',
            'nama_penerima' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'nullable|exists:invoice_items,id', // untuk update item lama
            'items.*.nama_barang' => 'required|string',
            'items.*.qty' => 'required|integer',
            'items.*.harga_satuan' => 'required|numeric',
        ]);

        $invoice->update([
            'tanggal_invoice' => $data['tanggal_invoice'] ?? $invoice->tanggal_invoice,
            'nama_penerima' => $data['nama_penerima'],
        ]);

        $totalPembayaran = 0;
        $itemIds = [];

        foreach ($data['items'] as $item) {
            $totalHarga = $item['qty'] * $item['harga_satuan'];
            $totalPembayaran += $totalHarga;

            if (isset($item['id'])) {
                $invoiceItem = $invoice->items()->find($item['id']);
                if ($invoiceItem) {
                    $invoiceItem->update([
                        'nama_barang' => $item['nama_barang'],
                        'qty' => $item['qty'],
                        'harga_satuan' => $item['harga_satuan'],
                        'total_harga' => $totalHarga,
                    ]);
                    $itemIds[] = $invoiceItem->id;
                }
            } else {
                $newItem = $invoice->items()->create([
                    'nama_barang' => $item['nama_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $totalHarga,
                ]);
                $itemIds[] = $newItem->id;
            }
        }

        $invoice->items()->whereNotIn('id', $itemIds)->delete();

        $invoice->update(['total_pembayaran' => $totalPembayaran]);

        return response()->json([
            'message' => 'Invoice berhasil diperbarui',
            'data' => $invoice->load('items')
        ]);
    }


    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
