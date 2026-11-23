<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable();
            $table->string('vendor')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('bank')->nullable();
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable();
            $table->string('employee_id')->nullable()->index();
            $table->date('start_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'vendor',
                'no_rekening',
                'bank',
                'phone',
                'id_number',
                'employee_id',
                'start_date',
            ]);
        });
    }
};