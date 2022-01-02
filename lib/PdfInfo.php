<?php

namespace Fikrea;

/**
 * La clase PdfInfo
 *
 * Obtiene información de un archivo pdf
 *
 * Se usa la utilidada pdfinfo que ya viene normalmente en los sistemas GNU/Linux
 *
 * Se puede instalar con:
 *
 * sudo apt install poppler-utils
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */

class PdfInfo extends AppObject
{
    /**
     * La ruta del archivo PDF
     *
     * @var string
     */
    protected string $path;

    /**
     * El constructor
     *
     * @param String $path                     La ruta del archivo PDF
     */
    public function __construct(string $path)
    {
        parent::__construct(
            [
                'path' => $path,
            ]
        );
    }

    /**
     * Obtiene la información del archivo PDF
     *
     * @return string[]                         Una lista de valores informativos
     */
    protected function info(): array
    {
        // Ejecuta el comando pdfinfo
        $cmd = "/usr/bin/pdfinfo {$this->path}";
        info("Comando :: ");
        info($cmd);
        exec($cmd, $output);

        return $output;
    }

    /**
     * Obtiene la información de un archivo PDF
     *
     * @return int                              El número de páginas del archivo
     *                                          o cero si no se ha podido determinar
     */
    public function pages(): int
    {
        // Obtiene la información del archivo PDF
        $info = $this->info();

        foreach ($info as $line) {
            // Extract the number
            if (preg_match("/Pages:\s*(\d+)/i", $line, $matches) === 1) {
                $pages = intval($matches[1]);
                break;
            }
        }
        
        return $pages ?? 0;
    }

    /**
     * Obtiene el size de un archivo PDF
     *
     * @return int                              El tamaño del archivo
     *                                          o cero si no se ha podido determinar
     */
    public function size(): int
    {
        // Obtiene la información del archivo PDF
        $info = $this->info();

        foreach ($info as $line) {
            // Extract the number
            if (preg_match("/File size:\s*(\d+)/i", $line, $matches) === 1) {
                $size = intval(explode(' ', $matches[1])[0]);
                break;
            }
        }
        
        return $size ?? 0;
    }

    /**
     * Devuelve si la orientación del archivo pdf, es portrait o landscape
     * basado en la primer hoja
     *
     * @return bool                             true cuando es portrait
     *                                          false cuando es landscape
     */
    public function isPortrait(): bool
    {
        // Obtiene la información del archivo PDF
        $info = $this->info();
        $data = "";

        foreach ($info as $line) {
            // Extract the number
            if (strpos($line, "Page size:") !== -1) {
                $data = explode('Page size:', $line)[1];
                break;
            } else {

            }
        }
        info ("Data ::");
        info ($data);

        // ['3264', 'x', '2448', ...]
        if (intval($data[2]) >= intval($data[0])) {
            return true;
        }
        return false;
    }
}
