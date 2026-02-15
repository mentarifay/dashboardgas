<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('volume_gas', function (Blueprint $table) {
            $table->date('bulan_date')->nullable()->after('bulan');
        });
    }

    public function down()
    {
        Schema::table('volume_gas', function (Blueprint $table) {
            $table->dropColumn('bulan_date');
        });
    }
};