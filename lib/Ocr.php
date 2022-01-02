<?php

namespace Fikrea;

use Illuminate\Support\Facades\Lang;

/**
 * Tesseract OCR
 *
 * @link https://packagist.org/packages/thiagoalessio/tesseract_ocr
 */
use thiagoalessio\TesseractOCR\TesseractOCR;

/**
 * Excpeciones requeridas
 */
use Fikrea\Exception\OcrException;

/**
 * La clase Ocr
 *
 *
 * sudo apt install tesseract-ocr
 *
 * Los archivos de idioma para realizar un reconocimiento óptimo se almacenan en (para la versión 4.0):
 *
 * /usr/share/tesseract-ocr/4.0/tessdata
 *
 * Se pueden obtener los archivos de idioma que se necesiten (para la versión 4.0), desde su repositorio en github:
 *
 * https://github.com/tesseract-ocr/tessdata
 *
 * Por ejemplo, para el archivo en español:
 *
 * wget https://github.com/tesseract-ocr/tessdata/raw/master/spa.traineddata
 *
 * Alternativamente:
 *
 * sudo apt install tesseract-ocr-spa
 *
 * @example
 *
 * use Fikrea\Ocr;
 * use Fikrea\Exception\OcrException;
 *
 * $ocr = new Ocr($file);
 *
 * try {
 *      $text = $ocr->run();
 * } catch (OcrException $e) {
 *      // Se ha producido un error al procesar el documento
 * }
 *
 * Obtiene el texto del archivo de de imagen cuya ruta absoluta es $file
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */
class Ocr extends AppObject
{
    /**
     * El objeto Tesseract
     *
     * @var TesseractOCR
     */
    protected TesseractOCR $ocr;

    /**
     * La ruta del archivo a procesar
     *
     * @var string
     */
    protected string $path;

    /**
     * El código ISO-639-1 del idioma del archivo a procesar
     *
     * @var string
     */
    protected string $lang;

    /**
     * El constructor
     *
     * @param string $path                      La ruta del archivo
     * @param string $lang                      El idioma
     */
    public function __construct(string $path, string $lang = null)
    {
        // Obtiene el idioma por defecto a utilizar para el OCR si no se ha proporcionado uno
        $lang??= config('ocr.lang.default');

        parent::__construct(
            [
                'path'  => $path,               // La ruta del archivo a procesar
                'lang'  => $lang,               // El idioma utilizado en en procesamiento
                'ocr'   => new TesseractOCR,    // El objeto Tesseract
            ]
        );
    }

    /**
     * Efectúa el procesamiento OCR del archivo
     *
     * @return string                           El contenido de texto del archivo
     * @throws OcrException                     Error al procesar el documeto
     */
    public function run(): string
    {
        // Fija el idioma ha utilizar para el OCR
        $langPrefix = $this->langPrefix($this->lang);
        $this->ocr->lang($langPrefix);

        if (mime_content_type($this->path) == 'application/pdf') {
            // Si es un archivo PDF
            $text = $this->loadPdf();
        } else {
            // Si es un archivo de imagen
            $text = $this->loadImage();
        }

        return $text;
    }

    /**
     * Obtiene el código de idioma para el reconociento eficiente de los textos
     *
     * Los archivos de idioma, que contienen los datos de entrenamiento, se localizan en:
     *
     * /usr/share/tesseract-ocr/4.0/tessdata
     *
     * Por ejemplo, el archivo en español es:
     *
     * spa.traineddata
     *
     * Estos archivos vienen prefijados por un códifo de idioma que no es el ISO-639-1
     * Este método toma un código de idioma ISO-639-1 y obtiene el prefijo que debe ser usado
     *
     * @example
     *
     * $this->langPrefix('es')
     *
     * devuelve:
     *
     * 'spa'
     *
     * @param string $iso                       El código ISO-639-1 del idioma
     *
     * @return string                           El prefijo de idioma que se utiliza para los datos
     *                                          de entrenamiento del modelo de reconocimiento OCR
     */
    protected function langPrefix(string $iso): string
    {
        // Obtiene los idiomas para el CR de la configuración
        $langs = config('ocr.lang.list');

        // Obtiene el prefijo para el código de idioma ISO-639-1 proporcionado
        // Si no existe, el idioma especificado, se usará el idioma por defecto
        return $langs[$iso] ?? config('ocr.lang.default');
    }

    /**
     * Carga un archivo PDF para ser procesado mediante OCR
     *
     * @return string                           El texto del documento PDF
     * @throws OcrException                     El archivo no ha podido ser procesado
     */
    protected function loadPdf(): string
    {
        // Obtiene el número de páginas del documento
        $pdfInfo = new PdfInfo($this->path);

        $pages = $pdfInfo->pages();

        // El número máximo de páginas que se pueden procesar
        $maxPages = config('ocr.max.pages');

        // Si el documento posee más páginas que el límite permitido en la configuración
        if ($pages > $maxPages) {
            throw new OcrException(
                Lang::get(
                    "El archivo posee demasiadas páginas (:pages de un máximo de :total)",
                    [
                        'pages'    => $pages,
                        'total'    => $maxPages,
                    ]
                )
            );
        }

        $imagick = new \Imagick;
        
        for ($i = 0, $text = ''; $i < $pages; $i++) {
            // Carga cada página
            try {
                $imagick->readImage("{$this->path}[$i]");
            } catch (\Exception $e) {
                throw new OcrException(
                    Lang::get('El archivo no ha podido ser procesado')
                );
            }

            // Obtiene la imagen e formato jpg
            $imagick->setImageFormat('jpg');

            // El contenido binario y tamaño de la imagen
            $content = $imagick->getImageBlob();
            $size    = $imagick->getImageLength();

            // Prepara el contenido de la imagen para el reconocimiento OCR
            $this->ocr->imageData($content, $size);

            $text.= $this->ocr->run();
        }

        return $text;
    }

    /**
     * Carga un archivo de imagen para ser procesado mediante OCR
     *
     * @return string                           El texto de la imagen
     * @throws OcrException                     El archivo no ha podido ser procesado
     */
    protected function loadImage(): string
    {
        $this->ocr->image($this->path);

        try {
            $text = $this->ocr->run();
        } catch (\Exception $e) {
            throw new OcrException($e->getMessage());
        }

        return $text;
    }
}
