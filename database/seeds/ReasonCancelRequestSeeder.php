<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonCancelRequestSeeder extends Seeder
{
    /**
     * Completa la base de datos
     *
     * @return void
     */
    public function run()
    {
        // Limpio la tabla reason_cancel_requests
        DB::statement("delete from reason_cancel_requests");

        DB::table('reason_cancel_requests')->insert([
            // motivo 1
            [
                'id'         => 1,
                'reason'     => 'No puedo realizar esta validación ahora mismo, lo intentare en otro momento.',
            ],
            // motivo 2
            [
                'id'          => 2,
                'reason'      => 'No he logrado llevar acabo esta validación por problemas tecnicos con la plataforma digital que proporciona el proceso.',
            ],
            // motivo 3
            [
                'id'          => 3,
                'reason'      => 'Carezco de esta información para llevar acabo lo que se me pide.',
            ],
            // motivo 4
            [
                'id'          => 4,
                'reason'      => 'No he entendido lo que se me pide y/o la información que esta validación tiene en su interior.',
            ],
            // motivo 5
            [
                'id'          => 5,
                'reason'      => 'No estoy de acuerdo con esta validación y la rechazo.',
            ],
            // motivo 6
            [
                'id'          => 6,
                'reason'      => 'No quiero realizar esta validación.',
            ],
        ]);
    }
}