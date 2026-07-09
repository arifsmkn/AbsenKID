<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys=off');
        DB::statement('ALTER TABLE doorprizes RENAME TO doorprizes_old');

        DB::statement("
            CREATE TABLE doorprizes (
                id integer primary key autoincrement not null,
                event_id integer not null,
                nama_hadiah varchar not null,
                gambar varchar,
                jumlah integer not null default '1',
                urutan integer not null default '0',
                created_at datetime,
                updated_at datetime,
                type varchar not null default 'doorprize',
                foreign key(event_id) references events(id) on delete cascade
            )
        ");

        DB::statement('INSERT INTO doorprizes SELECT * FROM doorprizes_old');
        DB::statement('DROP TABLE doorprizes_old');
        DB::statement('PRAGMA foreign_keys=on');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys=off');
        DB::table('doorprizes')->where('type', 'doorprize_utama')->update(['type' => 'doorprize']);
        DB::statement('ALTER TABLE doorprizes RENAME TO doorprizes_old');

        DB::statement("
            CREATE TABLE doorprizes (
                id integer primary key autoincrement not null,
                event_id integer not null,
                nama_hadiah varchar not null,
                gambar varchar,
                jumlah integer not null default '1',
                urutan integer not null default '0',
                created_at datetime,
                updated_at datetime,
                type varchar check (type in ('doorprize', 'grand_prize')) not null default 'doorprize',
                foreign key(event_id) references events(id) on delete cascade
            )
        ");

        DB::statement('INSERT INTO doorprizes SELECT * FROM doorprizes_old');
        DB::statement('DROP TABLE doorprizes_old');
        DB::statement('PRAGMA foreign_keys=on');
    }
};
