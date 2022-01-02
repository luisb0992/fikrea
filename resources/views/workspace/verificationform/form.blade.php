@extends('workspace.layouts.main')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- Css Personalizado --}}
@section('css')
    <link rel="stylesheet" href="@mix('assets/css/workspace/form-data.css')" />
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Debe rellenar o modificar los campos que se muestran en el formulario a continuación')
        <div class="page-title-subheading">
            @lang('Asegúrese de cumplir con las validaciones requeridas para cada campo')
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')
    <div v-cloak id="app" class="col-md-12">

        {{-- El registro de la visita del usuario --}}
        <div id="visit" data-visit="@json($visit)"></div>
        {{-- /El registro de la visita del usuario --}}

        {{-- Mensajes de error --}}
        <div id="errors" data-input-empty="@lang('Todos los campos del tipo descripción son requeridos, debe llenarlos')"
            data-input-one-empty="@lang('El campo es requerido')"
            data-input-min="@lang('No cumple el mínimo de caracteres aceptados')"
            data-input-max="@lang('Límite de caracteres permitidos alcanzado')"
            data-input-character-type="@lang('Ha insertado un tipo de carácter no permitido')">
        </div>
        {{-- /Mensajes de error --}}

        {{-- Mensajes de exito --}}
        <div id="success" data-success="@lang('Proceso completado con éxito')"></div>
        {{-- /Mensajes de exito --}}

        {{--  link que almacena el proceso  --}}
        <div id="urlIsDone" data-verification-is-done="@route('workspace.verificationform.isdone', ['token' => $token])"></div>
        {{--  /link que almacena el proceso  --}}

        {{-- Los botones de Acción --}}
        <div class="col-md-12 mb-4">

            <div class="btn-group" role="group">
                <a href="#" @click.prevent="saveFormdata" class="btn btn-lg btn-success mr-1"
                    data-save-request="@route('workspace.verificationform.save', ['token' => $token])"
                    data-redirect-request="@route('workspace.home', ['token' => $token])">
                    @lang('Finalizar')
                </a>

                <a href="@route('workspace.home', ['token' => $token])" class="btn btn-lg btn-danger">@lang('Atrás')</a>

                {{--  modal para agregar o ver un comentario  --}}
                @include('common.comments.button-add-comment-modal', [
                    'process' => $verificationForm,
                    'nameProcess' => config('validations.independent-validations.dataCertification'),
                ])
            </div>

        </div>
        {{-- /Los botones de Acción --}}

        {{-- Leyenda --}}
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <div class="text-justify text-muted">
                    <div class="col">
                        <p class="alert-heading">
                            @lang('A continuación usted verá un formulario de datos, donde se solicita verificar o
                            cumplimentar en caso de venir <b>VACÍO DE INFORMACIÓN</b>')
                        </p>
                        <hr>
                        <p>
                            1 - @lang('Cada bloque o ítem del formulario pertenece a un dato en concreto que se le solicita y
                            que se le explica en la columna <b>NOMBRE</b>')
                        </p>
                        <p>
                            2 - @lang('A continuación visualiza un campo vacío o con información en su
                            interior bajo la columna <b>DESCRIPCIÓN</b> y que es información que usted debe saber,
                            confirmar,
                            editar o cumplimentar')
                        </p>
                        <p>
                            3 - @lang('<b :class>¡CUIDADO!</b> El apartado <b>PARÁMETROS SOBRE LA DESCRIPCIÓN</b>, le explica
                            las reglas definidas por el solicitante para dicha información, donde puede solicitar que
                            tenga un tamaño o un tipo de carácter específico', ['class' => 'class="text-danger"'])
                        </p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        {{-- /leyenda --}}

        {{-- Formulario de datos --}}
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">
                        @lang('Verificación de datos')
                    </h5>

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Nombre')</th>
                                    <th>@lang('Descripción')</th>
                                    <th>@lang('Parámetros sobre la descripción')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($verificationForm->fieldsRow as $key => $input)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $input->field_name }}</td>
                                        <td>
                                            <input @keyup="validateFieldText({{ $input->id }})" type="text"
                                                name="field_text[]" value="{{ $input->field_text }}" class="form-control"
                                                id="input-field_text-{{ $input->id }}" data-id="{{ $input->id }}"
                                                placeholder="@lang('Inserte la infomración que se le solicita para el campo :field', ['field' => $input->field_name])">
                                            <div id="msj-field_text-{{ $input->id }}"></div>
                                        </td>
                                        <td>
                                            @include('workspace.partials.formdata.button-validations')
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- /Formulario de datos --}}

        {{-- Los botones de Acción --}}
        <div class="col-md-12 mb-4">

            <div class="btn-group" role="group">
                <a href="#" @click.prevent="saveFormdata" class="btn btn-lg btn-success mr-1"
                    data-save-request="@route('workspace.verificationform.save', ['token' => $token])"
                    data-redirect-request="@route('workspace.home', ['token' => $token])">
                    @lang('Finalizar')
                </a>

                <a href="@route('workspace.home', ['token' => $token])" class="btn btn-lg btn-danger">@lang('Atrás')</a>

                {{--  modal para agregar o ver un comentario  --}}
                @include('common.comments.button-add-comment-modal', [
                    'process' => $verificationForm,
                    'nameProcess' => config('validations.independent-validations.dataCertification'),
                ])
            </div>

        </div>
        {{-- /Los botones de Acción --}}

    </div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/workspace/form-data.js')"></script>
@stop
