<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_penerima',
        'alamat_penerima',
        'telp_penerima',
        'keterangan',
        'tanggal',
        'nama_barang_jasa',
        'qty',
        'jumlah',
        'nama_pengirim'
    ];
}
