<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkspaceStatuSeeder extends Seeder
{
    /**
     * Completa la base de datos
     *
     * @return void
     */
    public function run()
    {
        // Limpio la tabla workspace_status
        DB::statement("delete from workspace_status");

        DB::table('workspace_status')->insert([
            // Statu pendiente
            [
                'id'            => 1,
                'status'          => 'pending',
            ],
            // Statu Cancelado
            [
                'id'            => 2,
                'status'          => 'canceled',
            ],
            // Statu Realizado
            [
                'id'            => 3,
                'status'          => 'done',
            ],
        ]);
    }
}
