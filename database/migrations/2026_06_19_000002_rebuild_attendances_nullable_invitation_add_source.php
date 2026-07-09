<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// SQLite tidak support ALTER COLUMN, jadi rebuild table untuk:
// 1. Buat invitation_id nullable (manual admin tanpa undangan)
// 2. Tambah kolom source (scan_qr | manual_admin)
return new class extends Migration {
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('ALTER TABLE attendances RENAME TO attendances_old');

        DB::statement('
            CREATE TABLE "attendances" (
                "id" integer primary key autoincrement not null,
                "invitation_id" integer,
                "event_id" integer not null,
                "employee_npk" varchar(20) not null,
                "scanned_at" datetime not null,
                "scanned_by" varchar,
                "source" varchar not null default \'scan_qr\',
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("invitation_id") references "invitations"("id") on delete set null,
                foreign key("event_id") references "events"("id") on delete cascade,
                foreign key("employee_npk") references "employees"("npk")
            )
        ');

        DB::statement('
            INSERT INTO attendances (id, invitation_id, event_id, employee_npk, scanned_at, scanned_by, source, created_at, updated_at)
            SELECT id, invitation_id, event_id, employee_npk, scanned_at, scanned_by, \'scan_qr\', created_at, updated_at
            FROM attendances_old
        ');

        DB::statement('DROP TABLE attendances_old');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Tidak di-rollback (data live)
    }
};
