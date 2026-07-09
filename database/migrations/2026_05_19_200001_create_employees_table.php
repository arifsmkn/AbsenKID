<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('npk', 20)->primary();
            $table->string('nama');
            $table->string('subco');
            $table->string('jabatan');
            $table->string('ukuran_baju', 5)->nullable();
            $table->string('email')->nullable();
            $table->string('no_telpon', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
