<?php

/**
 * Define el comando:
 *
 * php artisan translation:get
 *
 * que extrae los literales que deben ser traducidos y los coloca en un archivo:
 *
 * /resources/lang/txt/es.txt
 *
 * que se puede cargar en Google Traslator con objeto de obtener un archivo de traducciones,
 * por ejemplo, en inglés:
 *
 * /resources/lang/txt/en.txt
 *
 * @copyright 2021 Retail Servicios Externos SL
 * @author javieru <javi@gestoy.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Fikrea\I18n;

class GetTranslation extends Command
{
    /**
     * Signatura y nombre del comando
     *
     * @var string
     */
    protected $signature = 'translation:get';

    /**
     * La descripción del comando
     *
     * @var string
     */
    protected $description = 'Busca los literales a traducir';

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exjecuta el comando de consola
     *
     * @return void
     */
    public function handle()
    {
        // Busca los literales a traducir y los coloca en el archivo /resources/lang/txt/es.txt
        $literals    = I18n::getStringsToTraslate();
        $numLiterals = count($literals);

        $this->info(
            "Se ha generado el archivo /resources/lang/txt/es.txt con éxito.\n
             {$numLiterals} literales encontrados.
            "
        );
    }
}
