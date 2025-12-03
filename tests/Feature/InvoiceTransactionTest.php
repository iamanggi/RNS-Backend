<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\Stok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_invoice_from_pembelian()
    {
        // 1. Create User and Authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Create Stok (Barang)
        $barang = Stok::create([
            'nama_barang' => 'Laptop Gaming',
            'harga' => 15000000,
            'jumlah' => 10,
            'satuan' => 'Unit',
            'kode_sku' => 'LPT-001',
            'user_id' => $user->id,
        ]);

        // 3. Create Pembelian (Transaction)
        $pembelian = Pembelian::create([
            'no_order' => 'ORD-001',
            'penerima_nama' => 'John Doe',
            'penerima_alamat' => 'Jl. Test No. 123',
            'penerima_telepon' => '08123456789',
            'tgl_transaksi' => now(),
            'status_pengiriman' => 'menunggu',
            'status_pembayaran' => 'belum_lunas',
            'grand_total' => 30000000,
        ]);

        // 4. Create Pembelian Items
        PembelianItem::create([
            'pembelian_id' => $pembelian->id,
            'barang_id' => $barang->id,
            'nama_barang' => $barang->nama_barang, // Usually copied for history
            'jumlah' => 2,
            'harga_satuan' => 15000000,
            'total_harga' => 30000000,
        ]);

        // 5. Call Invoice Store Endpoint
        $response = $this->postJson('/api/invoice', [
            'pembelian_id' => $pembelian->id,
            'tanggal_invoice' => now()->format('Y-m-d'),
        ]);

        // 6. Assertions
        $response->assertStatus(201);
        
        $this->assertDatabaseHas('invoices', [
            'pembelian_id' => $pembelian->id,
            'nama_penerima' => 'John Doe',
            'total_pembayaran' => 30000000,
        ]);

        $invoice = Invoice::where('pembelian_id', $pembelian->id)->first();
        
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'nama_barang' => 'Laptop Gaming',
            'qty' => 2,
            'harga_satuan' => 15000000,
            'total_harga' => 30000000,
        ]);
    }
}
