<?php

namespace Fikrea;

/**
 * La clase I18n
 *
 * Extrae los literales de texto para traducir en el proyecto
 *
 * @example
 *
 * Esta clase no se utiliza directamente, sino que se realiza a través de artisan.
 * En una terminal escribir:
 *
 * php artisan translation:get
 *
 * que extrae los literales que deben ser traducidos y los coloca en un archivo:
 *
 * /resources/lang/txt/es.txt
 *
 * Este archivo se puede suministrar a un equipo de traducción porfesional o se puede cargar en Google Traslator
 * con objeto de obtener un archivo de traducciones.
 *
 * Por ejemplo, si queremos traducir el archivo al ingles usando Google Traslator,
 * cargar el arhivo es.txt en la dirección:
 *
 * https://translate.google.es/?sl=es&tl=en&op=translate
 *
 * y guardar el resultado generado como:
 *
 * /resources/lang/txt/en.txt
 *
 * Ejecutar ahora
 *
 * php artisan translation:do --lang=en
 *
 * que combina los archivos es.txt y en.txt en un archivo:
 *
 * /resources/lang/en.json
 *
 * que contiene las traducciones a inglés.
 *
 * @copyright 2021 Retail Servicios Externos SL
 * @author javieru <javi@gestoy.com>
 */

use Illuminate\Support\Facades\File;

use Fikrea\Exception\FileException;

