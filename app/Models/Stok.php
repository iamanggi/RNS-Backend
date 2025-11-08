<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $fillable = [
        'barang_id', 
        'user_id', 
        'jumlah', 
        'tgl_masuk', 
        'tgl_keluar', 
        'harga'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
