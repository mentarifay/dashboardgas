<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volume_gas', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('daily_average_mmscfd')->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            
            // Index untuk query cepat
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('volume_gas', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};