<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Crea el usuario de pruebas usuario@fikrea.com
     *
     * usuario: usuario@fikrea.com
     * contraseña: Demo.1234
     *
     * @return void
     */
    public function run()
    {
        // Limpio la tabla users: solo el usuario demo
        DB::statement("delete from users where id=1");

        // Crea un usuario que es además administrador
        DB::statement(
            "INSERT INTO 
                `users` 
                (
                    `id`, `type`, `role`, `guest_token`, 
                    `name`, `email`, `email_verified_at`, 
                    `password`, 
                    `remember_token`, `created_at`, `updated_at`, `locale`, `validation_code`, 
                    `lastname`, `address`, `dial_code`, `phone`, 
                    `city`, `province`, `country`, 
                    `company`, `position`, 
                    `image`, 
                    `config`, 
                    `active`
                ) 
             VALUES
                (
                    1, 1, 1, NULL,
                    'Lucas', 'usuario@fikrea.com', '2021-01-01 00:00:00', 
                    '$2y$10\$ok402FLs/i/ZQlllI3IELeMmGLgSzndPkyOdkDsWzafnRUtpSLFXO',
                    NULL, '2021-01-01 00:00:00', '2021-01-01 00:00:00', 'es', NULL, 
                    'Fernández', 'Calle los Herrán, Vitoria-Gasteiz, España', '+34', '600 100 200', 
                    'Vitoria-Gazteiz', 'Álava',
                    'IBM', 'Ventas',
                    NULL, 
                    NULL, 
                    NULL, 
                    1
                );
            "
        );
        
        // Limpio la tabla subscriptions: solo para usuario demo
        DB::statement("delete from subscriptions where user_id=1");

        // Crea la subscripción asociada al plan Premium
        DB::statement(
            "INSERT INTO 
            `subscriptions` 
                (
                    `id`, `user_id`, 
                    `plan_id`, `months`, 
                    `starts_at`, `ends_at`, `canceled_at`, 
                    `payment`, `payed_at`
                ) 
             VALUES
                (
                    1, 1, 
                    3, 12, 
                    '2021-01-01', '2021-12-31', NULL, 
                    0, NULL
                )
            "
        );
    }
}
