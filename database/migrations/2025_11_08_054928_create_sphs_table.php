<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
   Schema::create('surat_penawarans', function (Blueprint $table) {
    $table->id();
    $table->string('nomor_sph')->unique(); // ✅ Tambahkan kolom ini
    $table->date('tanggal')->nullable();
    $table->string('tempat')->nullable();
    $table->string('lampiran')->nullable();
    $table->string('hal')->nullable();
    $table->string('jabatan_tujuan')->nullable();
    $table->string('nama_perusahaan');
    $table->json('detail_barang'); // ✅ kalau kamu simpan array barang
    $table->decimal('total_keseluruhan', 15, 2);
    $table->string('penandatangan');
    $table->timestamps();
});

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sphs');
    }
};
