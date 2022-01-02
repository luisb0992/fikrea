{{-- Condiciones de uso y políticas aplicables al sitio web --}}
<div class="row">
    <div class="col-md-12 text-center">
        <a href="#" data-toggle="modal" data-target="#modal-legal-warning">
            @lang('Aviso Legal')
        </a>
        |
        <a href="#" data-toggle="modal" data-target="#modal-privacity-policy">
            @lang('Política de Privacidad')
        </a>
        |
        <a href="#" data-toggle="modal" data-target="#modal-cookies-policy">
            @lang('Política de Cookies')
        </a>
        |
        <a href="#" data-toggle="modal" data-target="#modal-return-policy">
            @lang('Política de Devoluciones')
        </a>
        |
        <a href="#" data-toggle="modal" data-target="#modal-use-conditions">
            @lang('Condiciones de Uso')
        </a>
    </div>
</div>
{{--/Condiciones de uso y políticas aplicacables al sitio web --}}



{{-- Modales con los textos legales --}}
@include('landing.modals.legal.legal-warning')
@include('landing.modals.legal.privacity-policy')
@include('landing.modals.legal.cookies-policy')
@include('landing.modals.legal.return-policy')
@include('landing.modals.legal.use-conditions')
{{-- /Modales con los textos legales --}}
