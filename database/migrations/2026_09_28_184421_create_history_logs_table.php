<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('history_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('module'); // Contoh: 'ATP Distribusi', 'OPM Reader', 'Upload Template'
            $table->string('action_type')->default('Generate'); // Generate, Upload, Delete, dll
            $table->string('region')->nullable();
            $table->string('target_name')->nullable(); // Nama OLT, Nama File, atau Objek terkait
            $table->text('description')->nullable(); // Keterangan tambahan yang bebas
            $table->string('status')->default('Selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_logs');
    }
};