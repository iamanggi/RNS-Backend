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

            // DATA BARANG
            $table->string('nama_barang');
            $table->string('foto')->nullable();        // path file
            $table->string('video')->nullable();       // path file
            $table->decimal('harga', 15, 2);           // harga barang

            // DATA STOK
            $table->integer('jumlah');                  // jumlah masuk/keluar
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_keluar')->nullable();

            // USER
            $table->unsignedBigInteger('user_id');      // siapa yang input

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};
