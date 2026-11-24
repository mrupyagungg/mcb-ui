<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // Identitas aset
            $table->string('asset_code', 50)->unique();
            $table->string('category', 100);
            $table->string('brand', 100)->nullable();
            $table->string('model', 150)->nullable();
            $table->string('serial_number', 150)->nullable()->index();

            // Penempatan aset
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('area', 100)->nullable();
            $table->string('location', 150)->nullable();
            $table->string('vendor', 150)->nullable();

            // Status dan kondisi
            $table->string('status', 50)->default('Available');
            $table->string('condition', 50)->default('Good');

            // Informasi aset
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();

            // Perolehan aset
            $table->date('acquisition_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->date('warranty_expiry')->nullable();

            // Pengelolaan data
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index(['assigned_to', 'area']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};