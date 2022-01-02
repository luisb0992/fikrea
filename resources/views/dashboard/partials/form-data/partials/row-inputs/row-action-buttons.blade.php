<label for="">&nbsp;</label>
<div class="d-flex align-items-center justify-content-md-end">

    {{-- boton de configuracion para valdiar los campos de la fila --}}
    <span data-toggle="tooltip" title="@lang('Valide sus campos dándole un formato único')">
        <b-button @click="$bvModal.show('validationModal-'+i)" variant="primary">
            <i class="fas fa-cog"></i> @lang('Configuración')
        </b-button>
    </span>

    {{-- ventana popover: muestra la info de las validaciones realizadas para el campo --}}
    <span class="ml-1">
        <b-button :id="'popover-info'+i" variant="info">
            <i class="fas fa-info-circle"></i>
        </b-button>
        <b-popover :target="'popover-info'+i">
            <template #title>
                <h5>
                    @lang('Validaciones realizadas')
                    <i class="fas fa-check-square text-success" v-if="row.isFieldTextValidated"></i>
                    <i class="fas fa-exclamation-triangle text-warning" v-else></i>
                </h5>
            </template>
            <div class="list-group">
                <li class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1 font-weight-bold">@lang('Mínimo aceptado')</p>
                        <small class="text-muted">
                            <i class="fas fa-check-square text-success" v-if="row.isFieldTextValidated"></i>
                        </small>
                    </div>
                    <p class="mb-1 mt-1">@{{ row . min ?? '---' }}</p>
                </li>
                <li class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1 font-weight-bold">@lang('Máximo permitido')</p>
                        <small class="text-muted">
                            <i class="fas fa-check-square text-success" v-if="row.isFieldTextValidated"></i>
                        </small>
                    </div>
                    <p class="mb-1 mt-1">@{{ row . max ?? '---' }}</p>
                </li>
                <li class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1 font-weight-bold">@lang('Tipo de carácter')</p>
                        <small class="text-muted">
                            <i class="fas fa-check-square text-success" v-if="row.isFieldTextValidated"></i>
                        </small>
                    </div>
                    <p class="mb-1 mt-1">
                        <select class="form-control" v-model="row.characterType" disabled>
                            <option value="" :selected="row.characterType">@lang('Cualquiera')</option>
                            @foreach ($characterTypes as $type)
                                <option value="{{ $type }}" :selected="row.characterType">
                                    @include('dashboard.partials.form-data.partials.character-type', [
                                    'type' => $type
                                    ])
                                </option>
                            @endforeach
                        </select>
                    </p>
                </li>
            </div>
        </b-popover>
    </span>

    {{-- boton para eliminar toda la fila correspondiente --}}
    <span data-toggle="tooltip" title="@lang('Eliminar campo y sus validaciones')" class="ml-1">
        <button type="button" class="btn btn-danger" @click="deleteDataFormRow(i)">
            <i class="far fa-trash-alt"></i>
        </button>
    </span>
</div>
