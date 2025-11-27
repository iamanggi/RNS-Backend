<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    /**
     * Nama tabel yang terkait dengan model ini.
     * @var string
     */
    protected $table = 'pembelians';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * @var array
     */
    protected $fillable = [
        'no_order',
        'penerima_nama',
        'penerima_alamat',
        'penerima_telepon',
        'tgl_transaksi',
        'status_pengiriman',
        'status_pembayaran',
        'total_cicilan',
        'sisa_cicilan',
        'grand_total',
    ];

    /**
     * Relasi ke model PembelianItem.
     * Satu pembelian memiliki banyak item pembelian.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(PembelianItem::class);
    }
}
