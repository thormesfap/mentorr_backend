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
        Schema::create('mentorias', function (Blueprint $table) {
            $table->id();
            $table->integer('valor');
            $table->integer('quantidade_sessoes');
            $table->string('expectativa');
            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_termino');
            $table->boolean('ativa');
            $table->float('avaliacao');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('mentor_id')->constrained('mentors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorias');
    }
};
