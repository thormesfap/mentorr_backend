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
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->text('biografia', 2000)->nullable();
            $table->integer('preco');
            $table->string('tags')->nullable();
            $table->text('curriculo', 4000)->nullable();
            $table->integer('minutos_por_chamada');
            $table->integer('quantidade_chamadas');
            $table->float('avaliacao')->nullable();
            $table->foreignId('user_id')->unique()->constrained('users');
            $table->foreignId('cargo_id')->nullable()->constrained('cargos');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
