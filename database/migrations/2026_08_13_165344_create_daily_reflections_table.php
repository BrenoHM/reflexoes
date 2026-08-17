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
        Schema::create('daily_reflections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('paragrafo')->comment('Número do parágrafo no Diário de Santa Faustina');
            $table->longText('reflexao');
            $table->date('dia');
            $table->timestamps();
            $table->softDeletes();

            $table->index('paragrafo');

            // Impede duas reflexões com a mesma data e o mesmo estado de exclusão.
            // No MySQL, valores NULL em `deleted_at` não são considerados iguais entre
            // si por um índice único, então este índice sozinho NÃO garante que só
            // exista uma reflexão ATIVA por data (duas linhas com deleted_at NULL
            // passariam pelo índice). A regra "uma reflexão ativa por dia" é garantida
            // de fato na camada de aplicação, pela validação em StoreDailyReflectionRequest
            // e UpdateDailyReflectionRequest (consulta via DailyReflection::query(), que já
            // exclui registros com soft delete automaticamente).
            // O índice aqui evita duplicatas idênticas e acelera as buscas por data.
            $table->unique(['dia', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reflections');
    }
};
