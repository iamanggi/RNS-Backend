<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kwitansi',
        'tanggal',
        'nama_penerima',
        'alamat_penerima',
        'total_pembayaran',
        'total_bilangan',
        'keterangan',
        'status',
    ];


    protected $casts = [
        'tanggal' => 'date',
    ];
}
