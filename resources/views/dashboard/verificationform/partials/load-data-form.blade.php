<div class="col-md-12 mb-3">
    <div class="card mb-2 border-info">
        <h5 class="d-flex justify-content-between bg-info card-header text-white">
            <div class="flex-grow-1 text-left"><i class="fas fa-tasks"></i> @lang('verificación de datos')</div>
            <div class="text-right">
                <button type="button" class="btn btn-warning" data-toggle="tooltip"
                    title="@lang('Esta opción eliminará la plantilla que está en uso')" @click="addNewFormTemplate"
                    :disabled="isActiveButton">
                    <i class="fas fa-plus"></i> @lang('Nueva plantilla')
                </button>
            </div>
        </h5>

        {{-- Formulario que se muestra al estar la plantilla cargada....
                    o donde se crea desde cero campos para validar un usuario o "firmante" --}}
        <form @submit.prevent="addFormToUSerforValidate">
            <div class="card-body">

                {{-- Seleccion de tipo de formulario --}}
                <div class="form-group" v-show="isNewFormTemplate && rowsFormData.length">
                    <label for="select-template">@lang('Nueva plantilla')</label>
                    <select class="form-control" id="select-template" @change="changeValueInType">
                        <option value="{{ \App\Enums\FormType::PARTICULAR_FORM }}">@lang('Particular')
                        </option>
                        <option value="{{ \App\Enums\FormType::BUSINESS_FORM }}">@lang('Empresarial')</option>
                    </select>
                </div>

                {{-- nombre para el formulario --}}
                <div class="form-group">
                    <label for="nameForm">@lang('Nombre para el formulario')</label>
                    <input type="text" class="form-control" id="nameForm" v-model="verificationForm.name"
                        placeholder="@lang('Indique un nombre descriptivo para su formulario')">
                </div>

                {{-- comentario para el formulario --}}
                <div class="form-group">
                    <label for="commentForm">@lang('Comentario')</label>
                    <textarea class="form-control" id="commentForm" v-model="verificationForm.comment" cols="30"
                        rows="10" placeholder="@lang('Indique algún tipo de comentario para su formulario')"></textarea>
                </div>

                {{-- chececk para seleccion multiple --}}
                <div class="form-row border shadow-sm mb-2 p-2 bg-light" v-if="rowsFormData.length > 1">
                    <div class="form-group col-md-12">
                        <div class="d-md-flex align-items-md-center ml-4 mt-2 mt-md-0">
                            <input type="checkbox" v-model="allChecked" @click="changeCheckStatus"
                                class="form-check-input" style="transform: scale(2.0)">
                            <label class="ml-2 mt-md-2">
                                <small class="text-dark text-center">
                                    <span class="text-primary">
                                        @lang('Marque o desmarque todos los check si lo desea')
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
                <button type="button" class="btn btn-success" @click="addNewFieldToForm"
                    :disabled="isActiveButton">@lang('Agregar nuevo concepto')</button>
            </div>
        </form>
    </div>
</div>
