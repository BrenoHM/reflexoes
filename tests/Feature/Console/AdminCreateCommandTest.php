<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCreateCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_a_new_admin_interactively(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nome do administrador', 'Admin Teste')
            ->expectsQuestion('E-mail do administrador', 'admin@example.com')
            ->expectsQuestion('Senha do administrador (mín. 8 caracteres)', 'senha-forte-123')
            ->expectsQuestion('Confirme a senha', 'senha-forte-123')
            ->assertExitCode(0);

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('Admin Teste', $user->name);
        $this->assertTrue(Hash::check('senha-forte-123', $user->password));
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_fails_when_password_confirmation_does_not_match(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nome do administrador', 'Admin Teste')
            ->expectsQuestion('E-mail do administrador', 'admin@example.com')
            ->expectsQuestion('Senha do administrador (mín. 8 caracteres)', 'senha-forte-123')
            ->expectsQuestion('Confirme a senha', 'outra-senha-456')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    }

    public function test_asks_for_confirmation_before_overwriting_an_existing_admin(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $this->artisan('admin:create')
            ->expectsQuestion('Nome do administrador', 'Admin Teste')
            ->expectsQuestion('E-mail do administrador', 'admin@example.com')
            ->expectsConfirmation('Já existe um administrador com o e-mail admin@example.com. Atualizar a senha dele?', 'no')
            ->assertExitCode(1);

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('senha-antiga', $user->password));
    }

    public function test_updates_an_existing_admin_password_when_confirmed(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $this->artisan('admin:create')
            ->expectsQuestion('Nome do administrador', 'Admin Teste')
            ->expectsQuestion('E-mail do administrador', 'admin@example.com')
            ->expectsConfirmation('Já existe um administrador com o e-mail admin@example.com. Atualizar a senha dele?', 'yes')
            ->expectsQuestion('Senha do administrador (mín. 8 caracteres)', 'senha-nova-123')
            ->expectsQuestion('Confirme a senha', 'senha-nova-123')
            ->assertExitCode(0);

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('senha-nova-123', $user->password));
    }

    public function test_creates_an_admin_non_interactively_via_options(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'Admin Via Opções',
            '--email' => 'admin-opcoes@example.com',
            '--password' => 'senha-forte-123',
        ])->assertExitCode(0);

        $user = User::query()->where('email', 'admin-opcoes@example.com')->firstOrFail();
        $this->assertSame('Admin Via Opções', $user->name);
        $this->assertTrue(Hash::check('senha-forte-123', $user->password));
    }

    public function test_does_not_overwrite_existing_admin_via_options_without_force(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $this->artisan('admin:create', [
            '--name' => 'Admin Teste',
            '--email' => 'admin@example.com',
            '--password' => 'senha-nova-123',
        ])->assertExitCode(1);

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('senha-antiga', $user->password));
    }

    public function test_overwrites_existing_admin_via_options_with_force(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $this->artisan('admin:create', [
            '--name' => 'Admin Teste',
            '--email' => 'admin@example.com',
            '--password' => 'senha-nova-123',
            '--force' => true,
        ])->assertExitCode(0);

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('senha-nova-123', $user->password));
    }

    public function test_the_password_is_never_written_to_command_output(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'Admin Teste',
            '--email' => 'admin@example.com',
            '--password' => 'senha-super-secreta',
        ])
            ->assertExitCode(0)
            ->doesntExpectOutputToContain('senha-super-secreta');
    }
}
