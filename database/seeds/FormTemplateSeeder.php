<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // -------------------------------------------------
            // formulario predeterminado para usuario particular
            // -------------------------------------------------
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Nombre completo',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'DNI - NIE',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Direccion',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Email',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Telefono',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Nº Cuenta bancaria / IBAN',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Nº Tarjeta bancaria + Fecha de caducidad',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Condicion de pago - cobro',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Nº Seguro social',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Cargo - funciones a realizar',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Duracion contractual',
            ],
            [
                'type'              => 1,
                'template_number'   => 1,
                'field_name'        => 'Fecha de inicio / entrada en vigencia',
            ],


            // ------------------------------------------------------------
            // Formulario predeterminado para usuario del tipo empresarial
            // ------------------------------------------------------------
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Razon social',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Nombre comercial',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'CIF / NIF',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Persona contacto / Nombre completo',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Cargo',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Telefono',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Email',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Horario de atencion',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'CNAE',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Fecha de registro de la empresa en el registro mercantil',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Nº Protocolo otorgado por el registro de la sociedad - apoderadamiento',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Notario que inscribio el poder - escritura',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Forma de pago - cobro',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Nº Cuenta bancaria / IBAN',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Nº Tarjeta bancaria + Fecha de caducidad',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Codigo BIC',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Codigo SWIFT',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Duracion contractual',
            ],
            [
                'type'              => 2,
                'template_number'   => 1,
                'field_name'        => 'Fecha inicio/entrada en vigencia',
            ],
        ];

        DB::table('form_templates')->insert($data);
    }
}
