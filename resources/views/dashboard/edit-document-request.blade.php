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
    @if ($documentRequest)
        @lang('Editar Solicitud de Documentos')
    @else
        @lang('Crear Solicitud de Documentos')
    @endif
    <div class="page-title-subheading">
        <div>
            @lang('Solicite a los usuarios los documentos que precise')
        </div>
        <div>
            @lang('Por ejemplo, puede solicitar su documento nacional de identidad, su curriculum vitae')
        </div>
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

<div v-cloak id="app" class="col-md-12">

    {{-- Modales --}}
    @include('dashboard.modals.requests.help-for-document-request')
    {{--/Modales --}}

    {{-- Datos del Documento --}}
    <div class="main-card mb-3 card offset-md-2 col-md-8">
        <div class="card-body">

            <form id="form-contact" method="post">

                <h3 class="mb-4">@lang('Datos de la Solicitud')</h3>

                @csrf

                {{-- Datos generales de la solicitud de documentos --}}
                <input type="hidden" id="id" name="id" value="@exists($documentRequest->id)" />

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label class="required" for="name">@lang('Nombre de la Solicitud')</label>
                        <input v-model="name" type="text" name="name" id="name" class="form-control" 
                            value="@exists($documentRequest->name)" placeholder="@lang('Un breve nombre descriptivo')" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="comment">@lang('Comentarios')</label>
                        <textarea name="comment" id="comment" rows="16" class="form-control">@exists($documentRequest->comment)</textarea>
                    </div>
                </div>
                {{--/Datos generales de la solicitud de documentos --}}

       
                <h3>@lang('Documentos a Solicitar')</h3>
                

                <div class="form-row">
         
                    {{-- Lista de documentos que se solicitan --}}
                    <div class="col-md-12" v-for="(document, index) in documents">
                        
                        <div class="text-secondary text-right bold">@lang('Documento') #@{{index+1}}</div>

                        <hr />
                        
                        <div class="col-md-12 mt-4">
                
                            {{-- El nombre del documento --}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="document" class="required">@lang('Documento')</label>
                                
                                    @if ($documentExamples)
                                        {{-- La lista de documentos requeridos de ejemplo --}}
                                        <div id="document-examples" data-documents="@json($documentExamples)"></div>
                                        {{-- Componente Select2
                                            @link https://github.com/godbasin/vue-select2

                                            Para las opciones originales del Select2@hasSection ('
                                            @link https://select2.org/configuration/options-api')
                                        --}}
                                        <Select2 id="document" class="select2"
                                            :options="documentExamples" :settings="{tags: true}" v-model="document.name" />
                                    @else 
                                        {{-- Si no hay documentos requeridos de ejemplo --}}
                                        <input id="document" v-model="document.name" class="form-control" placeholder="@lang('Documento que se solicita. Ejemplo: DNI.')" />
                                    @endif
                                </div>
                            </div>
                            {{--/El nombre del documento --}}

                            {{-- El comentario del documento --}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>@lang('Comentario')</label>
                                    <textarea v-model="document.comment" class="form-control" placeholder="@lang('comentario')"></textarea>
                                </div>
                            </div>
                            {{--/El comentario del documento --}}

                            {{-- El tipo de documento --}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>@lang('Tipo de Archivo')</label>
                                    <select v-model="document.type" class="form-control">
                                        <option value="" selected>@lang('Cualquiera')</option>
                                        @foreach ($documentTypes as $documentType)
                                            <option value="{{$documentType}}">
                                                @lang( $documentType )
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{--/El tipo de documento --}}

                            {{-- El periodo máximo de validez del documento --}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label class="mr-4 label-control">@lang('Periodo de validez')</label>
                                    <div class="text-right mb-2">
                                        <div class="btn-group">
                                            {{-- Selecciona un periodo en días --}}
                                            <button type="button" class="btn btn-primary" @click.prevent="selectValidity(document, 0, TimeUnit.Days)"
                                                :class="document.validity_unit ==  TimeUnit.Days ? 'btn-secondary': ''"
                                            >
                                                @lang('días')
                                            </button>
                                            {{-- Selecciona un periodo en meses --}}
                                            <button type="button" class="btn btn-primary" @click.prevent="selectValidity(document, 0, TimeUnit.Months)"
                                                :class="document.validity_unit ==  TimeUnit.Months ? 'btn-secondary': ''"
                                            >
                                                @lang('meses')
                                            </button>
                                            {{-- Selecciona un periodo en años --}}
                                            <button type="button" class="btn btn-primary" @click.prevent="selectValidity(document, 0, TimeUnit.Years)"
                                                :class="document.validity_unit ==  TimeUnit.Years ? 'btn-secondary': ''"
                                            >
                                                @lang('años')
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="document.validity > 0" class="col-md-12 bold btn btn-outline-primary p-2 no-events">
                                        <span v-if="document.validity_unit == TimeUnit.Days"> 
                                            @{{document.validity}} 
                                            @lang('día(s) a partir de la fecha actual')
                                        </span>
                                        <span v-else-if="document.validity_unit == TimeUnit.Months">
                                            @{{document.validity}} 
                                            @lang('mes(es) a partir de la fecha actual')
                                        </span>
                                        <span v-else-if="document.validity_unit == TimeUnit.Years">
                                            @{{document.validity}} 
                                            @lang('año(s) a partir de la fecha actual')
                                        </span>
                                        <span v-else>
                                            @lang('Sin determinar')
                                        </span>
                                    </div>
                                    <div v-else class="col-md-12 bold btn btn-outline-primary p-2 no-events">
                                        <span> 
                                            @lang('Sin definir')
                                        </span>
                                    </div>
                                    <input type="range" class="form-control" v-model="document.validity" min="0" 
                                            :max="document.validity_unit == TimeUnit.Days ? 60 : 12" value="0" />
                                    <div class="text-info">
                                        @lang('Deslice el control o seleccione un periodo determinado de validez del documento desde la fecha actual')
                                    </div>
                                    <div class="col-md-12 text-center mt-2">
                                        <button type="button" @click.prevent="selectValidity(document, 7, TimeUnit.Days)" class="btn btn-secondary m-1">7 @lang('días')</button>
                                        <button type="button" @click.prevent="selectValidity(document, 15, TimeUnit.Days)" class="btn btn-secondary m-1">15 @lang('días')</button>
                                        <button type="button" @click.prevent="selectValidity(document, 1, TimeUnit.Months)" class="btn btn-secondary m-1">1 @lang('mes')</button>
                                        <button type="button" @click.prevent="selectValidity(document, 3, TimeUnit.Months)" class="btn btn-secondary m-1">3 @lang('meses')</button>
                                        <button type="button" @click.prevent="selectValidity(document, 6, TimeUnit.Months)" class="btn btn-secondary m-1">6 @lang('meses')</button>
                                        <button type="button" @click.prevent="selectValidity(document, 1, TimeUnit.Years)" class="btn btn-secondary m-1">1 @lang('año')</button>
                                    </div>
                                </div>
                            </div>
                            {{--/La fecha de validez del documento --}}

                            {{-- El tamaño máximo del documento --}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>@lang('Tamaño Máximo')</label>
                                    <select v-model="document.maxsize" class="form-control">
                                        <option value="" selected>@lang('Cualquiera')</option>
                                        @foreach ($documentSizes as $documentSize)
                                            <option value="{{$documentSize}}">
                                                @filesize($documentSize)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{--/El tamaño máximo del documento --}}

                            {{-- Eliminar el documento --}}
                            <div class="form-row">
                                <div class="form-group col-md-12 text-right mt-2">
                                    <button type="button" @click.prevent="removeDocument(document)" class="btn btn-danger">
                                        @lang('Eliminar')
                                    </button>
                                </div>
                            </div>
                            {{--/Eliminar el documento --}}

                        </div>

                        <hr />
                    
                    </div>
                    {{--/Lista de documentos que se solicitan --}}

                </div>

                {{-- Botones de acción --}}
                <div class="text-right mt-4">
                    <button @click.prevent="addNewDocument" type="button" class="mt-2 btn btn-secondary">@lang('Añadir otro Documento')</button>
                    <button @click.prevent="save" :disabled="saveButtonDisabled" class="mt-2 btn btn-success"
                        data-save-document-request="@route('dashboard.document.request.save')"
                        data-after-save-redirect-to="@route('dashboard.document.request.signers')"
                        >
                        @lang('Guardar')
                    </button>
                </div>
                {{--/Botones de acción --}}

            </form>
        </div>
    </div>
</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@asset('assets/js/dashboard/vendor/tinymce/tinymce.min.js')" referrerpolicy="origin"></script>
<script src="@mix('assets/js/dashboard/requests/edit.js')"></script>
@stop