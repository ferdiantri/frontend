<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;
    protected $table = 'alamat';
    protected $fillable = [
        'id_alamat',
        'email',
        'nomor_telepon',
        'nama_penerima',
        'alamat',
        'province_id',
        'city_id',
        'provinsi',
        'kecamatan',
        'kabupaten',
        'kode_pos', 
    ];
}
