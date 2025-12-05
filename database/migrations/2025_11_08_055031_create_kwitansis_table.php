<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('kwitansis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kwitansi')->unique();
            $table->date('tanggal');
            $table->string('nama_penerima');
            $table->string('alamat_penerima');
            $table->decimal('total_pembayaran', 15, 2)->default(0);
            $table->string('total_bilangan')->nullable();
            $table->string('keterangan')->default('Lunas');
            $table->string('status')->default('belum diterima'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kwitansis');
    }
};
