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
        Schema::create('sessao_mentorias', function (Blueprint $table) {
            $table->id();
            $table->datetime('data_hora_inicio');
            $table->datetime('data_hora_termino');
            $table->float('avaliacao');
            $table->foreignId('mentoria_id')->constrained('mentorias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessao_mentorias');
    }
};
