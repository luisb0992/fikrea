<?php

/**
 * Modelo de Compartir en redes sociales
 *
 * Gestiona Las comparticiones que pueden haber en distintos procesos de la app
 * como arvhivos, documentos.
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use App\Enums\SocialMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareOnSocialNetwork extends Model
{
    /**
     * Tabla asociada al modelo
     *
     * @var string
     */
    protected $table = 'share_on_social_networks';

    /**
     * Los atrbutos asignados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'shareontable_id',          // id del modelo padre
        'shareontable_type',        // el modelo padre
        'user_id',                  // el usuario que comparte
        'social_network',           // la red social a la que comparte
        'text',                     // el texto al compartir (opcional)
        'hashtag',                  // el hashtag al compartir (opcional)
        'url'                       // la url que se va a compartir
    ];

    /**
     * Conversión de tipos
     *
     * @var array
     */
    protected $casts =
    [
        'social_network' => 'int',
    ];

    /**
     * El modelo padre al cual pertenece
     *
     */
    public function shareontable()
    {
        return $this->morphTo();
    }

    /**
     * Devuelve el usuario que comaparte
     *
     * @return BelongsTo            El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Devolver el nombr de la red social perteneciente
     *
     * @return string|null          La red social o null sino existe
     */
    public function getSocialNetworkAttribute() : ?string
    {
        $social = null;

        switch ($this->social_network) {
            case SocialMedia::FACEBOOK:
                $social = (string) SocialMedia::FACEBOOK;
                break;
            case SocialMedia::TWITTER:
                $social = (string) SocialMedia::TWITTER;
                break;
            case SocialMedia::LINKEDIN:
                $social = (string) SocialMedia::LINKEDIN;
                break;
            case SocialMedia::WHATSAPP:
                $social = (string) SocialMedia::WHATSAPP;
                break;
            default:
                break;
        }

        return $social;
    }
}
