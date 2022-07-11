<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    protected $table = 'pembelian';
    protected $fillable = [
        'id_pembelian',
        'id_barang',
        'tanggal_pembelian',
        'nama_barang',
        'ram',
        'internal',
        'warna',
        'jumlah_barang',
        'harga_beli',
        'harga_jual',
        'total_harga',
        'email',
    ];
}
