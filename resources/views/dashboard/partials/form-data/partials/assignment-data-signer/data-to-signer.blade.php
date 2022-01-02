{{-- mensaje informativo --}}
<div class="alert alert-info" role="alert" v-if="isNoSingerAvailable">
    <h5 class="alert-heading">@lang('Excelente')!</h5>
    <p>
        <small>@lang('Ya ha asignado a todos los firmantes disponibles la verificación de datos')</small>
    </p>
</div>

{{-- Accordion para todos los validados --}}
<div class="accordion mb-4" id="accordionSignersValidated">

    {{-- Tarjeta de cada firmante validado --}}
    <div class="card border-success mt-4" v-for="(row, index) in dataFormValidatedOnView" :key="index">

        {{-- Boton y titulo de la tarjeta --}}
        <div class="pt-3 px-4 pb-3 d-flex border-bottom" :id="'header-'+index">
            <div class="flex-grow-1 text-left">
                <button class="btn btn-outline-info" type="button" data-toggle="collapse" :data-target="'#collapse-'+index"
                    :aria-expanded="index === 0 ? true : false" :aria-controls="'collapse-'+index">
                    <i class="fas fa-user-check"></i> <span>@{{ row . nameSigner }}</span>
                </button>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-warning" data-toggle="tooltip"
                    title="@lang('Editar la asignación del formulario')"
                    @click="editFormAssignment(index, row.signer_id)">
                    <i class="fas fa-edit"></i> @lang('Editar')
                </button>
                <button type="button" class="btn btn-danger" data-toggle="tooltip"
                    title="@lang('Eliminar la asignación del formulario')"
                    @click="removeFormAssignment(index, row.signer_id)">
                    <i class="fas fa-user-times mr-1"></i> @lang('Eliminar')
                </button>
            </div>
        </div>
        {{-- /Boton y titulo de la tarjeta --}}

        {{-- Cuerpo y tabla de datos con los datos validados --}}
        <div :id="'collapse-'+index" class="collapse show" :aria-labelledby="'header-'+index"
            data-parent="#accordionSignersValidated">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">@lang('Nombre')</th>
                                <th scope="col">@lang('Descripción')</th>
                                <th scope="col">@lang('Mínimo aceptado')</th>
                                <th scope="col">@lang('Máximo permitido')</th>
                                <th scope="col">@lang('Tipo de carácter')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(input, indexTwo) in row.dataValidated" :key="indexTwo">
                                <th scope="row">@{{ indexTwo + 1 }}</th>
                                <td>@{{ input . field_name }}</td>
                                <td>@{{ input . field_text ?? '---' }}</td>
                                <td>@{{ input . min ?? '---' }}</td>
                                <td>@{{ input . max ?? '---' }}</td>
                                <td>@{{ input . tipo_caracter ?? '---' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- /Cuerpo y tabla de datos con los datos validados --}}
    </div>
    {{-- /Tarjeta de cada firmante validado --}}


</div>
{{-- Accordion para todos los validados --}}
