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
        Schema::create('alamat', function (Blueprint $table) {
            $table->string('id_alamat')->unique()->primary_key();
            $table->string('email');
            $table->string('nomor_telepon');
            $table->string('nama_penerima');
            $table->string('alamat');
            $table->string('provinsi');
            $table->string('province_id');
            $table->string('kabupaten');
            $table->string('city_id');
            $table->string('kecamatan');
            $table->integer('kode_pos');
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
        Schema::dropIfExists('alamat');
    }
};
