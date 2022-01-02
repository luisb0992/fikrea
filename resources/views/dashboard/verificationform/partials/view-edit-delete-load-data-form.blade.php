<div class="card" v-for="(row, index) in dataFormValidatedOnView" :key="index">

    {{-- Boton y titulo de la tarjeta --}}
    <div class="card-header d-flex">
        <div class="flex-grow-1 text-left">
            <button class="btn btn-info" type="button">
                <i class="fas fa-tasks"></i>
                <span v-if="row.name">@{{ row . name }}</span>
                <span v-else>@lang('Sin nombre de formulario')</span>
            </button>
        </div>
        <div class="text-right">
            <button type="button" class="btn btn-warning" data-toggle="tooltip"
                title="@lang('Editar la asignación del formulario')" @click="editFormAssignment(index, row.signer_id)">
                <i class="fas fa-edit"></i> @lang('Editar')
            </button>
            <button type="button" class="btn btn-danger" data-toggle="tooltip"
                title="@lang('Eliminar la asignación del formulario')" @click="removeFormAssignment()">
                <i class="fas fa-user-times mr-1"></i> @lang('Eliminar')
            </button>
        </div>
    </div>
    {{-- /Boton y titulo de la tarjeta --}}

    {{-- cuerpo del formulario asignado --}}
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
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
        <div class="form-group">
            <label>@lang('Comentario')</label>
            <textarea disabled cols="30" rows="6" class="form-control">@{{ row.comment ? row.comment.trim() : row.comment }}</textarea>
        </div>
    </div>
    {{-- /cuerpo del formulario asignado --}}
</div>
