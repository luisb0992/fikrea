@extends('workspace.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/workspace/document.css')" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Se destacan las páginas del documento que debe firmar')
    <div class="page-title-subheading">
        <p>
            @lang('Acceda a cada una de ellas y firme en los lugares indicados').
        </p>
    </div>
</div>
@stop

{{-- 
    Aquí incluímos el contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12">

    {{-- Los botones de acción --}}
    <div class="col-md-12 mt-2">
        <div class="btn-group" role="group">
            
            {{-- Botón cancelar proceso de firma--}}
            <a href="@route('workspace.home', ['token' => $token])"
                class="btn btn-lg btn-danger"
            >
                @lang('Cancelar')
            </a>
            {{-- /Botón cancelar proceso de firma--}}

            {{--  modal para agregar o ver un comentario  --}}
            @include('common.comments.button-add-comment-modal', [
                'validationType' => config('validations.document-validations.handWrittenSignature')
            ])

        </div>
    </div>
    {{--/Los botones de acción --}}

    <div>
        <p>
            @lang('Páginas que deben ser firmadas se muestran así') <span class="page-number remaining text-center">1</span>
            @lang('Las restantes se muestran así') <span class="page-number text-center">2</span>
        </p>
    </div>

    {{-- Mensajes --}}
    <div id="messages" class="d-none"
        data-process-document-title="@lang('Procesando el documento')"
        data-process-document-text="@lang('Obteniendo las páginas...')"
    ></div>
    {{-- /Mensajes --}}

    <input type="hidden" id="document" value=""
        data-signs="@json($signer->signs)"
        data-pdf="@route('dashboard.document.pdf', ['token' => $signer->token])"
        data-page="@route('workspace.validate.signature.page', ['token' => $signer->token, 'page' => '#'])" 
        data-remaining-pages="{{$pages}}" 
    />

    {{-- Miniaturas de cada página del documento --}}
    <ul id="pages">
        <li class="page template" data-page="">
            <div class="pagination">
                <div class="page-number"></div>
            </div>
        </li>
    </ul>
    {{--/ Miniaturas de cada página del documento --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
{{--
    Pdf.js
    @link https://mozilla.github.io/pdf.js/
--}}
<script src="@asset('assets/js/libs/pdf.min.js')"></script>

{{-- Moment.js --}}
<script src="@asset('assets/js/libs/moment-with-locales.min.js')"></script>

<script src="@mix('assets/js/workspace/document.js')"></script>
@stop