<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPenjualan extends Model
{
    use HasFactory;
    protected $table = 'log_penjualan';
    protected $fillable = [
        'id_penjualan',
        'status_log',
        'tanggal_penjualan_log',
    ];
}
