<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianItem extends Model
{
    /**
     * Nama tabel yang terkait dengan model ini.
     * @var string
     */
    protected $table = 'pembelian_items';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * @var array
     */
    protected $fillable = [
        'pembelian_id',
        'barang_id',
        'nama_barang',
        'jumlah',
        'harga_satuan',
        'total_harga',
    ];

    /**
     * Relasi ke model Pembelian.
     * Item pembelian ini dimiliki oleh satu Pembelian.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    /**
     * Relasi ke model Barang.
     * Item pembelian ini memiliki satu Barang.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barang()
    {
        return $this->belongsTo(Stok::class, 'barang_id');
    }
}
