<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
      protected $fillable = [
        'nama',
        'sku',
        'kategori',
        'merek',
        'deskripsi',
        'foto',
        'video',
        'tanggal',
        'harga_jual',
        'jumlah',
        'stok_tersedia',
        'satuan',
        'panjang',
        'lebar',
        'tinggi',
        'berat',
    ];

    public function stok()
    {
        return $this->hasMany(Stok::class);
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }

     public function pembelianItems()
    {
        return $this->hasMany(PembelianItem::class);
    }
}
