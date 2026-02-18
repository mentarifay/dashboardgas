<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // 'create', 'update', 'delete', 'login', 'logout'
            $table->string('table_name')->nullable(); // 'volume_gas', 'users', etc
            $table->bigInteger('record_id')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index('table_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};