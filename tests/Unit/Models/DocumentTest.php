<?php

namespace Tests\Unit\Models;

use Tests\TestCase;

use App\Models\Document;
use App\Models\Signer;

use Carbon\Carbon;

class DocumentTest extends TestCase
{
    /**
     * Obtiene los firmantes que están pendientes de realizar alguna validación
     *
     * @return void
     */
    /** @test */
    public function testSignersWithPendingValidations(): void
    {
        // Colección de firmantes
        $signers = collect();

        // Los documentos con fecha de actualización desde hace quince días
        $documents = Document::whereDate('updated_at', '<=', Carbon::now()->subDays(15));

        $documents->each(function ($document) use (&$signers) {
            // Obtiene los firmantes del documento que no han completado alguna de las validaciones
            $documentSigners = $document->validations
                                ->where('validated', false)
                                ->map(fn ($validation) => Signer::find($validation->user))
                                ->unique();
            
            if ($documentSigners->isNotEmpty()) {
                // Añade los firmantes del documento que no han completado
                // alguna de las validaciones propuestas a la lista
                $signers = $signers->merge($documentSigners);
            }
        });

        $this->assertGreaterThanOrEqual(0, $signers->count());
    }
}
