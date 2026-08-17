<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Cria (ou atualiza) o usuário administrador inicial a partir das
     * variáveis ADMIN_NAME, ADMIN_EMAIL e ADMIN_PASSWORD do .env.
     *
     * Nenhuma senha real fica hardcoded no código: em ambiente local, se as
     * variáveis não forem definidas, uma senha aleatória é gerada e impressa
     * no console para uso único.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD');

        if (blank($password)) {
            $password = str()->password(16);
            $this->command?->warn("ADMIN_PASSWORD não definido no .env. Senha gerada para {$email}: {$password}");
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Administrador'),
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Administrador pronto: {$email}");
    }
}
