@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Crea un formulario de datos')
        <div class="page-title-subheading">
            <div>
                @lang('En esta herramienta encontrará unas plantillas predeterminadas donde le sugerimos una serie de campos
                que puede rellenar para que la persona se los valide o confirme, o simplemente dejarlos vacíos para que este
                sea el que los complemente')
            </div>
        </div>
    </div>
@stop
{{-- /Encabezado --}}

{{-- El contenido de la página --}}
@section('content')
    <div id="app" class="row no-margin col-md-12">

        {{-- ----------------------------------------------------
            -> Requerimientos para el proceso
            -----------------------------------------------------
            - Mensajes (aviso, info, warning, etc)
            - Validaciones futuras
            - Firmantes o usuarios a solicitar los datos
            - Modals de configuracion
            ---------------------------------------------------- --}}

        {{-- Mensajes de la aplicación --}}
        <div id="messages" {{-- mensajes de éxito --}} data-process-complete="@lang('Se ha creado con éxito las validaciones')"
            data-success-template="@lang('Plantilla cargada con éxito')"
            data-load-data-verification-complete="@lang('La verificación de datos previa ha sido cargada')"
            data-success-min-max-character-type-validation="@lang('Validacion asignada con éxito')" {{-- mensajes de error --}}
            data-text-is-empty="@lang('El campo nombre no debe estar vacío si esta seleccionado')"
            data-text-is-all-empty="@lang('El campo nombre no debe estar vacío')"
            data-checks-is-not-checked="@lang('Ningun campo esta seleccionado, debe seleccionar al menos 1')"
            data-sign-is-empty="@lang('No hay firmantes a quien asignarles formulario de datos')"
            data-form-is-empty="@lang('Debe crear o usar alguna plantilla de formulario de datos primero')"
            data-form-data-validate-is-empty="@lang('No ha asignado ningún formulario a algún firmante o usuario')"
            data-validate-min-max="@lang('El valor máximo no puede ser menor o igual al mínimo, debe ser mayor')"
            {{-- Mensajes de alerta --}}
            data-info-min-max-character-type-validation="@lang('Aviso! No se validaron los campos, debe dar clic en el botón VALIDAR si desea que sea validado')">
        </div>
        {{-- /Mensajes de la aplicación --}}

        {{-- Validaciones para el documento --}}
        <div id="documentValidations" data-document-validations="@json($validations)"></div>
        {{-- /Validaciones para el documento --}}

        {{-- data de lso firmantes --}}
        <div id="signers" data-signers="@json($signers)"></div>
        {{-- /data de lso firmantes --}}

        {{-- Modal para verificar si quedan datos por validar en la configuracion de campos --}}
        @include('dashboard.partials.form-data.partials.row-inputs.row-verification-validation-modal')
        {{-- /Modal para verificar si quedan datos por validar en la configuracion de campos --}}

        {{-- recuperar o no la fila de campos eliminada --}}
        @include('dashboard.partials.form-data.partials.row-inputs.row-modal-retrieve-row')
        {{-- /recuperar o no la fila de campos eliminada --}}

        {{-- Archivo que contiene las traducciones automaticas para ser renderizadas en la vista --}}
        @include('dashboard.partials.form-data.partials.render-text-on-view')
        {{-- /Archivo que contiene las traducciones automaticas para ser renderizadas en la vista --}}

        {{-- ----------------------------------------------------
            -> Proceso y configuracion del formulario
            -----------------------------------------------------
            - Botones de accion
            - Plantillas predterminadas
            - Plantillas personales
            - Formularios y datos ya asignados
            - Construccion y configuracion de la asignacion
            ---------------------------------------------------- --}}

        {{-- Los botones de Acción para continuar o cancelar el proceso --}}
        @include('dashboard.partials.form-data.partials.actions-button', [
        'document' => $document, // el documento
        ])
        {{-- /Los botones de Acción para continuar o cancelar el proceso --}}

        {{-- Seccion donde se carga la las plantillas del sistema y las que posee el usuario --}}
        @include('dashboard.partials.form-data.partials.section-layouts-form-data', [
        'appFormTemplates' => $appFormTemplates, // Data de las plantillas del sistema
        'userFormTemplates' => $userFormTemplates, // Data de las plantillas del usuario
        ])
        {{-- / Seccion donde se carga la las plantillas del sistema y las que posee el usuario --}}

        {{-- Seccion donde se carga o se crea la plantilla desde cero --}}
        @include('dashboard.partials.form-data.partials.load-form-formdata', [
        'signers' => $signers, // firmantes
        ])
        {{-- /Seccion donde se carga o se crea la plantilla desde cero --}}

        {{-- Datos finales donde se muestra el nombre del firmante
			como ayuda visual para el usuario de saber los firmantes que ya le ha asignado
			u formulario de datos validado --}}
        @include('dashboard.partials.form-data.partials.view-assignment-data-to-signer')
        {{-- /cierre --}}

        {{-- Los botones de Acción para continuar o cancelar el proceso --}}
        @include('dashboard.partials.form-data.partials.actions-button', [
        'document' => $document, // el documento
        ])
        {{-- /Los botones de Acción para continuar o cancelar el proceso --}}
    </div>
@stop
{{-- /Cuerpo de la pagina --}}

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/documents/form-data.js')"></script>
@stop
