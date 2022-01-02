{{-- Tabla que muestra los (signers) o usuarios que han sido selecciondos para realizar
    una participacion en un evento --}}

<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">@lang('Lista de Usuarios')</h5>
        <div class="table-responsive">
            <table id="signers" class="mb-0 table table-striped" data-signers="@json($users)">
                <thead>
                    <tr>
                        <th>@lang('Apellidos')</th>
                        <th>@lang('Nombre')</th>
                        <th>@lang('Dirección de Correo')</th>
                        <th>@lang('Teléfono')</th>
                        <th>@lang('DNI')</th>
                        <th>@lang('Compañía')</th>
                        <th>@lang('Cargo')</th>
                        <th>
                            @lang('Dirección')
                            <b-button v-b-tooltip.hover
                                title="@lang('Puede editar la dirección del usuario participante antes de agregarlo al censo definitivo')"
                                variant="dark" size="sm">
                                <i class="fas fa-info-circle"></i>
                            </b-button>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="signers.length">
                    <tr v-for="(signer,index) in signers" :key="index">
                        <td data-label="@lang('Apellidos')">@{{ signer . lastname ?? '---' }}</td>
                        <td data-label="@lang('Nombre')">@{{ signer . name ?? '---' }}</td>
                        <td data-label="@lang('Email')">@{{ signer . email ?? '---' }}</td>
                        <td data-label="@lang('Teléfono')">@{{ signer . phone ?? '---' }}</td>
                        <td data-label="@lang('DNI')">@{{ signer . dni ?? '---' }}</td>
                        <td data-label="@lang('Compañía')">@{{ signer . company ?? '---' }}</td>
                        <td data-label="@lang('Cargo')">@{{ signer . position ?? '---' }}</td>
                        <td data-label="@lang('Dirección')">
                            <div v-if="signer.address">
                                <textarea class="form-control" rows="2" @input="editAddress(index)"
                                    :id="'addressEditable'+index">@{{ signer.address }}</textarea>
                            </div>
                            <div v-else>
                                <textarea class="form-control" rows="2" @input="editAddress(index)"
                                    :id="'addressEditable'+index">
                                </textarea>
                            </div>
                        </td>
                        <td class="text-center">
                            <button @click.prevent="removeSigner(index)" class="btn btn-danger">
                                @lang('Eliminar')
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="9" class="text-center text-danger bold">
                            @lang('No hay registros')
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
