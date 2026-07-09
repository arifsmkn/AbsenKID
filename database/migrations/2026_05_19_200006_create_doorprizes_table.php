<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doorprizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('nama_hadiah');
            $table->string('gambar')->nullable();
            $table->integer('jumlah')->default(1);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('doorprize_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doorprize_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('employee_npk', 20);
            $table->foreign('employee_npk')->references('npk')->on('employees');
            $table->timestamp('won_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doorprize_winners');
        Schema::dropIfExists('doorprizes');
    }
};
