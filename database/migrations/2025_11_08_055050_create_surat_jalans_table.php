<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penerima');
            $table->string('alamat_penerima');
            $table->string('telp_penerima');
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->string('nama_barang_jasa');
            $table->integer('qty');
            $table->decimal('jumlah', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_jalans');
    }
};
