<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class AdminCreateCommand extends Command
{
    /**
     * Aceita nome/e-mail/senha via opção (útil para automação), mas o normal
     * é rodar sem opções e responder aos prompts interativos — assim a senha
     * nunca aparece no histórico do shell nem em logs.
     */
    protected $signature = 'admin:create
        {--name= : Nome do administrador (solicitado interativamente se omitido)}
        {--email= : E-mail do administrador (solicitado interativamente se omitido)}
        {--password= : Senha do administrador (solicitada interativamente se omitida; evite esta opção fora de scripts, pois fica no histórico do shell)}
        {--force : Se o e-mail já existir, atualiza a senha sem pedir confirmação}';

    protected $description = 'Cria o administrador inicial da aplicação (ou atualiza a senha de um já existente)';

    public function handle(): int
    {
        $name = $this->option('name') ?: text('Nome do administrador', required: true);

        $email = $this->option('email') ?: text('E-mail do administrador', required: true);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email']],
        );

        if ($validator->fails()) {
            $this->components->error($validator->errors()->first());

            return self::FAILURE;
        }

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser && ! $this->option('force')) {
            $confirmed = $this->option('password')
                ? false
                : confirm("Já existe um administrador com o e-mail {$email}. Atualizar a senha dele?", default: false);

            if (! $confirmed) {
                $this->components->warn('Nenhuma alteração feita. Use --force para atualizar sem confirmar.');

                return self::FAILURE;
            }
        }

        $password = $this->option('password') ?: password('Senha do administrador (mín. 8 caracteres)', required: true);

        $passwordValidator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', 'min:8']],
        );

        if ($passwordValidator->fails()) {
            $this->components->error($passwordValidator->errors()->first());

            return self::FAILURE;
        }

        if (! $this->option('password')) {
            $confirmation = password('Confirme a senha', required: true);

            if (! hash_equals($password, $confirmation)) {
                $this->components->error('As senhas não coincidem.');

                return self::FAILURE;
            }
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        $this->components->info(
            $user->wasRecentlyCreated
                ? "Administrador criado com sucesso: {$email}"
                : "Senha atualizada com sucesso para: {$email}"
        );

        return self::SUCCESS;
    }
}
