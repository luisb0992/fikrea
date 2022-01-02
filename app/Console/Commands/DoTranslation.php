<?php

/**
 * Define el comando:
 *
 * php artisan translation:do --lang=en
 *
 * Previamente los literales han tenido que ser extraídos con:
 *
 * php artisan translation:get
 *
 * y llevados a traducir a Google Translator a un traductor profesional
 *
 * El comando combina, entonces, los literales a traducir:
 *
 * /resources/lang/txt/es.txt
 *
 * Con el archivo de traducciones correspondiente, obtenido con Google Translator:
 *
 * /resources/lang/txt/en.txt
 *
 * Ejecutar:
 *
 * php artisan translation:do --lang=en
 *
 * Generando el archivo de idioma:
 *
 * /resources/lang/en.json
 *
 * @copyright 2021 Retail Servicios Externos SL
 * @author javieru <javi@gestoy.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Fikrea\I18n;

class DoTranslation extends Command
{
    /**
     * Signatura y nombre del comando
     *
     * @var string
     */
    protected $signature = 'translation:do {--lang=}';

    /**
     * La descripción del comando
     *
     * @var string
     */
    protected $description = 'Genera el archivo de traducción en formato JSON';

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
        // Obtiene el código ISO-639-1 del idioma
        $lang = $this->option('lang');

        if (!$lang) {
            $this->error('Debe especificar el código del idioma con el parámetro --lang. Ejemplo:
                          php artisan translation:do --lang=en');
            return;
        }

        // Combina el archivo con los literales a traducir /resources/lang/txt/es.txt
        // con el archivo con lso literales traducidos /resources/lang/txt/en.txt
        // para formar el archivo de idioma /resources/lang/en.json
        I18n::createTranslationFile($lang);
     
        $this->info("Se ha generado el archivo /resources/lang/{$lang}.json con éxito.");
    }
}
