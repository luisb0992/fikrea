<?php

/**
 * Trait HasVisits
 *
 * Controla la visita de un firmante a una página
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Traits;

use App\Models\Signer;
use App\Models\SignerVisit;

use Fikrea\ModelAndView;

trait HasVisits
{
    /**
     * Registra una visita del usuario firmante a la vista de una página
     *
     * @param Signer       $signer              El usuario firmante
     * @param ModelAndView $mav                 La vista a registrar
     *
     * @return void
     */
    final protected function registerVisit(Signer $signer, ModelAndView $mav): void
    {
        // Inyecta la visita a la visita a la vista indicada
        $mav->append(
            [
                'visit' => $signer->registerVisit(),
            ]
        );
    }

    /**
     * Actualiza los datos de registro de la visita de un usuario
     * a un proceso de validación
     *
     * @param array $visit                      La visita
     * @param array $position                   La posición datum WGS84
     *
     * @return void
     */
    final protected function updateVisit(array $visit, array $position): void
    {
        // Obtiene la visita del usuario
        $signerVisit = SignerVisit::findOrFail($visit['id']);

        // Actualiza la posición de la visita
        $signerVisit->latitude  = $position['latitude'];
        $signerVisit->longitude = $position['longitude'];

        // Elimina la fecha de inicio que viene del servidor para evitar que pueda ser actualizada y
        // actualiza la fecha de finalización de la  visita
        unset($visit['starts_at']);
        $signerVisit->ends_at = now();
    
        $signerVisit->update($visit);
    }
}
