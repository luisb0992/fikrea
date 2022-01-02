<div class="col-md-12 mb-3" v-show="!isNoSingerAvailable">
    <div class="card mb-2 border-info">
        <h5 class="d-flex justify-content-between bg-info card-header text-white">
            <div class="flex-grow-1 text-left"><i class="fas fa-tasks"></i> @lang('Verificación de datos')</div>
            <div class="text-right">
                <button type="button" class="btn btn-warning" data-toggle="tooltip"
                    title="@lang('Esta opción eliminará la plantilla que está en uso')" @click="addNewFormTemplate"
                    :disabled="isNoSingerAvailable">
                    <i class="fas fa-plus"></i> @lang('Nueva plantilla')
                </button>
            </div>
        </h5>

        {{-- Formulario que se muestra al estar la plantilla cargada....
                    o donde se crea desde cero campos para validar un usuario o "firmante" --}}
        <form @submit.prevent="addFormToUSerforValidate">

            {{-- Botones de interaccion --}}
            <div class="card-footer bg-transparent d-flex justify-content-end mb-n3">
                <button type="button" class="btn btn-success mr-2" @click="addNewFieldToForm" :disabled="isNoSingerAvailable">
                    <i class="fas fa-plus-square"></i> @lang('Agregar nuevo concepto')
                </button>
                <button type="submit" class="btn btn-warning" :disabled="isNoSingerAvailable">
                    <i class="fas fa-save"></i> @lang('Guardar formulario para verificar')
                </button>
            </div>
            {{-- /Botones de interaccion --}}

            <div class="card-body">

                {{-- Seleccion de firmante o usuario a solicitar datos --}}
                <div class="form-group">
                    <label for="select-signer">@lang('Seleccione un usuario o firmante')</label>
                    <select class="form-control" id="select-signer">
                        <option v-for="(sign, i) in selectSigners" :value="sign.id" :key="i">
                            @{{ sign . name ? (sign . lastname ? sign . name + ' ' + sign . lastname : sign . name) : sign . email }}
                        </option>
                    </select>
                </div>

                {{-- Seleccion de tipo de formulario --}}
                <div class="form-group" v-show="isNewFormTemplate && rowsFormData.length">
                    <label for="select-template">@lang('Nueva plantilla')</label>
                    <select class="form-control" id="select-template" @change="changeValueInType">
                        <option value="{{ \App\Enums\FormType::PARTICULAR_FORM }}">@lang('Particular')</option>
                        <option value="{{ \App\Enums\FormType::BUSINESS_FORM }}">@lang('Empresarial')</option>
                    </select>
                </div>

                <div class="form-row border shadow-sm mb-2 p-2 bg-light" v-if="rowsFormData.length > 1">
                    <div class="form-group col-md-12">
                        <div class="d-md-flex align-items-md-center ml-4 mt-2 mt-md-0">
                            <input type="checkbox" v-model="allChecked" @click="changeCheckStatus"
                                class="form-check-input" style="transform: scale(2.0)">
                            <label class="ml-2 mt-md-2">
                                <small class="text-dark text-center">
                                    <span class="text-primary">
                                        @lang('Marque o desmarque todos los campos si lo desea')
                                    </span>
                                </small>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- fila de inputs del formulario --}}
                <div id="bodyFormTemplate">
                    @include('dashboard.partials.form-data.partials.html-load-row-inputs')
                </div>
            </div>

            {{-- Botones de interaccion --}}
            <div class="card-footer bg-transparent d-flex justify-content-end">
                <button type="button" class="btn btn-success mr-2" @click="addNewFieldToForm" :disabled="isNoSingerAvailable">
                    <i class="fas fa-plus-square"></i> @lang('Agregar nuevo concepto')
                </button>
                <button type="submit" class="btn btn-warning" :disabled="isNoSingerAvailable">
                    <i class="fas fa-save"></i> @lang('Guardar formulario para verificar')
                </button>
            </div>
            {{-- /Botones de interaccion --}}

        </form>
    </div>
</div>
