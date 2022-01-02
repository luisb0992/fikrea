<?php

/**
 * Genera el documento con el texto de las cajas, colocando cada una de los textos
 * efectuadas en su página y posición
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Models\Document;

use App\Http\Controllers\Signature\DocumentController;

class TextboxsInDocument implements ShouldQueue
{
     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El documento a firmar
     *
     * @var Document
     */
    protected Document $document;

    /**
     * El constructor
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Ejecuta el trabajo de generación del documento firmado
     *
     * @return void
     */
    public function handle()
    {
        // Genera el documento con las cajas de texto
        // app(DocumentController::class)->textboxsInDocument($this->document);

        // Genera el documento firmado
        app(DocumentController::class)->signDocument($this->document);
    }

    /**
     * Si el proceso de generación del documento falla
     *
     * @return void
     */
    public function failed()
    {
        Log::error("[Error] No se ha podido generar el documento con los textos {$this->document->name}");

        // Marca el documento como procesado
        $this->document->hasBeenProcessed();
    }
}
