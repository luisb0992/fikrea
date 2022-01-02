<?php

/**
 * Trait DocumentConversion
 *
 * Gestiona la conversión de documentos a un formato común (PDF)
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Signature\Traits;

use App\Models\MediaType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Fikrea\AppStorage;
use Fikrea\PdfInfo;

/**
 * Excepciones requeridas
 */
use Fikrea\Exception\DocumentNotValidException;
use Fikrea\Exception\DocumentTooBigException;

trait DocumentConversion
{
    /**
     * Realiza la conversión del documento cargado
     *
     * Si el archivo es una imagen válida o archivo Word/Excel se convierte en pdf
     * Si el archivo ya es un archivo pdf, simplemente, se copia el archivo original
     *
     * @param array $document                   Los datos del documento
     * @param bool  $removeTemps                Si se eliminan los archivos temporales en el proceso
     *                                          de conversión
     *
     * @return void
     * @throws DocumentNotValidException        El documento no es válido
     *
     * @see https://www.php.net/manual/es/book.imagick.php
     * @see http://www.clie.cl/noticias_ficha.php?pid=29#:~:text=El%20folio%2C%20palabra%20que%20ha,en%20imprenta%20es%20de%20210x297mm.
     */
    protected function convert(array &$document, bool $removeTemps = true): void
    {
        // Si el tipo de archivo no coincide con alguno de los indicados
        // no se puede convertir
        if (!MediaType::where('media_type', $document['type'])->where('signable', 1)->exists()) {
            return;
        }

        if ($document['type'] == 'application/pdf') {
            // Si el archivo es un PDF nativo no hay conversión a realizar
            // y se copia la ruta donde se ha almacenado el archivo original
            $document['converted_path'] = $document['original_path'];

            // Obtiene el número de páginas del documento PDF
            $filePath = Storage::disk(env('APP_STORAGE'))->path($document['original_path']);

            $pdfInfo = new PdfInfo($filePath);
            $document['pages'] = $pdfInfo->pages();
        } else {
            //
            // El documento debe ser convertido a PDF
            //

            //
            // Crea, si no estuviesen ya creadas, se crean las carpeta que albergan:
            //
            //  1. Los archivos originales
            //  2. Los archivo convertidos a PDF para su procesamiento
            //  3. Los archivos firmados
            //  4. Las imágenes resultantes del procesamiento
            //
            Storage::disk('public')->makeDirectory(config('documents.folder.original'));
            Storage::disk('public')->makeDirectory(config('documents.folder.converted'));
            Storage::disk('public')->makeDirectory(config('documents.folder.signed'));
            Storage::disk('public')->makeDirectory(config('documents.folder.images'));

            // Si el almacenamiento es S3 se copia el archivo a la carpeta pública local
            // para su procesamiento posterior si no existía previamente
            if (AppStorage::isS3() && !Storage::disk('public')->exists($document['original_path'])) {
                Storage::disk('public')->put(
                    $document['original_path'],
                    Storage::disk('s3')->get($document['original_path'])
                );
            }

            // Obtiene la ruta de la imagen original a convertir
            $filePath = Storage::disk('public')->path($document['original_path']);
            // La extensión del archivo
            $fileExt  = (new \SplFileInfo($filePath))->getExtension();

            // Obtiene la ruta del archivo procesado en formato PDF que se va a generar
            // con la extensión PDF y la carpeta correcta para su almacenamiento
            $document['converted_path'] =
                preg_replace(
                    [
                        '"\.(' . $fileExt . ')$"',                       // La extensión del archivo
                        // La carpeta para los archivos originales
                        '/' . str_replace('/', '\/', config('documents.folder.original')) . '\//',
                    ],
                    [
                        '.pdf',                                      // La extensión final del archivo resultante
                        config('documents.folder.converted') . '/',  // La carpeta para los archivo convertidos
                    ],
                    $document['original_path']                       // El archivo original
                );

            // Obtiene la ruta absoluta del archivo que se va a generar en la conversión
            // y que se guardará en la carpeta pública local
            $documentConvertedPath = Storage::disk('public')->path($document['converted_path']);

            //---------------------------------------------------------------------------------------------------------
            // Si es una imagen
            // convertiremos la imagen a PDF con Imagick
            //
            // @see https://www.php.net/manual/es/book.imagick.php
            //---------------------------------------------------------------------------------------------------------
            if (strpos($document['type'], 'image') !== false) {
                // Carga Imagick para efectuar la conversión
                try {
                    $imagick = new \Imagick($filePath);
                } catch (\ImagickException $e) {
                    // El documento no es una imagen válida
                    throw new DocumentNotValidException($document['name']);
                }

                $data = $imagick->identifyimage();

                $resolution = $imagick->getImageResolution();
                
                // Llevamos la imagen a resolucion 72 dpi
                $imagick->setImageResolution (72 ,72);

                // Dimensiones de la imagen
                $height = $imagick->getImageHeight();
                $width  = $imagick->getImageWidth();

                $scale = 1;

                $newH = $height;
                $newW = $width;

                // Si las dimensiones son mayor que 1024 reducirlas
                if ($height > 1024 or $width > 1024) {
                    // buscar escala para que quede en 1024
                    $scale = 1;     // TODO

                    $newH =  (int) $imagick->getImageHeight() / $scale;
                    $newW = (int) $imagick->getImageWidth() / $scale;
                }

                $imagick->scaleImage($newW, $newH);

                // @see https://www.php.net/manual/ro/imagick.constants.php#imagick.constants.gravity
                $imagick->setGravity(\Imagick::GRAVITY_NORTH);

                // A4                        595 x  842 px     x 72 ppp (puntos por pulgada)
                // A4           210 x 297 mm 2480 x 3508 px    x 300 ppp

                // Convertimos el documento en formato A4 72 dpi o ppp
                $imagick->setImagePage(595, 842, 0, 0);

                $imagick->setImageFormat('pdf');
                $imagick->writeImage($documentConvertedPath);

                //dd($imagick->getImageHeight() . 'x' . $imagick->getImageWidth());
                
                //---------------------------------------------------------------------------------------------------------
                // Si es un archivo de texto plano o HTML
                // se convierte a PDF con DomPDF
                //
                // @link https://github.com/dompdf/dompdf
                //---------------------------------------------------------------------------------------------------------
            } elseif ($document['type'] == 'text/plain'
                                ||
                    $document['type'] == 'text/html'
            ) {
                // Carga el contenido del archivo de texto
                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml(file_get_contents($filePath));

                // Obtiene el PDF
                $dompdf->render();

                // Guarda el archivo PDF generado
                $documentConvertedPath = Storage::disk('public')->put($document['converted_path'], $dompdf->output());
                //---------------------------------------------------------------------------------------------------------
                // Si es un archivo en formato Microsoft Word
                // se convierte a PDF con PHP Word
                //
                // @link https://github.com/PHPOffice/PHPWord
                //---------------------------------------------------------------------------------------------------------
            } elseif ($document['type'] == 'application/msword'
                                        ||
                    $document['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ) {
                // Configuramos PhpWord
                \PhpOffice\PhpWord\Settings::setPdfRenderer(
                    \PhpOffice\PhpWord\Settings::PDF_RENDERER_MPDF,
                    base_path(config('documents.mpdf'))
                );
                // Efectuamos la conversión a PDF del documento
                if ($document['type'] == 'application/msword') {
                    // Conversión de archivos antiguos con formato doc
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath, 'MsDoc');
                } else {
                    // Conversión de archivos con formato docx
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath, 'Word2007');
                }

                $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                $pdfWriter->save($documentConvertedPath);
                //---------------------------------------------------------------------------------------------------------
                // Si es un archivo en formato Open Office Text (ODT)
                // se convierte a PDF con PHP Word
                //
                // @link https://github.com/PHPOffice/PHPWord
                //---------------------------------------------------------------------------------------------------------
            } elseif ($document['type'] == 'application/vnd.oasis.opendocument.text') {
                // Configuramos PhpWord
                \PhpOffice\PhpWord\Settings::setPdfRenderer(
                    \PhpOffice\PhpWord\Settings::PDF_RENDERER_MPDF,
                    base_path(config('documents.mpdf'))
                );

                // Carga el archivo OpenOffice ODT Writer
                $odtfile = \PhpOffice\PhpWord\IOFactory::load($filePath, 'ODText');

                // Guarda el archivo convertido a PDF
                $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($odtfile, 'PDF');
                $pdfWriter->save($documentConvertedPath);
                //---------------------------------------------------------------------------------------------------------
                // Si es un archivo en formato Microsoft Excel
                // se convierte a PDF con PhpSpreadsheet
                //
                // @link https://phpspreadsheet.readthedocs.io/en/latest/
                //---------------------------------------------------------------------------------------------------------
            } elseif ($document['type'] == 'application/vnd.ms-excel'
                                        ||
                    $document['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                        ||
                    $document['type'] == 'application/vnd.oasis.opendocument.spreadsheet'
            ) {
                // Carga el archivo Excel
                $spreadsheet  = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

                // Guarda el archivo convertido a PDF
                $pdfWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Dompdf');
                $pdfWriter->save($documentConvertedPath);
            } else {
                // El archivo no es convertible
                throw new DocumentNotValidException($document['name']);
            }

            // Obtiene el tamaño del archivo convertido
            $document['converted_size'] = Storage::disk('public')->size($document['converted_path']);

            // Obtiene la ruta del archivo original
            $filePath = Storage::disk('public')->path($document['converted_path']);
            
            // Obtiene el número de páginas del documento PDF
            $pdfInfo = new PdfInfo($filePath);
            $document['pages'] = $pdfInfo->pages();

            // Si se usa almacenamiento S3, se copia el archivo resultante de la conversión en el bucket S3
            if (AppStorage::isS3()) {
                // Se copia el archivo al almacenamiento S3
                Storage::disk('s3')->put(
                    $document['converted_path'],
                    Storage::disk('public')->get($document['converted_path']),
                );
                // Se elimina el archivo del almacenamiento público local
                if ($removeTemps) {
                    Storage::disk('public')->delete($document['converted_path']);
                }
            }
        }
    }
}
