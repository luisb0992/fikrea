<?php

/**
 * Define el comando:
 *
 * php artisan check:admin-users
 *
 * Chequea por los usuarios que tiene rol de administración
 * por el momento solo mikel@retailexternal.com
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos SL
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Enums\Role;

class CheckForAdminUsers extends Command
{
    /**
     * Signatura y nombre del comando
     *
     * @var string
     */
    protected $signature = 'check:admin-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica el estado de los usuarios que deben tener rol de administración';

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ejecuta el comando de consola
     *
     * @return void
     */
    public function handle()
    {
        collect(config('user.admins'))->each(function ($user) {
            $admin = User::where('email', $user)->first();
            if ($admin && $admin->role == 0) {
                $admin->role = Role::ADMIN;
                $admin->save();
            }
        });
    }
}
