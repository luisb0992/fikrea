<?php

/**
 * Trait DocumentProcess
 *
 * Procesa un documento en imágenes para poder incorporar las firmas
 * o las cajas de texto
 *
 * Requiere Imagick instalado en el sistema
 *
 * @see https://www.php.net/manual/es/book.imagick.php
 *
 * La extensión Imagick debe estar convenientemente configurada para aceptar el procesamiento
 * de archivos PDF. Para ello, hay que editar el archivo:
 *
 * /etc/ImageMagick-6/policy.xml
 *
 * Se debe permitir, explícitamente, el procesamiento de archivos PDF, para lo que se debe comentar la línea:
 *
 * <policy domain="coder" rights="none" pattern="PDF" />
 *
 * que debe quedar, por tanto, así:
 *
 * <!--<policy domain="coder" rights="none" pattern="PDF" />-->
 *
 * La conversión de archivos en imágenes hace un uso intensivo de la memoria del sistema.
 * Para dar más memoria a Imagick para procesar archivos grandes, por ejemplo, 8 GB, modificar la directiva siguiente:
 *
 * <policy domain="resource" name="memory" value="8GiB" />
 *
 * También se debe ajustar la memoria que puede consumir PHP.
 * Para ello se debe ajustar la variable memory_limit en el archivo php.ini
 * a un valor acorde a la memoria disponible en el sistema:
 *
 * @see https://www.php.net/manual/es/ini.core.php#ini.memory-limit
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Signature\Traits;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Storage;

use App\Models\Document;
use App\Models\Sign;
use App\Models\DocumentStamp;
use App\Models\Textbox;
use App\Models\User;
use Fikrea\AppStorage;

use Fikrea\Exception\DocumentNotValidException;
use Fikrea\Exception\DocumentTooBigException;

/**
 * Constantes definidas para las orientaciones posibles
 */
define('ORIENTATION_HORIZONTAL', 1);
define('ORIENTATION_VERTICAL', 2);

/**
 * Constante definida con la resolución estándar
 *
 * @link https://es.wikipedia.org/wiki/Puntos_por_pulgada
 */
define('STANDAR_DOCUMENT_RESOLUTION', 72);

