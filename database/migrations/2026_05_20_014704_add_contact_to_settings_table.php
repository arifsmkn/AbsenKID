<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Seed default contact settings via seeder, no schema change needed
        \App\Models\Setting::set('panitia_whatsapp', '');
        \App\Models\Setting::set('panitia_email', '');
        \App\Models\Setting::set('panitia_nama', 'Panitia Konvensi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
