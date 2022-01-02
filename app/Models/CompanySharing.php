<?php

/**
 * Modelo de Compartir datosde la empresa del usuario
 *
 * Usado para la generación de la facturas, es decir, para asociar un usuario a una compañía
 * y para compartir la informacion unica a personas externas
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lang;

class CompanySharing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_sharings';

    /**
     * Lista de atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'user_id',                  // El id del usuario asociado a la cmpañía
        'name',                     // El nombre o razón social de la compañía
        'cif',                      // El código de indentifcación fiscal o CIF
                                    // @link https://es.wikipedia.org/wiki/C%C3%B3digo_de_identificaci%C3%B3n_fiscal
        'address',                  // La dirección postal de la compaía
        'phone',                    // El teléfono de la compañía
        'city',                     // La localidad/ciudad
        'province',                 // La provincia/región/estado
        'country',                  // El país
        'code_postal',              // El codigo postal
        'dial_code',                // Codigo prefijo del pais
        'email',                    // Email alternativo para las notificaciones de factura
        'title',                    // Si desea agregar un titulo
        'comment',                  // Si desea agregar un comentario
        'signature',                // Si se debe enviar la firma del usuario
        'token',                    // EL token de acceso
    ];

    protected $casts = [
        'signature' => 'boolean',
    ];

    /**
     * Obtiene el usuario asociado a la compaía
     *
     * @return BelongsTo                        El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retorna el titulo de la facturacion compartida
     *
     * @return string|null
     */
    public function getSomeTitleAttribute(): ?string
    {
        return $this->title ?? Lang::get('Sin titulo');
    }

    /**
     * Retorna el comentario de la facturacion compartida
     *
     * @return string|null
     */
    public function getSomeCommentAttribute(): ?string
    {
        return $this->comment ?? Lang::get('Sin comentarios');
    }

    /**
     * Obtiene los datos de facturacion por su token
     *
     * @param string $token                     El token
     *
     * @return self                             los datos de facturacion
     * @throws ModelNotFoundException           No existe
     */
    public static function findByToken(string $token): self
    {
        return self::where('token', $token)->firstOrFail();
    }
}
