<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Função para calcular média da mentoria
        DB::unprepared('
            CREATE OR REPLACE FUNCTION calcular_media_mentoria()
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE mentorias
                SET avaliacao = (
                    SELECT AVG(avaliacao)
                    FROM sessao_mentorias sm
                    WHERE sm.mentoria_id = NEW.mentoria_id
                )
                WHERE id = NEW.mentoria_id;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Função para calcular média do mentor
        DB::unprepared('
            CREATE OR REPLACE FUNCTION calcular_media_mentor()
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE mentors
                SET avaliacao = (
                    SELECT AVG(avaliacao)
                    FROM mentorias me
                    WHERE me.mentor_id = NEW.mentor_id
                )
                WHERE id = NEW.mentor_id;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Criar os triggers
        DB::unprepared('
            CREATE TRIGGER avaliacao_mentoria
            AFTER UPDATE OF avaliacao ON sessao_mentorias
            FOR EACH ROW
            EXECUTE FUNCTION calcular_media_mentoria();
        ');

        DB::unprepared('
            CREATE TRIGGER avaliacao_mentor
            AFTER UPDATE OF avaliacao ON mentorias
            FOR EACH ROW
            EXECUTE FUNCTION calcular_media_mentor();
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers
        DB::unprepared('DROP TRIGGER IF EXISTS avaliacao_mentor ON mentorias;');
        DB::unprepared('DROP TRIGGER IF EXISTS avaliacao_mentoria ON sessao_mentorias;');

        // Drop functions
        DB::unprepared('DROP FUNCTION IF EXISTS calcular_media_mentor();');
        DB::unprepared('DROP FUNCTION IF EXISTS calcular_media_mentoria();');
    }
};
