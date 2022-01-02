<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Completa la base de datos
     *
     * @return void
     */
    public function run()
    {
        // Limpio la tabla plans
        DB::statement("delete from plans");

        DB::table('plans')->insert([
            // Plan Gratuito
            [
                'id'            => 0,
                'name'          => 'Free',
                'disk_space'    => 100,         // 100 MB
                'signers'       => 1,           // Número de firmantes
                'monthly_price' => 0,           // Precio mensual
                'yearly_price'  => 0,           // Precio anual
                'change_price'  => 0,           // El precio unitario del cambio al plan superior
                'tax'           => 21,          // El IVA Aplicable
                'trial_period'  => 30,          // Periodo de prueba de 30 días
            ],
            // Plan Premium
            [
                'id'            => 1,
                'name'          => 'Premium',
                'disk_space'    => 2*1024,      // 2 GB
                'signers'       => 4,           // Número de firmantes
                'monthly_price' => 4.95,        // Precio mensual
                'yearly_price'  => 49.95,       // Precio anual
                'change_price'  => 10.00,       // El precio mensual por el cambio del
                                                // plan "premium" al plan "enterprise"
                'tax'           => 21,          // El IVA Aplicable
                'trial_period'  => null,
            ],
            // Plan Enterprise
            [
                'id'            => 2,
                'name'          => 'Enterprise',
                'disk_space'    => 10*1024,     // 10 GB
                'signers'       => 200,         // Número de firmantes
                'monthly_price' => 14.95,       // Precio mensual
                'yearly_price'  => 149.95,      // Precio anual
                'change_price'  => 0,
                'tax'           => 21,          // El IVA Aplicable
                'trial_period'  => null,
            ],
            // Plan FIKREA
            [
                'id'            => 3,
                'name'          => 'FIKREA',
                'disk_space'    => 50*1024,     // 50 GB
                'signers'       => 200,         // Número de firmantes
                'monthly_price' => 14.95,       // Precio mensual
                'yearly_price'  => 149.95,      // Precio anual
                'change_price'  => 0,
                'tax'           => 21,          // El IVA Aplicable
                'trial_period'  => null,
            ],
        ]);
    }
}
