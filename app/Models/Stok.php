<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $fillable = [
        'nama_barang',
        'foto',
        'video',
        'harga',
        'jumlah',
        'satuan',
        'merek',
        'kode_sku',
        'panjang',
        'lebar',
        'tinggi',
        'berat',
        'tgl_masuk',
        'tgl_keluar',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
