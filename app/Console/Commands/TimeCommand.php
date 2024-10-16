<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Users; // Asegúrate de que el modelo User esté importado

class TimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:time-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando que lista los usuarios y muestra la hora actual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = Users::find(2); // Buscar usuario con ID 1

        if (!$user) {
            Log::info('User not found.');
            return;
        }

        // Registrar información del usuario
        Log::info('User ID: ' . $user->id);
        Log::info('User Email: ' . $user->email);
        Log::info('User Name: ' . $user->nombre);
    }
}