<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Support\Facades\DB;

   return new class extends Migration
   {
       public function up()
       {
           // ALTER TABLE yang udah ada
           DB::statement("ALTER TABLE volume_gas MODIFY COLUMN data ENUM('PENYALURAN', 'PENERIMAAN')");
       }

       public function down()
       {
           DB::statement("ALTER TABLE volume_gas MODIFY COLUMN data ENUM('PENYALURAN')");
       }
   };