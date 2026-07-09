<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('employee_npk', 20);
            $table->foreign('employee_npk')->references('npk')->on('employees');
            $table->enum('status', ['hadir', 'tidak_hadir']);
            $table->timestamp('confirmed_at');
            $table->timestamps();
            $table->unique(['event_id', 'employee_npk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_confirmations');
    }
};