trait DocumentProcess
{
    /**
     * Obtiene el documento en imágenes
     *
     * Se parte del archivo ya convertido previamente a PDF
     * y se descompone en tantas imágenes como páginas
     *
     * @param Document $document                El documento
     *
     * @return string[]                         Una lista de imágenes que se almacenan  en la carpeta de imágenes
     *                                          El índice del array es el número de página
     *
     * @throws DocumentNotValidException        El documento no es válido
     * @throws DocumentTooBigException          El documento posee demasiadas páginas para ser procesado
     */
    protected function getDocumentImages(Document $document): array
    {
        /**
         * Tener en cuenta que dos procesos diferentes pueden modificar el documento original
         *
         * 1 - Cajas de textos
         * 2 - Firma manuscrita
         *
         * por lo que cuando se vaya a adicionar uno, no excluir el otro
         */

        // Si el almacenamiento es S3 debe copiarse a la carpeta pública local para su procesamiento
        // si no existía previamente, aqui debemos copiar el archivo firmando para no perder los textos
        // o las firmas que sehan realizado con anterioridad [signed_path]
        if (AppStorage::isS3() && !Storage::disk('public')->exists($document['converted_path'])) {
            Storage::disk('public')->put(
                $document['converted_path'],
                Storage::disk('s3')->get($document['converted_path'])
            );
        }

        // La ruta absoluta del archivo convertido (PDF)
        $convertedFile = Storage::disk('public')->path($document['converted_path']);

        // Obtiene la carpeta de las imágenes del documento
        $imagesFolder  = config('documents.folder.images');

        // Obtiene el formato de las imágenes a generar
        $imageFormat   = config('documents.images.format');

        // Inicia Imagick
        $imagick = new \Imagick($convertedFile);

        // Antes de cargar todo el documento en la memoria
        // obtenemos los atributos básicos del mismo
        try {
            $imagick->pingImage($convertedFile);
        } catch (\ImagickException $e) {
            // El formato del archivo no es válido
            throw new DocumentNotValidException($convertedFile);
        }

        // Obtiene el número de páginas del documento PDF

        // El número de imágenes es el doble que el real, pero no se documenta este comportamiento
        // para la función getNumberImages de Imagick
        // @see https://www.php.net/manual/es/imagick.getnumberimages.php
        $document->pages = $imagick->getNumberImages() / 2;

        // Si se supera el número de páginas que puede procesar el sistema
        // y que se establece en la configuración
        if ($document->pages > config('documents.max.pages')) {
            // Marcamos el documento como procesado
            $document->hasBeenProcessed();
            // Lanzamos la excepción
            throw new DocumentTooBigException($convertedFile);
        }

        // Guarda el documento (el número de páginas del mismo)
        $document->save();

        // Importante destruir el objeto creado para generar uno nuevo
        // leyendo, ahora sí, toda la información de las imágenes en memoria
        $imagick->destroy();

        // Fija la resolución de la imágen

        // Es fundamental realizar esta operación antes de inciar el procesado de la misma
        // para no obtener imágenes con una calidad pobre

        list($imageWidthResolution, $imageHeightResolution) =
            [
                config('documents.images.resolution.width'),
                config('documents.images.resolution.height'),
            ];

        $imagick->setResolution($imageWidthResolution, $imageHeightResolution);

        // Vamos obteniendo cada una de las imágenes, una por página
        for ($images = [], $i = 0; $i < $document->pages; $i++) {
            // Obtenemos cada una de las imágenes
            // @see https://www.php.net/manual/es/imagick.readimage.php
            try {
                $imagick->readImage("{$convertedFile}[{$i}]");
            } catch (\ImagickException $e) {
                throw new DocumentTooBigException($convertedFile);
            }

            // Obtendremos imágenes el formato indicado, sustituyendo el fondo transparente (el canal alfa)
            // y reemplazándolo por un fondo de color blanco (#ffffff)
            $imagick->setImageFormat($imageFormat);

            // En el caso que el formato de las imágenes sea JPG
            // Obtenemos las imágenes con la máxima calidad posible
            if ($imageFormat == 'jpg') {
                $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $imagick->setImageCompressionQuality(100);
            }

            // Sustituye el canal alfa de la imagen por un fondo blanco
            // sino en la conversión de un PDF a imágenes JPEG pueden obtenerse
            // fondos negros, que es lo que rige por defecto
            try {
                $imagick->setImageAlphaChannel(\Imagick::VIRTUALPIXELMETHOD_WHITE);
            } catch (\ImagickException $e) {
                // No se ha podido fijar el canal alpha de la imagen
                // No hacer nada
            }

            // Define el nombre de la imagen para su almacenamiento en la carpeta de imágenes
            // Todo el procesamiento de imágenes debe realizarse en la carpeta pública local
            $imageFilename = bin2hex(openssl_random_pseudo_bytes(16)) . '.' . $imageFormat;
            // Guarda la imagen generada
            $imageFilePath = Storage::disk('public')->path("{$imagesFolder}/{$imageFilename}");
            $imagick->writeImage($imageFilePath);

            $images[$i + 1] = $imageFilePath;
        }

        // Libera los recursos utilizados
        $imagick->destroy();

        // Si el almacenamiento es S3 se elimina el archivo resultante de la conversión
        // guardado en el almacenamiento público local
        if (AppStorage::isS3()) {
            Storage::disk('public')->delete($document['converted_path']);
        }

        return $images;
    }

