<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('volume_gas', function (Blueprint $table) {
            $table->id();
            $table->enum('data', ['PENYALURAN']); 
            $table->string('shipper');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->string('periode'); // Jan-20, Feb-20, dst
            $table->decimal('daily_average_mmscfd', 10, 2);
            $table->timestamps();

            // Index untuk query cepat
            $table->index('data');
            $table->index(['tahun', 'bulan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('volume_gas');
    }
};