{{-- Datos de Facturación --}}
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">@lang('Datos de Facturación')</h5>

        <div class="form-group">
            <label class="check-container">
                <input id="buttonCopyProfileToCompany" type="checkbox" @change="copyProfileToCompany" />
                <span class="square-check"></span>
                <span class="ml-4 pl-1">@lang('Quiere copiar los datos de su perfil')</span>
            </label>
        </div>

        <div class="form-row">
            {{-- Empresa/Razón Social --}}
            <div class="col-md-8">
                <div class="position-relative form-group">
                    <label for="companyName" class="">@lang('Empresa/Razón Social')</label>
                    <input value="@exists($user->billing->name)" name="companyName" id="companyName" type="text"
                        class="form-control" form="form-user-data" />
                </div>
            </div>
            {{-- /Empresa/Razón Social --}}

            {{-- CIF-NIF --}}
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="companyCif" class="">@lang('CIF-NIF')</label>
                    <input value="@exists($user->billing->cif)" name="companyCif" id="companyCif" type="text"
                        maxlength="50" class="form-control" form="form-user-data" />
                </div>
            </div>
            {{-- /CIF-NIF --}}

        </div>

        {{-- Dirección de Correo para Facturación --}}
        <div class="form-row">
            <div class="col-md-12">
                <div class="position-relative form-group">
                    <label for="companyEmail" class="">@lang('Dirección de Correo para Facturación')</label>
                    <input value="@exists($user->billing->email)" name="companyEmail" id="companyEmail" type="email"
                        class="form-control" form="form-user-data" />
                </div>
            </div>
        </div>
        {{-- /Dirección de Correo para Facturación --}}

        {{-- Dirección Postal --}}
        <div class="form-row">
            <div class="col-md-12">
                <div class="position-relative form-group">
                    <label for="companyAddress" class="">@lang('Dirección Postal')</label>
                    <input value="@exists($user->billing->address)" name="companyAddress" id="companyAddress"
                        type="text" class="form-control" form="form-user-data" />
                </div>
            </div>
        </div>
        {{-- /Dirección Postal --}}

        <div class="form-row">

            {{-- Código Postal --}}
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="companyCodePostal" class="">@lang('Código Postal')</label>
                    <input value="@exists($user->billing->code_postal)" name="companyCodePostal" id="companyCodePostal"
                        maxlength="6" type="text" class="form-control" form="form-user-data" @keypress="soloNumeros" />
                </div>
            </div>
            {{-- /Código Postal --}}

            {{-- Teléfono --}}
            <div class="col-md-8">
                <label for="phone" class="">@lang('Teléfono')</label>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {{-- Componente Select2
                                @link https://github.com/godbasin/vue-select2

                                Para las opciones originales del Select2@hasSection('
                                @link https://select2.org/configuration/options-api') --}}
                            <Select2 id="company-select-country" class="select2" :options="codeCountries"
                                :settings="{tags: true, templateResult: changeTemplate, templateSelection: changeTemplate}"
                                v-model="company_dial_code" data-bdial="@exists($user->billing->dial_code)"
                                @change="setCompanyDialCode" />

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <input value="@exists($user->billing->phone)" name="companyPhone" id="companyPhone"
                                type="text" class="form-control" @keypress="soloNumeros" form="form-user-data" />
                        </div>
                    </div>
                </div>
            </div>
            {{-- /Teléfono --}}

        </div>

        <div class="form-row">

            {{-- Localidad --}}
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="companyCity" class="">@lang('Localidad')</label>
                    <input value="@exists($user->billing->city)" name="companyCity" id="companyCity" type="text"
                        class="form-control" @keypress="soloLetras" form="form-user-data" />
                </div>
            </div>

            {{-- Provincia --}}
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="companyProvince" class="">@lang('Provincia')</label>
                    <input value="@exists($user->billing->province)" name="companyProvince" id="companyProvince"
                        type="text" class="form-control" @keypress="soloLetras" form="form-user-data" />
                </div>
            </div>
            {{-- /Provincia --}}

            {{-- País --}}
            <div class="col-md-4">
                <div class="position-relative form-group">
                    <label for="companyCountry" class="">@lang('País')</label>

                    <Select2 value="" class="select2" :options="optionsCountries" v-model="companyCountry"
                        form="form-user-data" />

                </div>
            </div>
            {{-- Dato país de la compañía que se envía, relacionado con el select del país --}}
            <div>
                <input type="hidden" name="companyCountry" id="companyCountry" v-model="companyCountry"
                    form="form-user-data">
            </div>
            {{-- /País --}}
        </div>

        <hr>

        {{-- compartir por copia de link --}}
        <div class="btn-group" role="group">
            <button id="btnGroupDropShare" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                @lang('Compartir vía url')
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDropShare">
                <a class="dropdown-item text-primary" href="#" @click.prevent="shareViaLink" data-signature=true>
                    <i class="fas fa-signature mr-1"></i> @lang('Incluir firma digital')
                </a>
                <a class="dropdown-item text-info" href="#" @click.prevent="shareViaLink"
                    data-url="@route('dashboard.profile.shareBillingForLink')">
                    <i class="fas fa-not-equal mr-1"></i> @lang('NO Incluir firma digital')
                </a>
            </div>
        </div>

        {{-- compartir a un correo --}}
        <div class="btn-group" role="group">
            <button id="btnGroupDropShare" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                @lang('Compartir vía Email')
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDropShare">
                <a class="dropdown-item text-primary" href="#" @click.prevent="shareBillingData" data-signature=true>
                    <i class="fas fa-signature mr-1"></i> @lang('Incluir firma digital')
                </a>
                <a class="dropdown-item text-info" href="#" @click.prevent="shareBillingData">
                    <i class="fas fa-not-equal mr-1"></i> @lang('NO Incluir firma digital')
                </a>
            </div>
        </div>

        {{-- Mensajes de la aplicación --}}
        <div id="messageShare" data-share-title="@config('app.name')"
            data-share-text="@lang('Se ha copiado la dirección de descarga del perfil de facturación')"
            data-not-available="@lang('No se puede copiar el link al portapapeles')"
            data-blocked="@lang('Ha sido bloqueada la copia de su link, por favor intente en otro navegador')"
            data-not-support="@lang('No se pudo copiar, esta opción no está disponible bajo tu navegador')">
        </div>
        {{-- /Mensajes de la aplicación --}}

        {{-- input por si la copia del link no funciona esta se genera mediante otro metodo --}}
        <button class="d-none" id="btnCopiar" @click.prevent="forceCopy"></button>
        <span class="d-none" id="sharingUrl"></span>

    </div>
</div>
{{-- /Datos de Facturación --}}
