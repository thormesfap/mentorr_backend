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
            $table->string('expectativa')->nullable();
            $table->dateTime('data_hora_inicio')->nullable();
            $table->dateTime('data_hora_termino')->nullable();
            $table->boolean('ativa')->default(true);
            $table->float('avaliacao')->nullable();
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
