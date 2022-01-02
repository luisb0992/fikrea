{{-- Datos Personales --}}
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">@lang('Datos Personales')</h5>
        <form id="form-user-data"
            name="form-user-data"
            method="post"
            action="@route('dashboard.profile.save')"
        >

            @csrf

            {{-- Usuario invitado (1) o registrado (0) --}}
            @guest
            <input type="hidden" id="guest" value="1" />
            @else
            <input type="hidden" id="guest" value="0" />
            @endguest

            {{-- Tipo de cuenta --}}
            <div class="form-row">
                <div class="col md-6">
                    <div>
                        <label for="type">@lang('Tipo de cuenta')</label>
                    </div>
                    <div v-show="type == 0" class="mb-2 text-info bold">
                        <i class="fas fa-exclamation-circle"></i>
                        @lang('Si usa :app en un ámbito empresarial considere cambiar ahora el tipo de cuenta', ['app' => config('app.name')])
                    </div>
                    <div class="position-relative form-group">
                        <select v-model="type" class="form-control" name="type" id="type">
                            @foreach(
                                [
                                    '0' => new \App\Enums\UserType(\App\Enums\UserType::PERSONAL_ACCOUNT), 
                                    '1' => new \App\Enums\UserType(\App\Enums\UserType::BUSSINESS_ACCOUNT),
                                ] 
                            as $value => $text)
                                @if ($value == $user->type)
                                <option selected value="{{$value}}">{{$text}}</option>
                                @else
                                <option value="{{$value}}">{{$text}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            {{-- /Tipo de cuenta --}}

            {{-- Nombre --}}
            <div class="form-row">
                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label for="name">@lang('Nombre')</label>
                        @error('name')
                        <input value="@old('name')" name="name" id="name" type="text" class="form-control is-invalid"
                        @keypress="soloLetras" autofocus />
                        <div class="invalid-feedback">{{$message}}</div>
                        @else
                        <input value="{{$user->name}}" name="name" id="name" type="text" class="form-control"
                            @keypress="soloLetras" />
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label for="lastname" class="">@lang('Apellidos')</label>
                        <input value="{{$user->lastname}}" name="lastname" id="lastname" type="text"
                            class="form-control" @keypress="soloLetras" />
                    </div>
                </div>
            </div>
            {{-- /Nombre --}}

            {{-- Dirección de Correo --}}
            <div class="form-row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label for="email" class="">@lang('Dirección de Correo')</label>
                        @auth
                            <input disabled value="{{$user->email}}" name="email" id="email" type="text"
                                class="form-control" />
                        @else
                            @error('email')
                            <input value="{{$user->email}}" name="email" id="email" type="text"
                                class="form-control is-invalid" />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                            <input value="{{$user->email}}" name="email" id="email" type="text"
                                class="form-control" />
                            @enderror
                        @endauth
                    </div>
                </div>
            </div>
            {{-- /Dirección de Correo --}}

            {{-- Dirección Postal --}}
            <div class="form-row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label for="address" class="">@lang('Dirección Postal')</label>
                        <input value="{{$user->address}}" name="address" id="address" type="text"
                            class="form-control" />
                    </div>
                </div>
            </div>
            {{-- /Dirección Postal --}}

            <div class="form-row">

                {{-- Código Postal --}}
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label for="code_postal" class="">@lang('Código Postal')</label>
                        <input value="{{$user->code_postal}}" name="code_postal" maxlength="6" id="code_postal" type="text" class="form-control" @keypress="soloNumeros"/>
                    </div>
                </div>
                {{-- /Código Postal --}}

                {{-- Teléfono --}}
                <div class="col-md-8">
                    <label for="phone" class="">@lang('Teléfono')</label>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                {{-- Valor por defecto para companyDialCode, 
                                     valor por defecto que sera tomado por vue
                                --}}
                                <input type="hidden" name="companyDialCode" form="form-user-data" id="company-bdial" value="@exists($user->billing->dial_code)">
                                {{-- Componente Select2
                                    @link https://github.com/godbasin/vue-select2

                                    Para las opciones originales del Select2@hasSection ('
                                    @link https://select2.org/configuration/options-api')
                                --}}
                                <Select2 id="select-country" class="select2"
                                    :options="codeCountries" :settings="{tags: true, templateResult: changeTemplate, templateSelection: changeTemplate}" v-model="dial_code" name="dial_code" data-dial="{{$user->dial_code}}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <input value="{{$user->phone}}" name="phone" id="phone" type="text" class="form-control"
                                @keypress="soloNumeros"/>
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
                        <label for="city" class="">@lang('Localidad')</label>
                        <input value="{{$user->city}}" name="city" id="city" type="text" class="form-control" @keypress="soloLetras" />
                    </div>
                </div>
                {{-- /Localidad --}}

                {{-- Provincia --}}
                <div class="col-md-4">
                    <div class="position-relative form-group">
                        <label for="province" class="">@lang('Provincia')</label>
                        <input value="{{$user->province}}" name="province" id="province" type="text"
                            class="form-control" @keypress="soloLetras" />
                    </div>
                </div>
                {{-- Provincia --}}

                {{-- Pais --}}
                <div class="col-md-4">
                    <div class="position-relative form-group">

                        <label for="country" class="">@lang('País')</label>
                        <Select2 value="{{$user->country}}"
                            name="country" id="country"
                            class="select2"
                            :options="optionsCountries"
                        />

                    </div>
                </div>
                {{-- /Pais --}}

            </div>

            {{-- Los datos adicionales para la cuenta de empresa --}}
            <div v-show="type == 1" class="form-row">
                
                {{-- Empresa/Razón Social --}}
                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label for="company" class="">@lang('Empresa/Razón Social')</label>
                        <input value="{{$user->company}}" name="company" id="company" type="text" class="form-control" />
                    </div>
                </div>
                {{-- /Empresa/Razón Social --}}

                {{-- Cargo --}}
                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <label for="position" class="">@lang('Cargo')</label>
                        <input value="{{$user->position}}" name="position" id="position" type="text"
                            class="form-control" />
                    </div>
                </div>
                {{-- /Cargo --}}

            </div>
            {{--/Los datos adicionales para la cuenta de empresa --}}

        </form>

    </div>
</div>
{{--/ Datos Personales --}}