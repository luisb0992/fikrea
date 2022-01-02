{{--
    Botones que prmiten compartir archivos en las rdes sociales
    --------------------------------------------------------------------
    => $route: url a compartir
    => $type: el tipo de proceso el cual se comparte (archivos, documentos)
    => $id: el id del proceso el cual se comparte (archivos, documentos)
    --}}

<div class="d-none" id="divShareSocialNetwork"
    data-url-save="@route('dashboard.share.socialnetwork.save')"
    data-url-save-share-file="@route('dashboard.save.file.sharing')"
    data-type="{{ $type }}"
    data-route="{{ $route }}"

    {{--  mensajes de alerta  --}}
    data-error-social="@lang('No se ha indicado alguna red social')"
    data-error-url="@lang('La URL a compartir no existe')"
    data-error-type="@lang('Ha ocurrido un error, no existe un tipo definido')"
>
</div>

<div class="btn-group" role="group">
    <button id="btnShareSocialNetwork" type="button" class="btn btn-primary dropdown-toggle mr-1"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span data-toggle="tooltip" data-placement="bottom" title="@lang('Compartir en redes sociales')">
            <i class="fas fa-share-square"></i>
        </span>
    </button>
    <div class="dropdown-menu" aria-labelledby="btnShareSocialNetwork">

        {{--  Facebook  --}}
        <a class="dropdown-item p-2 text-facebook" href="#" @click.prevent="saveShareSocialMedia"
            data-social="{{ \App\Enums\SocialMedia::FACEBOOK }}"
            data-id="{{ $id }}">
                <i class="fab fa-facebook-square mr-2 fa-2x"></i> @lang('Facebook')
        </a>

        {{--  Teitter  --}}
        <a class="dropdown-item p-2 text-twitter" href="#" @click.prevent="saveShareSocialMedia"
            data-hashtag="{{ config('app.name') }}"
            data-text="@lang(':app. El procedimiento más rápido y sencillo de firma digital', ['app' => config('app.name')])"
            data-social="{{ \App\Enums\SocialMedia::TWITTER }}"
            data-id="{{ $id }}">
                <i class="fab fa-twitter-square mr-2 fa-2x"></i> @lang('Twitter')
        </a>

        {{--  linkedin  --}}
        <a class="dropdown-item p-2 text-linkedin" href="#" @click.prevent="saveShareSocialMedia"
            data-social="{{ \App\Enums\SocialMedia::LINKEDIN }}"
            data-id="{{ $id }}">
                <i class="fab fa-linkedin mr-2 fa-2x"></i> @lang('Linkedin')
        </a>

        {{--  Whatsapp  --}}
        <a class="dropdown-item p-2 text-success" href="#" @click.prevent="saveShareSocialMedia"
            data-text="@lang(':app. El procedimiento más rápido y sencillo de firma digital', ['app' => config('app.name')])"
            data-social="{{ \App\Enums\SocialMedia::WHATSAPP }}"
            data-id="{{ $id }}">
                <i class="fab fa-whatsapp-square mr-2 fa-2x"></i> @lang('Whatsapp')
        </a>
    </div>
</div>