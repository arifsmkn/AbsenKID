<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invitation_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('employee_npk', 20);
            $table->enum('channel', ['whatsapp', 'email']);
            $table->string('target');          // nomor WA atau alamat email
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['invitation_id', 'channel']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_sends');
    }
};
