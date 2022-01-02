{{-- Crear un Nuevo Firmante --}}
<div class="col-md-6 col-s-12">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">@lang('Añadir un nuevo Firmante')</h5>
            <div class="mb-2">@lang('Si la dirección de correo o el teléfono está en la lista de contactos se completará la información')</div>

            <form id="form" @submit.prevent="addNewSigner" method="post" action="@route('dashboard.contact.save')"
                data-message-success="@lang('Se ha guardado el contacto con éxito')"
                data-message-failed="@lang('No se ha podido guardar el contacto')">
                
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