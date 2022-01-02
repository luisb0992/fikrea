@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @if ($document)
        @lang('Editar Documento')
    @else
        @lang('Crear Documento')
    @endif
    <div class="page-title-subheading">
        @if ($document)
            <div>
                @lang('Puede editar el nombre y los comentarios del archivo')
            </div>
        @else
            <div>
                @lang('Cree un sencillo documento de texto para su firma')
            </div>
            <div>
                @lang('Si desea subir un documento para firmar, hágalo desde el <strong>Gestor de Archivos</strong>')
            </div>
        @endif
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

{{-- El mensaje flash que se muestra cuando la operación ha tenido éxito o error --}}
<div class="offset-md-3 col-md-6">
    @include('dashboard.sections.body.message-success')
    @include('dashboard.sections.body.message-error')
</div>
{{--/El mensaje flash que se muestra cuando la oepracion ha tenido éxito o error --}}

<div id="app" class="col-md-12">

    {{-- Modales necesarias --}}
    @include('dashboard.modals.documents.link-sidebar-modal')
    {{-- /Modales necesarias --}}

    {{-- Las rutas de las solicitudes de la aplicación --}}
    <div id="request" data-text-from-file="@route('dashboard.document.ocr')"></div>
    {{--/Las rutas de las solicitudes de la aplicación  --}}

    {{-- La configuración OCR de la aplicación --}}
    <div id="ocr" data-mimes="{{ $ocrMimeTypes }}" data-max-size="@config('ocr.max.size')"></div>
    {{--/La configuración OCR de la aplicación --}}

    {{-- Mensajes de la aplicación --}}
    <div id="message"
        data-file-is-not-valid="@lang('El archivo no es válido')"
        data-file-too-big="@lang('El archivo es demasiado grande para ser procesado')"
        data-ocr-failed="@lang('No se ha podido reconocer el texto')"
    ></div>
    {{--/Mensajes de la aplicación --}}
    
    {{-- Datos del Documento --}}
    <div class="main-card mb-3 card offset-md-3 col-md-6">
        <div class="card-body">
            <h5 class="card-title">
                @if ($document)
                    @lang('Editar Documento')
                @else
                    @lang('Crear Documento')
                @endif
            </h5>
            <form id="form" method="post" action="@route('dashboard.document.save')">

                @csrf

                <input type="hidden" id="id" name="id" value="@exists($document->id)" />

                {{-- El nombre del documento --}}
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="name" class="required">@lang('Nombre')</label>
                            @error('name')
                            <input type="text" name="name" id="name" value="@old('name')" class="form-control is-invalid" autofocus />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                                @if ($document)
                                <input type="text" name="name" id="name" value="@stripext($document->name)" class="form-control" autofocus />
                                @else
                                <input type="text" name="name" id="name" class="form-control" autofocus />
                                @endif
                            @enderror
                        </div>
                    </div>
                </div>
                {{--/(El nombre del documento --}}

                {{-- Los comentarios opcionales al documento --}}
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="comment">@lang('Comentarios')</label>
                            @error('name')
                                <textarea name="comment" id="comment" class="form-control">@old('comment')</textarea>
                            @else
                                <textarea name="comment" id="comment" class="form-control">@exists($document->comment)</textarea>
                            @enderror
                        </div>
                    </div>
                </div>
                {{--/Los comentarios opcionales al documento --}}

                {{-- El contenido del documento --}}
                @if (!$document || $document->content)
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="content">@lang('Contenido')</label>
                            @error('name')
                                <textarea name="content" id="content" class="form-control" rows="16">@old('content')</textarea>
                            @else
                                <textarea name="content" id="content" class="form-control" rows="16">@exists($document->content)</textarea>
                            @enderror
                        </div>
                    </div>               
                </div>
                @endif
                {{--/El contenido del documento --}}

                {{-- Los botones de acción --}}
                <div class="text-right">
                    <div class="small">
                        <em>@lang('Tamaño admitido para el archivo de imagen :size MB', ['size' => config('ocr.max.size')])</em>
                    </div>
                    <div class="btn-group">
                        <input id="file" type="file" accept="image/*;application/pdf" @change.prevent="getTextFromFile" class="d-none" />
                        <button @click.prevent="selectFile" class="mt-2 btn btn-info mr-1">@lang('Añadir texto desde imagen')</button>
                        <button class="mt-2 btn btn-success">@lang('Guardar')</button>
                    </div>
                </div>
                {{--/Los botones de acción --}}

            </form>
        </div>
    </div>
</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@asset('assets/js/dashboard/vendor/tinymce/tinymce.min.js')" referrerpolicy="origin"></script>
<script src="@mix('assets/js/dashboard/documents/edit.js')"></script>
@stop