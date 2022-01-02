@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
@stop

{{-- El encabezado con la ayuda --}}
@section('help')
    <div>
        @lang('Seleccionar los usuarios')
        <div class="page-title-subheading">
            @lang('Elija las personas a las que van a intervenir en el evento').
            @lang('Puede agregarlas de la lista de contactos o crear una nueva').
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div v-cloak id="app" class="row no-margin col-md-12">

        {{-- Modales necesarias --}}
        @include('dashboard.modals.documents.link-sidebar-modal')
        @include('dashboard.events.partials.census.modal-cancel-census', [
        'route' => route('dashboard.event.list')
        ])
        {{-- /Modales necesarias --}}

        {{-- Los botones con las acciones --}}
        @include('dashboard.events.partials.census.action-buttons')
        {{-- /Los botones con las acciones --}}

        {{-- Mensajes de la aplicación --}}
        <div id="messages" data-event-success="@lang('El censo se ha guardado con éxito')"
            data-server-error="@lang('Ha ocurrido un error, intente más tarde')">
        </div>
        {{-- /Mensajes de la aplicación --}}

        {{-- datos del evento --}}
        <div class="col-md-12 col-sm-6">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title text-info">{{ $event->title }}</h5>
                    <div class="mb-2">{{ $event->formatdescription }}</div>

                    <div class="text-primary">
                        @include('dashboard.events.partials.event-types', [
                        'type' => $event->type
                        ])
                    </div>
                </div>
            </div>
        </div>

        {{-- Añadir Firmante desde Mis Contactos --}}
        <div class="col-md-6 col-sm-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Seleccionar Contacto')</h5>
                    <div class="mb-2">@lang('Seleccione un contacto y se añadirá a la lista')</div>
                    @include('dashboard.partials.contacts-table')
                </div>
            </div>
        </div>
        {{-- / Añadir Firmante desde Mis Contactos --}}

        {{-- Crear un nuevo usuario --}}
        <div class="col-md-6 col-sm-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Añadir un nuevo usuario')</h5>
                    <div class="mb-2">@lang('Si la dirección de correo o el teléfono está en la lista de contactos se
                        completará la información')</div>

                    <form id="form" @submit.prevent="addNewSigner" method="post" action="@route('dashboard.contact.save')"
                        data-message-success="@lang('Se ha guardado el contacto con éxito')"
                        data-message-failed="@lang('No se ha podido guardar el contacto')">

                        <div class="form-row">
                            <div class="col-md-8">
                                <div class="position-relative form-group">
                                    <label for="email">@lang('Dirección de Correo')</label>
                                    <input v-model="email" name="email" id="email" list="contacts" class="form-control"
                                        :class="email ? (emailIsInvalid ? 'is-invalid': 'is-valid') : ''"
                                        @blur="findContactByEmail(email)" maxlength="100" autofocus
                                        data-request-email="@route('dashboard.contact.find.email')"
                                        data-message-email-exists="@lang('La dirección de correo ya fue añadida a la lista de firmantes')" />
                                    <datalist id="contacts">
                                        @foreach ($user->contacts as $contact)
                                            <option value="{{ $contact->email }}"></option>
                                        @endforeach
                                    </datalist>
                                    <div class="invalid-feedback">
                                        @{{ error || "@lang('La dirección de correo no es válida')" }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="phone" class="">@lang('Teléfono')</label>
                                    <input v-model="phone" name="phone" id="phone" type="text" class="form-control"
                                        maxlength="50" @blur="findContactByPhone(phone)"
                                        data-request-phone="@route('dashboard.contact.find.phone')" />
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="name">@lang('Nombre')</label>
                                    <input v-model="name" name="name" id="name" type="text" class="form-control"
                                        maxlength="100" />
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="position-relative form-group">
                                    <label for="lastname" class="">@lang('Apellidos')</label>
                                    <input v-model="lastname" name="lastname" id="lastname" type="text" class="form-control"
                                        maxlength="255" />
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="dni" class="">@lang('DNI')</label>
                                    <input v-model="dni" name="dni" id="dni" type="text" class="form-control"
                                        maxlength="20" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="company" class="">@lang('Compañía')</label>
                                    <input v-model="company" name="company" id="company" type="text" class="form-control"
                                        maxlength="255" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="position" class="">@lang('Cargo')</label>
                                    <input v-model="position" name="position" id="position" type="text" class="form-control"
                                        maxlength="100" />
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="address" class="">@lang('Dirección')</label>
                                    <textarea v-model="address" name="address" id="address" class="form-control"
                                        rows="4"></textarea>
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
                            <button :disabled="whenInvalidData || maxSignerExceed"
                                class="mt-2 btn btn-success">@lang('Añadir')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- / Crear un Nuevo usuario --}}

        {{-- Lista de usuarios asignados --}}
        <div class="col-md-12">
            @include('dashboard.events.partials.census.census-table', [
            'users' => $event->participants
            ])
        </div>
        {{-- /Lista de usuarios asignados --}}

        {{-- Los botones con las acciones --}}
        @include('dashboard.events.partials.census.action-buttons')
        {{-- /Los botones con las acciones --}}

    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/events/census.js')"></script>
@stop
