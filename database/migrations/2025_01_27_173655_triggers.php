<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('CREATE TRIGGER avaliacao_mentoria
AFTER UPDATE ON sessao_mentorias
FOR EACH ROW
BEGIN
UPDATE mentorias SET avaliacao = (
        SELECT AVG(avaliacao)
        FROM sessao_mentorias sm
        WHERE sm.mentoria_id = NEW.mentoria_id
    )
WHERE mentoria.id = NEW.mentoria_id;
END');

        DB::unprepared('CREATE TRIGGER avaliacao_mentor
AFTER UPDATE ON mentorias
FOR EACH ROW
BEGIN
UPDATE mentors SET avaliacao = (
        SELECT AVG(avaliacao)
        FROM mentorias me
        WHERE me.mentor_id = NEW.mentor_id
    )
WHERE mentors.id = NEW.mentor_id;
END');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS avaliacao_mentor');
        DB::unprepared('DROP TRIGGER IF EXISTS avaliacao_mentoria');
    }
};
