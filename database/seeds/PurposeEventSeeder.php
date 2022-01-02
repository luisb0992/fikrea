<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class PurposeEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purpose_events')->truncate();

        $data = [
            [
                'name'          => 'Empresarial',
                'description'   => 'Evento empresarial para pequeñas o grandes empresas.'
            ],
            [
                'name'          => 'Ayuda Humanitaria',
                'description'   => 'Evento solidario para alguna actividad de ayuda a otras personas.'
            ],
            [
                'name'          => 'Satisfacción',
                'description'   => 'Evento para conocer el grado de satisfacción de sus clientes y consumidores mediante el uso de cuestionarios.'
            ],
            [
                'name'          => 'Estudios de Mercado',
                'description'   => 'Evento para conocer mejor el comportamiento y las necesidades de tus clientes actuales y potenciales, realice un seguimiento de su competencia y mide la efectividad de los recursos.'
            ],
            [
                'name'          => 'Clima Laboral',
                'description'   => 'Evento para conocer y evaluar la percepción de sus empleados sobre la organización y así implantar medidas de mejora.'
            ],
            [
                'name'          => 'Familiares y Amigos',
                'description'   => 'Evento para fomentar la participación de todos en el que cada persona podrá votar por sus preferencias.'
            ],
            [
                'name'          => 'Test de Concepto',
                'description'   => 'Evento para conocer la intención de compra de las personas y saber si están realmente pensando en comprar el producto propuesto.'
            ],
            [
                'name'          => 'Animales',
                'description'   => 'Evento en apoyo a los animales.'
            ],
            [
                'name'          => 'Medio Ambiente',
                'description'   => 'Evento relacionado con el medio ambiente.'
            ],
            [
                'name'          => 'Salud',
                'description'   => 'Evento en cuidado de la salud.'
            ],
            [
                'name'          => 'Justicia',
                'description'   => 'Evento a favor de la justicia social.'
            ],
            [
                'name'          => 'Economia',
                'description'   => 'Evento en relación a la economía.'
            ],
            [
                'name'          => 'Empleo',
                'description'   => 'Evento en cuestión del trabajo o empleo.'
            ],
            [
                'name'          => 'Politica',
                'description'   => 'Evento en relación a estudios políticos.'
            ],
            [
                'name'          => 'Deporte',
                'description'   => 'Evento en relación con el deporte.'
            ],
            [
                'name'          => 'Tecnologia',
                'description'   => 'Evento en relación con la ciencia y tecnología.'
            ],
            [
                'name'          => 'Educación',
                'description'   => 'Evento en relación con el estudio y educación.'
            ],
            [
                'name'          => 'Cultura',
                'description'   => 'Evento en relación con la cultura y sociedad.'
            ],
            [
                'name'          => 'Violencia de Género',
                'description'   => 'Evento en relación con la violencia de género.'
            ],
            [
                'name'          => 'Derechos Humanos',
                'description'   => 'Evento en relación con derechos humanos.'
            ],
            [
                'name'          => 'Derechos LGTB',
                'description'   => 'Evento en relación con derechos LGTB.'
            ],
        ];

        DB::table('purpose_events')->insert($data);
    }
}
