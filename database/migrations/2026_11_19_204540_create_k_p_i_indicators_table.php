<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('kategori', 50);
            $table->string('jenis', 20); // target atau rubrik
            $table->decimal('bobot', 5, 2);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kategori', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_indicators');
    }
};