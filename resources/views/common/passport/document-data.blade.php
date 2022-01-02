{{-- Datos del documento de identificación --}}
<div class="col-md-4 mt-1">

    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label class="bold" for="type">@lang('Tipo de Documento')</label>
                <select v-model="passport.type" class="form-control" name="type" id="type">
                    <option value="0">@lang('Otros')</option>
                    <option value="1">@lang('DNI')</option>
                    <option value="2">@lang('NIE')</option>
                    <option value="3">@lang('Pasaporte')</option>
                    <option value="4">@lang('Carné de conducir')</option>
                </select>
            </div>

            <div class="form-group mt-4">
                <label class="bold required" for="number">@lang('Número')</label>
                <input v-model="passport.number" :class="passport.number ? 'is-valid': 'is-invalid'" class="form-control" type="text" id="number" name="number" />
            </div>

            <div class="form-group">
                {{-- Fecha de expedición del documento --}}
                <label class="bold" for="expedition_date">@lang('Fecha de Expedición')</label>
                <vuejs-datepicker 
                    :language="datepickerLanguage"
                    format="dd-MM-yyyy" input-class="datepicker" placeholder="@lang('Ejemplo: 01-01-2010')"
                    v-model="passport.expedition_date"
                    id="expedition_date" name="expedition_date">
                </vuejs-datepicker>
            </div>

            <div class="form-group">
                {{-- Fecha de expiración o caducidad del documento --}}
                <label class="bold" for="expiration_date">@lang('Fecha de Expiración')</label>
                <vuejs-datepicker
                    :language="datepickerLanguage" 
                    format="dd-MM-yyyy" input-class="datepicker" placeholder="@lang('Ejemplo: 01-01-2010')"
                    v-model="passport.expiration_date"
                    id="expiration_date" name="expiration_date">
                </vuejs-datepicker>
            </div>

            <div class="form-group text-right mt-2">
                
                <button :disabled="!passport.front || !passport.back || !passport.number" type="button"    @click="addPassportToList" class="btn btn-success">
                    <i class="fas fa-plus-square"></i>
                    @lang('Añadir a la lista')
                </button>

            </div>

        </div>
    </div>
</div>
{{--/Datos del documento de identificación --}}