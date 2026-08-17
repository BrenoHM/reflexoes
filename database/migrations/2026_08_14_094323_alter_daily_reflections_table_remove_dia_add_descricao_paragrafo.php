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
        Schema::table('daily_reflections', function (Blueprint $table) {
            // A regra "uma reflexão por dia" deixou de existir: as reflexões
            // agora são sorteadas aleatoriamente, então o índice único
            // (dia, deleted_at) precisa sair antes da coluna.
            $table->dropUnique(['dia', 'deleted_at']);
            $table->dropColumn('dia');

            $table->text('descricao_paragrafo')->after('paragrafo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reflections', function (Blueprint $table) {
            $table->dropColumn('descricao_paragrafo');
            $table->date('dia')->nullable()->after('paragrafo');
        });

        Schema::table('daily_reflections', function (Blueprint $table) {
            $table->unique(['dia', 'deleted_at']);
        });
    }
};
