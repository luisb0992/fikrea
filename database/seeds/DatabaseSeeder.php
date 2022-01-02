<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Completa la base de datos
     *
     * @return void
     */
    public function run()
    {
        /**
         * Los usuarios
         */
        try {
            $this->call(UserSeeder::class);
        } catch (\Exception $e) {
            // No hacer nada
        }

        /**
         * Los Planes de subscripción
         */
        try {
            $this->call(PlanSeeder::class);
        } catch (\Exception $e) {
            // No hacer nada
        }

        /**
         * La biblioteca de sellos predeterminados
         */
        $this->call(StampSeeder::class);

        /**
         * Los formularios predeterminados para validacion de datos (plantillas)
         */
        $this->call(FormTemplateSeeder::class);

        /**
         * La lista de documentos requeridos predeterminados
         */
        $this->call(RequiredDocumentExampleSeeder::class);
        /**
         * La lista de Status en el Workspace predeterminados
         */
        $this->call(WorkspaceStatuSeeder::class);
        /**
         * Los motivos para la cancelacion de la solicitud de documento predeterminados
         */
        $this->call(ReasonCancelRequestSeeder::class);

        $this->call(MediaTypesSeeder::class);

        /**
         * Los propositos o finalidades de un evento
         */
        $this->call(PurposeEventSeeder::class);
    }
}
