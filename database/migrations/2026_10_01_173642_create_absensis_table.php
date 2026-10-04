<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->enum('status_masuk', ['tepat_waktu', 'terlambat'])->nullable();
            $table->decimal('latitude_masuk', 10, 7)->nullable();
            $table->decimal('longitude_masuk', 10, 7)->nullable();
            $table->text('jalan_masuk')->nullable();
            $table->string('kota_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->decimal('latitude_keluar', 10, 7)->nullable();
            $table->decimal('longitude_keluar', 10, 7)->nullable();
            $table->text('jalan_keluar')->nullable();
            $table->string('kota_keluar')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};