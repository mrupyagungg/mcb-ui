<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('region', 100)->nullable();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['team_id', 'status']);
            $table->index(['region', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};