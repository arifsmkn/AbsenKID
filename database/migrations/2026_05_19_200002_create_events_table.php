<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('tahun');
            $table->string('tema')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('logo')->nullable();
            $table->string('maps_embed')->nullable();
            $table->string('lokasi')->nullable();
            $table->date('tanggal')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('theme_config')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
