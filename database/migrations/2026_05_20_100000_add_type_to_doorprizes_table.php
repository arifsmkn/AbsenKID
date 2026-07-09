<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('doorprizes', function (Blueprint $table) {
            $table->enum('type', ['doorprize', 'grand_prize'])->default('doorprize')->after('urutan');
        });
    }

    public function down(): void
    {
        Schema::table('doorprizes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
