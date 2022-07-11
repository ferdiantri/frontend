<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komplain extends Model
{
    use HasFactory;
    protected $table = 'komplain';
    protected $fillable = [
        'id_komplain',
        'id_penjualan',
        'masalah',
        'deskripsi_masalah',
        'link_youtube',
        'tanggapan_admin',
        'status',
        'tanggal_komplain',
        'email',
        'email_admin'
    ];
}
