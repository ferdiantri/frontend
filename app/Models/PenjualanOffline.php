<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanOffline extends Model
{
    public function barang(){
        return $this->belongsTo(Barang::class);
    }
    use HasFactory;
    protected $table = 'penjualan_offline';
    protected $fillable = [
        'id_penjualan_offline',
        'tanggal_penjualan',
        'nama_pembeli',
        'alamat',
        'nomor_telepon',
        'id_barang',
        'jumlah_barang',
        'harga',
        'total_harga',
        'email',
    ];
}
