<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('no_order')->unique(); 
            $table->string('penerima_nama');
            $table->text('penerima_alamat');
            $table->string('penerima_telepon')->nullable();

            $table->date('tgl_transaksi');
            $table->enum('status_pengiriman', ['dikirim', 'menunggu', 'cicilan']);
            $table->enum('status_pembayaran', ['belum_lunas', 'cicilan', 'lunas']);
            $table->decimal('total_cicilan', 15, 2)->default(0);
            $table->decimal('sisa_cicilan', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2); 
            $table->timestamps();
        });

        Schema::create('pembelian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
            $table->string('nama_barang'); 
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_items');
        Schema::dropIfExists('pembelians');
    }
};
