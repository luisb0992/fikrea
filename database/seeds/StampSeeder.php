<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class StampSeeder extends Seeder
{
    /**
     * Inserta lo sellos de la biblioteca predeterminada de sellos
     * que se proporcionan como ejemplo
     *
     * @return void
     */
    public function run()
    {
        // Elimina los sellos anteriores si los hubiera
        DB::statement(
            "DELETE FROM stamps WHERE user_id IS NULL"
        );

        // Los idiomas disponibles
        $languages = config('language.allowed');

        // Crea una entrada de un sello para cada idioma disponible
        foreach ($languages as $language) {
            DB::table('stamps')->insert(
                [
                    // Sello "Aceptado"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Aceptado', [], $language),
                        'path' => "{$language}/accepted-stamp.png",
                        'type' => 'image/png',
                    ],
                    // Sello "Cancelado"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Cancelado', [], $language),
                        'path' => "{$language}/canceled-stamp.png",
                        'type' => 'image/png',
                    ],
                    // Sello "Pagado"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Pagado', [], $language),
                        'path' => "{$language}/payed-stamp.png",
                        'type' => 'image/png',
                    ],
                    // Sello "Pendiente"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Pendiente', [], $language),
                        'path' => "{$language}/pending-stamp.png",
                        'type' => 'image/png',
                    ],
                    // Sello "Confirmado"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Confirmado', [], $language),
                        'path' => "{$language}/posted-stamp.png",
                        'type' => 'image/png',
                    ],
                    // Sello "Rechazado"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Rechazado', [], $language),
                        'path' => "{$language}/rejected-stamp.png",
                        'type' => 'image/png',
                    ],
                    // Sello "Urgente"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Urgente', [], $language),
                        'path' => "{$language}/urgent-stamp.png",
                        'type' => 'image/png',
                    ],
                    // Sello "Verificado"
                    [
                        'lang' => $language,
                        'name' => Lang::get('Verified', [], $language),
                        'path' => "{$language}/verified-stamp.png",
                        'type' => 'image/png',
                    ],
                ]
            );
        }
    }
}
