<?php

/**
 * Define el comando:
 *
 * php artisan send:reminderPendingValidation
 *
 * que envía notificaciones a los usuarios que aún tienen validaciones pendientes de ser realizadas
 *
 * Sólo se envían estas notificaciones durante los próximos 15 días a la fecha de actualización por última vez
 * del documento
 *
 * @copyright 2021 Retail Servicios Externos SL
 * @author javieru <javi@gestoy.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\Models\Signer;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\VerificationForm;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\SmsController;

class SendReminderPendingValidation extends Command
{
    /**
     * Signatura y nombre del comando
     *
     * @var string
     */
    protected $signature = 'send:reminderPendingValidation';

    /**
     * Descripción del comando de consola
     *
     * @var string
     */
    protected $description = 'Envía notificaciones a los usuarios que aún no han completado las validaciones';

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
    public function handle(): void
    {
        // Envía las solicitudes de validación de documentos pendientes
        $this->sendPendingDocumentValidations();

        // Envía las solicitudes de documentos pendientes
        $this->sendPendingDocumentRequests();

        // Envía las solicitudes de verificaciones de datos pendientes
        $this->sendPendingCertificationsForms();
    }

    /**
     * Envía las solicitudes de validación de documentos pendientes
     *
     * @return void
     */
    protected function sendPendingDocumentValidations(): void
    {
        // La lista de firmantes a notificar
        $signers = collect();

        // Los documentos para validar en los últimos 15 días
        $documents = Document::whereDate('updated_at', '>=', Carbon::now()->subDays(15));
        
        // Tomo el signer en los procesos pendientes de las validaciones de los documentos
        $documents->each(function ($document) use (&$signers) {
            // Obtiene los firmantes del documento que no han completado alguna de las validaciones
            $documentSigners = $document->validations()
                ->whereHas('process', function ($query) {
                    $query->where('workspace_statu_id', \App\Enums\WorkspaceStatu::PENDIENTE);
                })
                ->get()
                ->map(fn ($validation) => Signer::find($validation->user))
                ->unique();
            
            if ($documentSigners->isNotEmpty()) {
                // Añade los firmantes del documento que no han completado
                // alguna de las validaciones propuestas a la lista
                $signers = $signers->merge($documentSigners);
            }
        });
        
        /**
         * Notificar a los firmantes que no sean el creador/autor del documento
         * Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
         *
         * Y que no hayan cancelado el proceso
         */
        $signers->filter(fn ($signer) => !$signer->creator && !$signer->canceled_at)->each(function ($signer) {
            // Obtiene el usuario del documento
            $user = $signer->document->user;

            // Obtiene la fecha de actualización del documento
            $documentLastUpdate = $signer->document->updated_at;

            // Obtiene el número de días que deben transcurrir entre notificaciones consecutivas
            $days = intval($user->config->notification->days);
            
            // Sólo se notifica en el caso que los días trascurridos desde la última actualización
            // sea un múltiplo del intervalo de días que haya configurado el usuado en su configuración
            if ($documentLastUpdate->diff(Carbon::now())->days % $days == 0) {
                // Se registra una nueva compartición de Documento
                $sharing = $signer->document->sharings()->create(
                    [
                        'signers' => json_encode(
                            [
                                'signers' => [$signer->id],
                            ]
                        )
                    ]
                );

                if ($signer->email) {
                    // Si se ha proporcionado el correo del firmante se notifica por email
                    EmailController::sendWorkSpaceAccessEmail($user, $signer, $sharing);
                } elseif ($signer->phone) {
                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SMSController::sendWorkSpaceAccessSms($user, $signer, $sharing);
                }
            }
        });
    }

    /**
     * Envía las solicitudes de documentos pendientes
     *
     * @return void
     */
    protected function sendPendingDocumentRequests(): void
    {
        // La lista de firmantes a notificar
        $signers = collect();

        // Las solicitudes de documentos a realizar en los últimos 15 días
        $requests = DocumentRequest::whereDate('created_at', '>=', Carbon::now()->subDays(15));

        // Tomo el signer en los procesos pendientes de los firmantes de las solicitudes
        // que no tengan validaciones(sino la solicitud es una validación)
        $requests->each(function ($request) use (&$signers) {
            $documentSigners = $request->signers()->get()
                ->filter(
                    fn($signer) => $signer->signerProcess() == \App\Enums\SignerProcesType::REQUEST_PROCESS
                    &&
                    $signer->process->isPending()
                )
                ->map(fn ($signer) => $signer)
                ->unique();
            
            if ($documentSigners->isNotEmpty()) {
                // Añade los firmantes del documento que no han completado la solicitud de documentos
                $signers = $signers->merge($documentSigners);
            }
        });

        /**
         * Notificar a los firmantes que no sean el creador/autor de la solicitud de documentos
         * Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
         *
         * Y que no haya cancelado la solicitud
         */
        $signers->filter(fn ($signer) => !$signer->creator && !$signer->canceled_at)->each(function ($signer) {
            // Obtiene el usuario de la solicitud de documentos
            $user = $signer->request()->user;

            // Obtiene la fecha de creación de la solicitud de documentos
            $documentCreatedDate = $signer->request()->created_at;
            // Obtiene el número de días que deben trascurrir entre notificaciones consecutivas
            $days = intval($user->config->notification->days);
            
            // Sólo se notifica en el caso que los días trascurridos desde la última actualización
            // sea un múltiplo del intervalo de días que haya configurado el usuado en su configuración
            if ($documentCreatedDate->diff(Carbon::now())->days % $days == 0) {
                // Se registra una nueva compartición de Solicitud de documentos
                // DocumentRequestSharing
                $sharing = $signer->request()->sharings()->create(
                    [
                        'signers' => json_encode(
                            [
                                'signers' => [$signer->id],
                            ]
                        )
                    ]
                );

                if ($signer->email) {
                    // Si se ha proporcionado el correo del firmante se notifica por email
                    EmailController::sendWorkSpaceAccessEmail($user, $signer, $sharing);
                } elseif ($signer->phone) {
                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SMSController::sendWorkSpaceAccessSms($user, $signer, $sharing);
                }
            }
        });
    }

    /**
     * Envía las solicitudes de verificaciones de datos pendientes
     *
     * @return void
     */
    protected function sendPendingCertificationsForms(): void
    {
        // La lista de firmantes a notificar
        $signers = collect();

        // Las certificaciones de datos realizar en los últimos 15 días
        $requests = VerificationForm::whereDate('created_at', '>=', Carbon::now()->subDays(15));

        // Tomo el signer en los procesos pendientes de los firmantes de las solicitudes
        $requests->each(function ($request) use (&$signers) {
            $validationSigners = $request->signers()->get()
                ->filter(
                    fn($signer) => $signer->signerProcess() == \App\Enums\SignerProcesType::FORM_PROCESS
                    &&
                    $signer->process->isPending()
                )
                ->map(fn ($signer) => $signer)
                ->unique();
            
            if ($validationSigners->isNotEmpty()) {
                // Añade los firmantes que no han completado la certificación
                $signers = $signers->merge($validationSigners);
            }
        });

        /**
         * Notificar a los firmantes que no sean el creador/autor de la certificación de datos
         * Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
         *
         * Y que no haya cancelado el proceso
         */
        $signers->filter(fn ($signer) => !$signer->creator && !$signer->canceled_at)->each(function ($signer) {
            // Obtiene el usuario de la solicitud de documentos
            $user = $signer->verificationForm->user;

            // Obtiene la fecha de creación de la certificación de datos
            $documentCreatedDate = $signer->verificationForm->created_at;
            // Obtiene el número de días que deben trascurrir entre notificaciones consecutivas
            $days = intval($user->config->notification->days);
            
            // Sólo se notifica en el caso que los días trascurridos desde la última actualización
            // sea un múltiplo del intervalo de días que haya configurado el usuado en su configuración
            if ($documentCreatedDate->diff(Carbon::now())->days % $days == 0) {
                // Se registra una nueva compartición de certificación de datos
                // VerificationFormSharing
                $sharing = $signer->verificationForm->sharings()->create(
                    [
                        'signers' => json_encode(
                            [
                                'signers' => [$signer->id],
                            ]
                        )
                    ]
                );

                if ($signer->email) {
                    // Si se ha proporcionado el correo del firmante se notifica por email
                    EmailController::sendWorkSpaceAccessEmail($user, $signer, $sharing);
                } elseif ($signer->phone) {
                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SMSController::sendWorkSpaceAccessSms($user, $signer, $sharing);
                }
            }
        });
    }
}
