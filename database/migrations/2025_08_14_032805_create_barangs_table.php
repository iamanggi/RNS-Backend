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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama Barang
            $table->string('sku')->unique(); // Kode SKU
            $table->string('kategori'); // Bisa nanti relasi ke tabel kategori kalau mau
            $table->string('merek')->nullable();
            $table->text('deskripsi')->nullable();

            // Media
            $table->string('foto')->nullable(); // Simpan path foto
            $table->string('video')->nullable(); // Simpan path video

            // Harga & stok
            $table->date('tanggal')->nullable();
            $table->decimal('harga_jual', 15, 2);
            $table->integer('jumlah')->default(0); // jumlah pembelian awal
            $table->integer('stok_tersedia')->default(0);
            $table->enum('satuan', ['pcs', 'kg', 'box', 'liter'])->default('pcs');

            // Dimensi & berat
            $table->decimal('panjang', 8, 2)->nullable();
            $table->decimal('lebar', 8, 2)->nullable();
            $table->decimal('tinggi', 8, 2)->nullable();
            $table->decimal('berat', 10, 2)->nullable(); // gram

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
