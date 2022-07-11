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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->string('id_pembelian')->unique()->primary_key();
            $table->string('id_barang');
            $table->dateTime('tanggal_pembelian');
            $table->string('nama_barang');
            $table->string('ram');
            $table->string('internal');
            $table->string('warna');
            $table->integer('jumlah_barang');
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->integer('total_harga');
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
        Schema::dropIfExists('pembelian');
    }
};
