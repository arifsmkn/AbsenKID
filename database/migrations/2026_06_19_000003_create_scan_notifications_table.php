<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scan_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('employee_npk', 20);
            $table->foreign('employee_npk')->references('npk')->on('employees');
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->timestamp('scanned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_notifications');
    }
};
