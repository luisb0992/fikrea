{{-- Vista para crear o editar una solicitud de documentos --}}
@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title')
    @lang('certificación de datos')
@stop

{{-- Css Personalizado --}}
@section('css')

@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @if ($verificationForm)
            @lang('Editar un formulario de datos')
        @else
            @lang('Crear un formulario de datos')
        @endif
        <div class="page-title-subheading">
            <div>
                @lang('En esta herramienta encontrará unas plantillas predeterminadas donde le sugerimos una serie de campos
                que puede rellenar para que la persona se los valide o confirme, o simplemente dejarlos vacios para que este
                sea el que los complemente')
            </div>
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div id="app" class="row no-margin col-md-12">

        {{-- ----------------------------------------------------
        -> Requerimientos para la creacion de la verificación
        ---------------------------------------------------------
        - Mensajes (aviso, info, warning, etc)
        - Validaciones futuras
        - Firmantes o usuarios a solicitar los datos
        - Modals de configuracion

        Nota: algunos "includes" comparten la integracion con el proceso en el documento
        estos estan marcados para el programador como "COMPARTE PROCESOS"
        ---------------------------------------------------- --}}

        {{-- Mensajes de la aplicación --}}
        <div id="messages" data-process-complete="@lang('Se ha creado con éxito la verificación de datos')"
            data-success-template="@lang('Plantilla cargada con éxito')"
            data-load-data-verification-complete="@lang('La verificación de datos previa ha sido cargada')"
            data-success-min-max-character-type-validation="@lang('Validación asignada con éxito')"
            data-text-is-empty="@lang('El campo nombre no debe estar vacío si está seleccionado')"
            data-text-is-all-empty="@lang('El campo nombre no debe estar vacío')"
            data-checks-is-not-checked="@lang('Ningún campo está seleccionado, debe seleccionar al menos 1')"
            data-form-is-empty="@lang('Debe crear o usar alguna plantilla de formulario de datos primero')"
            data-form-data-validate-is-empty="@lang('No ha creado ningún formulario')"
            data-validate-min-max="@lang('El valor máximo no puede ser menor o igual al mínimo, debe ser mayor')"
            data-info-min-max-character-type-validation="@lang('Aviso! No se validaron los campos, debe dar clic en el botón VALIDAR si desea que sea validado')">
        </div>
        {{-- /Mensajes de la aplicación --}}

        {{-- Modal para verificar si quedan datos por validar en la configuracion de campos --}}
        @include('dashboard.verificationform.partials.row-verification-validation-modal')
        {{-- /Modal para verificar si quedan datos por validar en la configuracion de campos --}}

        {{-- recuperar o no la fila de campos eliminada | *COMPARTE PROCESOS* --}}
        @include('dashboard.partials.form-data.partials.row-inputs.row-modal-retrieve-row')
        {{-- /recuperar o no la fila de campos eliminada --}}

        {{-- Archivo que contiene las traducciones automaticas para ser renderizadas en la vista | *COMPARTE PROCESOS* --}}
        @include('dashboard.partials.form-data.partials.render-text-on-view')
        {{-- /Archivo que contiene las traducciones automaticas para ser renderizadas en la vista --}}

        {{-- ----------------------------------------------------
        -> Proceso y configuracion del formulario
        ---------------------------------------------------------
        - Botones de accion
        - Plantillas predterminadas
        - Plantillas personales
        - Formularios y datos ya asignados
        - Construccion y configuracion de la asignacion

        Nota: algunos "includes" comparten la integracion con el proceso en el documento
        estos estan marcados para el programador como "COMPARTE PROCESOS"
        ------------------------------------------------------- --}}

        {{-- Los botones de Acción para continuar o cancelar el proceso --}}
        @include('dashboard.verificationform.partials.action-buttons', [
        'verificationForm' => $verificationForm, // la verificación
        ])
        {{-- /Los botones de Acción para continuar o cancelar el proceso --}}

        {{-- Seccion donde se carga la las plantillas del sistema y
            las que posee el usuario | *COMPARTE PROCESOS* --}}
        @include('dashboard.partials.form-data.partials.section-layouts-form-data', [
        'appFormTemplates' => $appFormTemplates, // Data de las plantillas del sistema
        'userFormTemplates' => $userFormTemplates, // Data de las plantillas del usuario
        ])
        {{-- / Seccion donde se carga la las plantillas del sistema y las que posee el usuario --}}

        {{-- Seccion donde se carga o se crea la plantilla desde cero --}}
        @include('dashboard.verificationform.partials.load-data-form')
        {{-- /Seccion donde se carga o se crea la plantilla desde cero --}}

        {{-- Los botones de Acción para continuar o cancelar el proceso --}}
        @include('dashboard.verificationform.partials.action-buttons', [
        'verificationForm' => $verificationForm, // el documento
        ])
        {{-- /Los botones de Acción para continuar o cancelar el proceso --}}
    </div>
@stop
{{-- /Cuerpo de la pagina --}}

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/verificationform/edit.js')"></script>
@stop
