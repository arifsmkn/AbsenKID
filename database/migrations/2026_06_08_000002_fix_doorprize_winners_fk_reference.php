<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrasi sebelumnya (rename doorprizes -> doorprizes_old) membuat SQLite
     * otomatis memperbarui foreign key di doorprize_winners menjadi merujuk ke
     * "doorprizes_old", yang kemudian dihapus — menyisakan referensi menggantung.
     * Migrasi ini membangun ulang tabel doorprize_winners agar FK merujuk ke
     * "doorprizes" yang benar.
     */
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('ALTER TABLE doorprize_winners RENAME TO doorprize_winners_old');

        DB::statement('
            CREATE TABLE "doorprize_winners" (
                "id" integer primary key autoincrement not null,
                "doorprize_id" integer not null,
                "event_id" integer not null,
                "employee_npk" varchar not null,
                "won_at" datetime not null,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("doorprize_id") references "doorprizes"("id") on delete cascade,
                foreign key("event_id") references "events"("id") on delete cascade,
                foreign key("employee_npk") references "employees"("npk")
            )
        ');

        DB::statement('
            INSERT INTO doorprize_winners (id, doorprize_id, event_id, employee_npk, won_at, created_at, updated_at)
            SELECT id, doorprize_id, event_id, employee_npk, won_at, created_at, updated_at FROM doorprize_winners_old
        ');

        DB::statement('DROP TABLE doorprize_winners_old');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Tidak ada rollback — perbaikan referensi FK yang rusak.
    }
};
