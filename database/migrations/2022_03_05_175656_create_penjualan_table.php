<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->string('id_penjualan');
            $table->string('id_barang');
            $table->string('id_alamat');
            $table->dateTime('tanggal_penjualan');
            $table->integer('harga_barang');
            $table->integer('jumlah_barang');
            $table->string('id_voucher');
            $table->integer('potongan');
            $table->integer('total_harga');
            $table->string('jasa_pengiriman');
            $table->integer('ongkir');
            $table->string('status');
            $table->string('nomor_resi');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penjualan');
    }
};
