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
        @lang('Estos son sus archivos')
        <div class="page-title-subheading">
            @lang('Seleccione el o los usuarios con los que desea compartir los archivos')
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
    <div v-cloak id="app" class="row no-margin col-md-12">

        {{-- Las modales necesarias --}}
        @include('dashboard.modals.files.files-list-on-sharing')
        {{-- /Las modales necesarias --}}

        {{-- El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}
        <input type="hidden" id="max-users" value="{{$user->subscription->plan->signers}}"/>
        {{--/El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}

        {{-- Los botones con las acciones --}}
        <div class="col-md-12 mb-4">
            <div class="input-group" role="group">
                <a :class="!users.length ? 'disabled' : ''" href="#" @click.prevent="share"
                   data-request-save-file-sharing="@route('dashboard.save.file.sharing')"
                   data-request-list-file-sharings="@route('dashboard.files.sharing')"
                   class="btn btn-lg btn-success mr-1">
                    @lang('Continuar')
                </a>
                <a href="@route('dashboard.file.list')" class="btn btn-lg btn-danger">
                    @lang('Cancelar')
                </a>
            </div>
        </div>
        {{-- /Los botones con las acciones --}}

        {{-- Título y descripción para la compartición --}}
        <div class="col-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Título y descripción')</h5>
                    <div class="mb-2">@lang('Indique un título y una descripción para los ficheros que se compartirán')</div>
                    <div class="form-row">
                        <div class="col-4">
                            <div class="position-relative form-group">
                                <label for="title">@lang('Título')</label>
                                <input v-model="title" name="title" id="title" type="text" class="form-control"
                                       maxlength="100" autofocus/>
                            </div>
                        </div>

                        <div class="col-8">
                            <div class="position-relative form-group">
                                <label for="description" class="">@lang('Descripción')</label>
                                <input v-model="description" name="description" id="description" type="text"
                                       class="form-control" maxlength="255"/>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- /Título y descripción para la compartición --}}

        {{-- Lista de archivos que se van a compartir --}}
        @include('includes.file_list', ['files' => $files])
        {{--/ Lista de archivos que se van a compartir --}}

        {{-- Selección de los contactos desde la lista de contactos guardados --}}
        <div class="col-md-6 col-s-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Seleccionar Contacto')</h5>
                    <div class="mb-2">@lang('Seleccione un contacto y se añadirá a la lista')</div>
                    @include('dashboard.partials.contacts-table')
                </div>
            </div>
        </div>
        {{--/Selección de los contactos desde la lista de contactos guardados --}}

        {{-- Añadir un nuevo usuario a la lista --}}
        <div class="col-md-6 col-s-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Añadir un nuevo Usuario')</h5>
                    <div class="mb-2">@lang('Si la dirección de correo o el teléfono está en la lista de contactos se completará la información')</div>

                    <form id="form" @submit.prevent="addNewUser" method="post" action="@route('dashboard.contact.save')"
                          data-message-success="@lang('Se ha guardado el contacto con éxito')"
                          data-message-failed="@lang('No se ha podido guardar el contacto')">

                        {{-- La lista de archivos que se comparten --}}
                        <input type="hidden" id="files" value="{{$files->toJson()}}"/>
                        {{--/La lista de archivos que se comparten --}}

                        <div class="form-row">
                            <div class="col-md-8">
                                <div class="position-relative form-group">
                                    <label for="email">@lang('Dirección de Correo')</label>
                                    <input v-model="email" name="email" id="email" type="text" class="form-control"
                                           @blur="findContactByEmail(email)"
                                           :class="email ? (emailIsInvalid ? 'is-invalid': 'is-valid') : ''"
                                           maxlength="100" autofocus
                                           data-request-email="@route('dashboard.contact.find.email')"
                                           data-message-email-exists="@lang('La dirección de correo ya fue añadida a la lista de firmantes')"/>
                                    <div class="invalid-feedback">@{{ error ||
                                        '@lang('La dirección de correo no es válida')' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="phone" class="">@lang('Teléfono')</label>
                                    <input v-model="phone" name="phone" id="phone" type="text" class="form-control"
                                           maxlength="50"
                                           @blur="findContactByPhone(phone)"
                                           data-request-phone="@route('dashboard.contact.find.phone')"/>
                                </div>
                            </div>

                        </div>

                        <div class="form-row">

                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="name">@lang('Nombre')</label>
                                    <input v-model="name" name="name" id="name" type="text" class="form-control"
                                           maxlength="100"/>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="position-relative form-group">
                                    <label for="lastname" class="">@lang('Apellidos')</label>
                                    <input v-model="lastname" name="lastname" id="lastname" type="text"
                                           class="form-control" maxlength="255"/>
                                </div>
                            </div>

                        </div>

                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="dni" class="">@lang('DNI')</label>
                                    <input v-model="dni" name="dni" id="dni" type="text" class="form-control"
                                           maxlength="20"/>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="company" class="">@lang('Compañía')</label>
                                    <input v-model="company" name="company" id="company" type="text"
                                           class="form-control" maxlength="255"/>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="position" class="">@lang('Cargo')</label>
                                    <input v-model="position" name="position" id="position" type="text"
                                           class="form-control" maxlength="100"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-row text-right">
                            <label class="check-container mr-2">
                                <span class="text bold">@lang('Añadir a la lista de contactos')</span>
                                <input v-model="addUserToContactList" class="form-control" type="checkbox"/>
                                <span class="square-check"></span>
                            </label>
                        </div>

                        <div class="text-right">
                            <button :disabled="whenInvalidData" class="mt-2 btn btn-success">@lang('Añadir')</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- Tabla con la lista de usuarios seleccionados --}}
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-body"><h5 class="card-title">@lang('Lista de Usuarios')</h5>
                    <div class="table-responsive">
                        <table id="users" class="mb-0 table table-striped">
                            <thead>
                            <tr>
                                <th>@lang('Apellidos')</th>
                                <th>@lang('Nombre')</th>
                                <th>@lang('Dirección de Correo')</th>
                                <th>@lang('Teléfono')</th>
                                <th>@lang('DNI')</th>
                                <th>@lang('Compañía')</th>
                                <th>@lang('Cargo')</th>
                                <th></th>
                            </tr>
                            </thead>

                            {{-- La lista de Usuarios --}}
                            <tbody v-if="users.length">
                            <tr v-for="(user,index) in users" :key="index">
                                <td data-label="@lang('Apellidos')">@{{ user.lastname }}</td>
                                <td data-label="@lang('Nombre')">@{{ user.name }}</td>
                                <td data-label="@lang('Dirección de Correo')">@{{ user.email }}</td>
                                <td data-label="@lang('Teléfono')">@{{ user.phone }}</td>
                                <td data-label="@lang('DNI')">@{{ user.dni }}</td>
                                <td data-label="@lang('Compañía')">@{{ user.company }}</td>
                                <td data-label="@lang('Cargo')">@{{ user.position }}</td>
                                <td class="text-center">
                                    <button @click.prevent="removeUser(index)" class="btn btn-danger">
                                        @lang('Eliminar')
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                            {{--/La lista de Usuarios --}}

                            <tbody v-else>
                            <tr>
                                <td colspan="8" class="text-center">
                                    @lang('No hay registros')
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{--/Tabla con la lista de usuarios seleccionados --}}

        {{-- Los botones con las acciones --}}
        <div class="col-md-12 mb-4">
            <div class="btn-group" role="group">
                <a :class="!users.length ? 'disabled' : ''" href="#" @click.prevent="share"
                   class="btn btn-lg btn-success mr-1">
                    @lang('Continuar')
                </a>
                <a href="@route('dashboard.file.list', ['count'=>10])" class="btn btn-lg btn-danger">
                    @lang('Cancelar')
                </a>
            </div>
        </div>
        {{-- /Los botones con las acciones --}}

    </div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/files/file-share.js')"></script>
@stop