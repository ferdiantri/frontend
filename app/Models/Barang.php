<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'barang';
    protected $fillable = [
        'id_barang',
        'gambar',
        'nama_barang',
        'ram',
        'internal',
        'warna',
        'kamera_depan',
        'kamera_belakang',
        'layar',
        'chipset',
        'baterai',
        'harga',
        'stok_barang',
        'terjual',
        'status',
        'email',
    ];
}
