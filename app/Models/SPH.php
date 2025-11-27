<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SPH extends Model
{
    use HasFactory;

    protected $table = 'surat_penawarans';
    protected $fillable = [
        'nomor_sph',
        'tanggal',
        'tempat',
        'lampiran',
        'hal',
        'jabatan_tujuan',
        'nama_perusahaan',
        'detail_barang',
        'total_keseluruhan',
        'penandatangan',
    ];

    protected $casts = [
        'detail_barang' => 'array',
        'tanggal' => 'date',
    ];
}
