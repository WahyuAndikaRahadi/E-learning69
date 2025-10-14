<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahPelanggaranToWaktuUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('waktu_ujian', function (Blueprint $table) {
            $table->integer('jumlah_pelanggaran')->default(0)->after('selesai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waktu_ujian', function (Blueprint $table) {
            $table->dropColumn('jumlah_pelanggaran');
        });
    }
}