    /**
     * Añade una firma a un documento
     *
     * @param Sign     $sign                    La firma a añadir
     * @param Document $document                El documento
     *
     * @return void
     */
    protected function addSignToDocument(Sign $sign, Document $document): void
    {
        // Obtenemos la imagen decodificada de la firma
        $signDecoded = base64_decode(preg_replace('#data:image/[^;]+;base64,#', '', $sign->sign));

        // Leemos la imagen de la firma
        $signImage = new \Imagick;
        $signImage->readImageBlob($signDecoded);

        $signImage->resizeImage(
            $this->pdfToImageCoordinates($signImage->getImageWidth(), ORIENTATION_HORIZONTAL),
            $this->pdfToImageCoordinates($signImage->getImageHeight(), ORIENTATION_VERTICAL),
            config('documents.images.filter'),     // Filtro utilizado
            config('documents.images.blur'),       // Factor de borrosidad
        );

        // Obtenemos la ruta de la imagen de la página del documento a procesar
        $pageImagePath = $document->images[$sign->page];

        // Leemos la página
        $pageImage = new \Imagick($pageImagePath);

        // Obtenemos las coordenadas de la posición de la firma
        list($x, $y) =
            [
                $this->pdfToImageCoordinates($sign->x + config('documents.sign.offset.x'), ORIENTATION_HORIZONTAL),
                $this->pdfToImageCoordinates($sign->y + config('documents.sign.offset.y'), ORIENTATION_VERTICAL),
            ];

        // Inserta la imagen de la firma en la página en la posición indicada
        $pageImage->compositeImage($signImage, config('documents.images.composite.method'), $x, $y);

        // Añadimos a la firma el código que la identifica de forma única y el nombre del firmante
        // Ejemplo: Lucas Pérez [e6be5b92cb45d]
        $text = new \ImagickDraw;

        $text->setFillColor(config('documents.sign.code.color'));
        $text->setTextUnderColor(config('documents.sign.code.background'));
        $text->setFontSize(config('documents.sign.code.size'));

        $pageImage->annotateImage($text, $x, $y-50 > 0 ? $y-50 : 1, 0, "{$sign->signer} [{$sign->code}]");

        $pageImage->writeImage($pageImagePath);
    }

    /**
     * Añade un texto a un documento
     *
     * @param Textbox  $box                     La caja de texto a añadir
     * @param Document $document                El documento
     *
     * @return void
     */
    protected function addTextToDocument(Textbox $box, Document $document): void
    {
        // Obtenemos la ruta de la imagen de la página del documento a procesar
        $pageImagePath = $document->images[$box->page];

        // Leemos la página
        $pageImage = new \Imagick($pageImagePath);

        // Obtenemos las coordenadas de la posición de la caja de texto
        list($x, $y) =
            [
                $this->pdfToImageCoordinates($box->x + $box->shiftX + config('documents.sign.offset.x'), ORIENTATION_HORIZONTAL),
                $this->pdfToImageCoordinates($box->y + $box->shiftY + config('documents.sign.offset.y'), ORIENTATION_VERTICAL),
            ];
        // Añadimos a la firma el código que la identifica de forma única y el nombre del firmante
        // Ejemplo: Lucas Pérez [e6be5b92cb45d]
        $text = new \ImagickDraw;
        $text->setFillColor(config('documents.sign.code.color'));
        $text->setTextUnderColor(config('documents.sign.code.background'));
        $text->setFontSize(config('documents.sign.code.size'));

        $pageImage->annotateImage($text, $x, $y-50 > 0 ? $y-50 : 1, 0, "{$box->signer} [{$box->code}]");
        unset($text);

        // Añadimos el texto de la caja
        // Ejemplo: Lucas Pérez Pérez
        $textbox = new \ImagickDraw;
        $textbox->setFillColor(config('documents.sign.code.color'));
        $textbox->setTextUnderColor(config('documents.sign.code.background'));
        $textbox->setFontSize(config('documents.sign.code.size'));
        
        $pageImage->annotateImage($textbox, $x, $y, 0, "{$box->text}");
        unset($textbox);

        $pageImage->writeImage($pageImagePath);
    }

    /**
     * Añade un sello a un documento
     *
     * @param DocumentStamp $stamp                   El sello a añadir
     * @param Document      $document                El documento
     *
     * @return void
     */
    protected function addStampToDocument(DocumentStamp $stamp, Document $document): void
    {
        // Obtenemos la imagen decodificada del sello
        $stampDecoded = base64_decode(preg_replace('#data:image/[^;]+;base64,#', '', $stamp->stamp));

        // Leemos la imagen del sello
        $stampImage = new \Imagick;
        $stampImage->readImageBlob($stampDecoded);

        $stampImage->resizeImage(
            $this->pdfToImageCoordinates($stampImage->getImageWidth(), ORIENTATION_HORIZONTAL),
            $this->pdfToImageCoordinates($stampImage->getImageHeight(), ORIENTATION_VERTICAL),
            config('documents.images.filter'),     // Filtro utilizado
            config('documents.images.blur'),       // Factor de borrosidad
        );

        // Obtenemos la ruta de la imagen de la página del documento a procesar
        $pageImagePath = $document->images[$stamp->page];

        // Leemos la página
        $pageImage = new \Imagick($pageImagePath);

        // Obtenemos las coordenadas de la posición de la firma
        list($x, $y) =
            [
                $this->pdfToImageCoordinates($stamp->x, ORIENTATION_HORIZONTAL),
                $this->pdfToImageCoordinates($stamp->y, ORIENTATION_VERTICAL),
            ];

        // Inserta la imagen de la firma en la página en la posición indicada
        $pageImage->compositeImage($stampImage, config('documents.images.composite.method'), $x, $y);

        $pageImage->writeImage($pageImagePath);
    }

