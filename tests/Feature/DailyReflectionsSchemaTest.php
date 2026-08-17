<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DailyReflectionsSchemaTest extends TestCase
{
    /**
     * Não usa RefreshDatabase de propósito: este teste chama migrate/rollback
     * diretamente, e a estratégia de transação do RefreshDatabase não
     * combina bem com DDL (ALTER TABLE) executado no meio do teste.
     */
    public function test_migration_removes_dia_adds_descricao_paragrafo_and_can_be_rolled_back(): void
    {
        Artisan::call('migrate:fresh');

        $this->assertFalse(Schema::hasColumn('daily_reflections', 'dia'));
        $this->assertTrue(Schema::hasColumn('daily_reflections', 'descricao_paragrafo'));

        Artisan::call('migrate:rollback', ['--step' => 1]);

        $this->assertTrue(Schema::hasColumn('daily_reflections', 'dia'));
        $this->assertFalse(Schema::hasColumn('daily_reflections', 'descricao_paragrafo'));

        Artisan::call('migrate');

        $this->assertFalse(Schema::hasColumn('daily_reflections', 'dia'));
        $this->assertTrue(Schema::hasColumn('daily_reflections', 'descricao_paragrafo'));
    }
}
