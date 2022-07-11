<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    public function barang(){
        return $this->belongsTo(Barang::class);
    }
    use HasFactory;
    protected $table = 'penjualan';
    protected $fillable = [
        'id_penjualan',
        'id_barang',
        'id_alamat',
        'tanggal_penjualan',
        'harga_barang',
        'jumlah_barang',
        'id_voucher',
        'potongan',
        'total_harga',
        'jasa_pengiriman',
        'ongkir',
        'status',
        'nomor_resi',
        'email',
    ];
}