    /**
     * Convierte una medida o coordenada PDF a medida o coordenada sobre una imagen
     *
     * @param float  $coordinate                La coordenada o medida sobre PDF
     * @param int    $orientation               ORIENTATION_HORIZONTAL|ORIENTATION VERTICAL
     *                                          Horientación
     *
     * @return float                            La coordenada o medida sobre la imagen
     * @throws \Exception                       Orientación no válida
     */
    protected function pdfToImageCoordinates(float $coordinate, int $orientation): float
    {
        if ($orientation == ORIENTATION_HORIZONTAL) {
            $resolution = config('documents.images.resolution.width');
        } elseif ($orientation == ORIENTATION_VERTICAL) {
            $resolution = config('documents.images.resolution.height');
        } else {
            throw new \Exception('Orientación no válida');
        }

        return $coordinate * $resolution / STANDAR_DOCUMENT_RESOLUTION;
    }

    /**
     * Crea el documento firmado en formato PDF
     *
     * Se genera un documento, con las imágenes firmadas individuales
     *
     * @param Document $document                El documento
     *
     * @return string                           La ruta del archivo firmado
     */
    protected function createSignedDocument(Document $document): string
    {
        // Obtiene la ruta del documento firmado
        // que se almacena en la carpeta de archivos firmados (signed documents)
        $signedPath = Storage::disk('public')->path($document->signed_path);

        // Genera el documento PDF firmado
        $pdf = new \Imagick($document->images);

        $pdf->setImageFormat('pdf');
        $pdf->writeImages($signedPath, true);

        // Se obtiene el hash md5 y sha-1 del archivo
        $document->signed_md5  = md5(Storage::disk('public')->get($document->signed_path));
        $document->signed_sha1 = sha1(Storage::disk('public')->get($document->signed_path));

        $document->save();

        // Si el almacenamiento es S3, se copia el archivo firmado en el bucket S3
        if (AppStorage::isS3()) {
            // Se copia el archivo al almacenamiento S3
            Storage::disk('s3')->put(
                $document->signed_path,
                Storage::disk('public')->get($document->signed_path),
            );
            // Se elimina el archivo del almacenamiento público local
            Storage::disk('public')->delete($document->signed_path);
        }

        info("Signed Path del documento");
        info($document->signed_path);

        return $document->signed_path;
    }

    /**
     * Envia la notificacion necsaria  por sms o telefono al usuario y los firmantes
     * ademas de modificar las fechas del documento y los datos de la comparticion del documento
     *
     * @param Document      $document                 El documento
     * @param User          $user                     El usuario creador
     */
    public function sendNotificationOfDocumentToSigners(Document $document, User $user)
    {
        // se marca el documento como enviado
        $document->send();

        // Se registra una nueva compartición de Documento
        $document->sharings()->create([
            'type'      => 1,
            'signers'   => json_encode([
                'signers' => $document->signers->filter(fn ($signer) => !$signer->creator)->map(fn ($signer) => $signer->id)
            ])
        ]);

        // Notificar a los firmantes que no sean el creador/autor del documento
        // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
        $document->signers->filter(fn ($signer) => !$signer->creator)->each(function ($signer) use ($user) {
            if ($signer->email) {
                // Si se ha proporcionado el correo del firmante se notifica por email
                EmailController::sendWorkSpaceAccessEmail($user, $signer);
            } elseif ($signer->phone) {
                // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                SmsController::sendWorkSpaceAccessSms($user, $signer);
            }
        });

        // Se envía un correo al creador/autor del documento confirmando que ha compartido un documento
        EmailController::confirmDocumentShared($document->user, $document);
    }
}
