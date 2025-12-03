<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_invoice',
        'pembelian_id',
        'nama_penerima',
        'tanggal_invoice',
        'nama_barang',
        'qty',
        'harga_satuan',
        'total_harga',
        'total_pembayaran',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
    ];


    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
