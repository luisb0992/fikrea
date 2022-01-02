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
    @lang('Seleccionar los firmantes')
    <div class="page-title-subheading">
       @lang('Elija las personas que firmarán el documento').
       @lang('Puede seleccionarlas desde la lista de contactos o añadir un firmante nuevo').
       @lang('Si lo va a firmar sólo usted puede pasar al siguiente paso').
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="row no-margin col-md-12">
    {{-- Modales necesarias --}}
    @include('dashboard.modals.documents.link-sidebar-modal')
    @include('dashboard.modals.documents.cancel-signer-modal')
    {{-- /Modales necesarias --}}

    {{-- Mensajes de la aplicación --}}
    <div id="messages"
        data-how-to-add-new-signers="@lang('Puede seleccionar más firmantes desde la lista de contactos o añadir uno nuevo')">
    </div>
    {{--/Mensajes de la aplicación --}}

    {{-- Data para Vue JS --}}
    <div id="data"
        data-document="{{$document->id}}">
    </div>
    {{-- Data para Vue JS --}}

    {{-- Los botones con las acciones --}}
    <div class="col-md-12 mb-4">
        <div class="input-group" role="group">
            <a href="@route('dashboard.document.list')" @click.prevent="saveSigners" class="btn btn-lg btn-success mr-1">
                <span v-if="signers.length > 0">
                    @lang('Continuar')
                </span>
                <span v-else>
                    @lang('Solo Firmo Yo')
                </span>
            </a>
            <a href="" @click.prevent="cancelSignerModal" class="btn btn-lg btn-danger">@lang('Cancelar')</a>
        </div>
    </div>
    {{-- /Los botones con las acciones --}}

    {{-- El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}
    <input type="hidden" id="max-signers" value="{{$user->subscription->plan->signers}}" />
    {{--/El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}

    <div class="row">

        {{-- Añadir Firmante desde Mis Contactos --}}
        <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Seleccionar Contacto')</h5>
                    <div class="mb-2">@lang('Seleccione un contacto y se añadirá a la lista').
                    </div>
                    @include('dashboard.partials.contacts-table')
                </div>
            </div>
        </div>
        {{--/ Añadir Firmante desde Mis Contactos --}}

        {{-- Crear un Nuevo Firmante --}}
        <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Añadir un nuevo Firmante')</h5>
                    <div class="mb-2">@lang('Si la dirección de correo o el teléfono está en la lista de contactos se completará la información')</div>
        
                    <form id="form" @submit.prevent="addNewSigner" method="post" action="@route('dashboard.contact.save')"
                        data-message-success="@lang('Se ha guardado el contacto con éxito')"
                        data-message-failed="@lang('No se ha podido guardar el contacto')">
                         
                        {{-- Lista de rutas implicadas

                            dashboard.document.get.signers  : Obtener los firmantes
                            dashboard.document.save.signers : Guardar los firmantes
                            dashboard.document.validations  : Vista de validaciones
                                 
                        --}}
                        <input type="hidden" id="save-signers-request" value="@route('dashboard.document.save.signers', ['id' => $document->id])" />
                        <input type="hidden" id="set-validations-request" value="@route('dashboard.document.validations', ['id' => $document->id])" />
                        
                        <div class="form-row">
                            <div class="col-md-8">
                                <div class="position-relative form-group">
                                    <label for="email">@lang('Dirección de Correo')</label>
                                    <input v-model="email" name="email" id="email" list="contacts" class="form-control"
                                        @blur="findContactByEmail(email)"
                                        :class="email ? (emailIsInvalid ? 'is-invalid': 'is-valid') : ''" 
                                        maxlength="100" autofocus 
                                        data-request-email="@route('dashboard.contact.find.email')" 
                                        data-message-email-exists="@lang('La dirección de correo ya fue añadida a la lista de firmantes')" />
                                        <datalist id="contacts">
                                            @foreach ($user->contacts as $contact)
                                            <option value="{{$contact->email}}"></option>
                                            @endforeach
                                        </datalist>
                                    <div class="invalid-feedback">@{{ error || '@lang('La dirección de correo no es válida')' }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="phone" class="">@lang('Teléfono')</label>
                                    <input v-model="phone" name="phone" id="phone" type="text" class="form-control" maxlength="50"
                                        @blur="findContactByPhone(phone)"
                                        data-request-phone="@route('dashboard.contact.find.phone')" />
                                </div>
                            </div>

                        </div>
        
                        <div class="form-row">
        
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="name">@lang('Nombre')</label> 
                                    <input v-model="name" name="name" id="name" type="text" class="form-control" maxlength="100" />
                                </div>
                            </div>
        
                            <div class="col-md-8">
                                <div class="position-relative form-group">
                                    <label for="lastname" class="">@lang('Apellidos')</label>
                                    <input v-model="lastname" name="lastname" id="lastname" type="text" class="form-control" maxlength="255" />
                                </div>
                            </div>
        
                        </div>
        
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="dni" class="">@lang('DNI')</label>
                                    <input v-model="dni" name="dni" id="dni" type="text" class="form-control" maxlength="20" />
                                </div>
                            </div>
        
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="company" class="">@lang('Compañía')</label>
                                    <input v-model="company" name="company" id="company" type="text" class="form-control" maxlength="255" />
                                </div>
                            </div>
        
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="position" class="">@lang('Cargo')</label>
                                    <input v-model="position" name="position" id="position" type="text" class="form-control" maxlength="100" />
                                </div>
                            </div>
                        </div>
                        <div class="form-row text-right">
                            <label class="check-container mr-2">
                                <span class="text bold">@lang('Añadir a la lista de contactos')</span>
                                <input v-model="addSignerToContactList" class="form-control" type="checkbox" />
                                <span class="square-check"></span>
                            </label>
                        </div>
        
                        <div class="text-right">
                            <button :disabled="whenInvalidData || maxSignerExceed" class="mt-2 btn btn-success">@lang('Añadir')</button>
                        </div>
        
                    </form>
        
        
                </div>
            </div>
        </div>
        {{--/ Crear un Nuevo Firmante --}}

    </div>

    <div class="row">
        {{-- Lista de Firmantes del Documento --}}
        <div class="col-md-12">
            @include('dashboard.partials.signers-table')
        </div>
        {{--/Lista de Firmantes del Documento --}}
    </div>
    
    {{-- Los botones con las acciones --}}
    <div class="col-md-12 mb-4">
        <div class="btn-group" role="group">
            <a href="@route('dashboard.document.list')" @click.prevent="saveSigners" class="btn btn-lg btn-success mr-1">
                <span v-if="signers.length > 0">
                    @lang('Continuar')
                </span>
                <span v-else>
                    @lang('Solo Firmo Yo')
                </span>
            </a>
            <a href="" @click.prevent="cancelSignerModal" class="btn btn-lg btn-danger">@lang('Cancelar')</a>
        </div>
    </div>
    {{-- /Los botones con las acciones --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="/assets/js/dashboard/documents/signers.js"></script>
@stop