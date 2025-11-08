<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Tabel pembelians = data transaksi utama
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('no_order')->unique(); // misal ORD-2025-001

            // Data penerima langsung di sini
            $table->string('penerima_nama');
            $table->text('penerima_alamat');
            $table->string('penerima_telepon')->nullable();

            $table->date('tgl_transaksi');
            $table->enum('status_pengiriman', ['dikirim', 'menunggu', 'cicilan']);
            $table->enum('status_pembayaran', ['belum_lunas', 'cicilan', 'lunas']);
            $table->decimal('total_cicilan', 15, 2)->default(0);
            $table->decimal('sisa_cicilan', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2); // total keseluruhan semua barang
            $table->timestamps();
        });

        // Tabel pembelian_items = detail barang yang dibeli
        Schema::create('pembelian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_items');
        Schema::dropIfExists('pembelians');
    }
};