class I18n
{
    /**
     * Extrae los literales de texto a traducir
     *
     * Se genera un archivo con los literales en /resources/lang/txt/source.txt
     * que se puede cargar en Google Traslator y obtener el archivo con las traducciones
     * que se guardará en /resources/txt/target.txt
     *
     * @return string[]                         Una lista con los literales a traducir
     *
     * @example
     *
     * use Fikrea\ModelAndView;
     *
     * ModelAndView::getStringsToTraslate();
     *
     * Esto genera un archivo con los literales a traducir en /resources/lang/txt/es.txt
     * Para realizar esta acción podemos ejecutar el comando de artisan:
     *
     * php artisan translate:get
     *
     * Cargar el archivo /resources/lang/txt/es.txt en Google Traslator
     * Guardar el resultado en /resources/lang/txt/en.txt
     *
     * Luego llamar al método:
     *
     * ModelAndView::createTranslationFile('en);
     *
     * que combina es.txt y en.txt para crear el archivo de idioma en.json
     * El archivo en.json se encuentra en /resources/lang/en.json
     *
     */
    final public static function getStringsToTraslate(): array
    {
        // Obtiene la carpeta de las vistas de Blade
        $viewsFolder = base_path('/resources/views/');

        // La lista de literales a traducir
        $literals   = [];

        /**
         * Busca los literales a traducir en los archivos de extensión blade.php
         * del tipo @lang('{literal}', ...)
         *
         * Son etiquetas válidas:
         *
         * @lang('El usuario es incorrecto')
         * @lang('Su pedido número :order está preparado', ['order' => $order->id])
         *
         * No es correcta:
         *
         * @lang("El usuario es incorrecto")
         *
         * por utilizar comillas dobles como delimitador
         */
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsFolder)),
            '/^.+\.blade\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        $files    = iterator_to_array($files);
        $index    = 0;

        foreach ($files as $filepath => $file) {
            ++$index;

            // Obtiene el contenido del archivo sin saltos de línea y sin espacios múltiples
            $text = preg_replace(['/\r|\n/', '/\s+|\t+/'], ['', ' '], file_get_contents($filepath));
           
            // Encuentra todas la expresiones @lang('')
            preg_match_all('/\@lang\(\s?(\')(.*?)(\')/', $text, $translations, PREG_PATTERN_ORDER);
        
            foreach ($translations[2] as $literal) {
                // Elimina los espacios en blanco multiples del literal
                $literal = trim(preg_replace('/\s+|\t+/', ' ', $literal));
                // Si el literal ya existe, lo omite
                if (!in_array($literal, $literals)) {
                    $literals[] = $literal;
                }
            }
        }

        // Obtiene la carpeta app
        $appFolder =  app_path();

        /**
         * Busca los literales a traducir en los archivos de extensión .php
         * del tipo Lang::get('{literal}', ...)
         *
         * Lang::get('El usuario es incorrecto')
         * Lang::get('Su pedido número :order está preparado', ['order' => $order->id])
         *
         * No es correcta:
         *
         * Lang::get("El usuario es incorrecto")
         *
         * por utilizar comillas dobles como delimitador
         */
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($appFolder)),
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );
        
        $files    = iterator_to_array($files);
        $index    = 0;
        
        foreach ($files as $filepath => $file) {
            ++$index;
            
            // Obtiene el contenido del archivo sin saltos de línea y sin espacios múltiples
            $text = preg_replace(['/\r|\n/', '/\s+|\t+/'], ['', ' '], file_get_contents($filepath));
   
            // Encuentra todas la expresiones Lang::get('{literal}',...) en el texto
            preg_match_all('/Lang\:\:get\(\s?(\')(.*?)(\')/', $text, $translations, PREG_PATTERN_ORDER);
        
            foreach ($translations[2] as $index => $literal) {
                // Elimina los espacios en blanco multiples del literal
                $literal = trim(preg_replace('/\s+|\t+/', ' ', $literal));

                // Si no hay argumento o este es una variable se omite
                if (empty($literal) || substr($literal, 0, 1) == '$' || substr($literal, 0, 1) == ',') {
                    continue;
                }

                // Si el literal ya existe, lo omite
                if (!in_array($literal, $literals)) {
                    $literals[] = $literal;
                }
            }
        }

        // Los literales que comienza con dos puntos como :user o :app son argumentos a sustituir,
        // pero Google Traslator efectúa su traducción. Para evitarlo se sustituyen los dos puntos
        // por un guión bajo _user o _app
        foreach ($literals as &$literal) {
            $literal = preg_replace('/:(?![ ])/', '_', $literal);
        }

        // Si no existía previamente, se crea el directorio donde se guardan los archivos
        // con los literales de traducción
        File::makeDirectory(base_path('/resources/lang/txt'), 0775, false, true);
       
        // Guarda los literales en un archivo de texto plano es.txt en la carpeta /resources/lang/txt
        $sourceFile = base_path('/resources/lang/txt/es.txt');

        file_put_contents($sourceFile, implode(PHP_EOL, $literals));
        
        return $literals;
    }

    /**
     * Obtiene el archivo de idioma combinando los archivos source.txt y target.txt
     * en un archivo único en formato JSON {lang}.js que se almacena en /resources/lang
     *
     * @param string $lang                      El código ISO-639-1 del idioma
     *
     * @return array                            Una lista con las traducciones
     *
     * @example
     *
     * use Fikrea\ModelAndView;
     *
     * $dictionary = ModelAndView::createTranslationFile('en');
     *
     */
    final public static function createTranslationFile(string $lang): array
    {
        // El archivo fuente con los textos en el idioma por defecto (es)
        $sourceFile = base_path('/resources/lang/txt/es.txt');

        // El archivo objetivo con los textos traducidos en el idioma especificado
        $targetFile = base_path("/resources/lang/txt/{$lang}.txt");

        if (!file_exists($targetFile)) {
            throw new FileException("No se ha encontrado el archivo {$lang}.txt en /resources/lang/txt");
        }

        // El archivo de idioma final que combina los dos archivos anteriores en un JSON
        $translationFile = base_path("/resources/lang/{$lang}.json");

        // Carga los archivos y los descompone en líneas
        $source = preg_split('/$\R?^/m', file_get_contents($sourceFile));
        $target = preg_split('/$\R?^/m', file_get_contents($targetFile));

        $dictionary = [];

        // Se analiza el contenido línea a línea del archivo fuente
        foreach ($source as $index => $line) {
            if (isset($target[$index])) {
                // Las palabras precedidas por unn guión bajo como _user o _app son argumentos
                // Debe reemplazarse el guión bajo _ por dos puntos :
                // y quedar como :user o :app
                $original   = preg_replace('/_/', ':', trim($line));
                $translated = addslashes(preg_replace('/_/', ':', trim($target[$index])));

                $dictionary[$original] = $translated;
            }
        }
        
        // Crea el archivo de idioma JSON
        file_put_contents($translationFile, json_encode($dictionary, JSON_PRETTY_PRINT));

        // Devuelve el diccionario
        return $dictionary;
    }
}
