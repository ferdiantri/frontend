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
        Schema::create('barang', function (Blueprint $table) {
            $table->string('id_barang')->unique()->primary_key();
            $table->string('gambar');
            $table->string('nama_barang');
            $table->string('ram');
            $table->string('internal');
            $table->string('warna');
            $table->string('kamera_depan');
            $table->string('kamera_belakang');
            $table->string('layar');
            $table->string('chipset');
            $table->string('baterai');
            $table->integer('harga');
            $table->integer('stok_barang');
            $table->integer('terjual');
            $table->string('status');
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
        Schema::dropIfExists('barang');
    }
};
