<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stoks', function (Blueprint $table) {
            $table->string('merek')->nullable()->after('nama_barang');
            $table->string('kode_sku')->nullable()->after('merek');
            $table->integer('panjang')->nullable()->after('satuan');
            $table->integer('lebar')->nullable()->after('panjang');
            $table->integer('tinggi')->nullable()->after('lebar');
            $table->integer('berat')->nullable()->after('tinggi');
        });
    }

    public function down(): void
    {
        Schema::table('stoks', function (Blueprint $table) {
        });
    }
};
