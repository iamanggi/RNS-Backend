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
        Schema::create('stoks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->string('foto')->nullable();
            $table->string('video')->nullable();
            $table->decimal('harga', 15, 2);
            $table->integer('jumlah');
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};
