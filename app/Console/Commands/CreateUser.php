<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        {
            $users = [
                ['sap' => 12, 'name' => 'Contagem 1', 'email' => 'contagem1@dapco.com.br'],
                ['sap' => 13, 'name' => 'Contagem 2', 'email' => 'contagem2@dapco.com.br'],
                ['sap' => 14, 'name' => 'Contagem 3', 'email' => 'contagem3@dapco.com.br'],
                ['sap' => 15, 'name' => 'Contagem 4', 'email' => 'contagem4@dapco.com.br'],
                ['sap' => 16, 'name' => 'Contagem 6', 'email' => 'contagem6@dapco.com.br'],
                ['sap' => 24, 'name' => 'Embalagem', 'email' => 'embalagem@dapco.com.br'],
                ['sap' => 25, 'name' => 'Estoque', 'email' => 'estoque@dapco.com.br'],
                ['sap' => 33, 'name' => 'Regi', 'email' => 'rego@dapco.com.br'],
            ];

            $createdUsers = [];

            foreach ($users as $userData) {
                // Gera uma senha numérica aleatória de 10 dígitos
                $password = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

                // Cria o usuário no banco de dados
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt($password),
                    'sap' => $userData['sap'],
                    'email_verified_at' => now(),
                ]);

                // Guarda o email e a senha gerada
                $createdUsers[] = [
                    'email' => $userData['email'],
                    'password' => $password,
                ];
            }

            // Exibe a lista de usuários criados com seus emails e senhas
            $this->info("Usuários criados com sucesso:");
            foreach ($createdUsers as $createdUser) {
                $this->info("Email: {$createdUser['email']}, Senha: {$createdUser['password']}");
            }
        }
    }
}
