{{--
    Tabla que muestra los firmanentes (signers) o usuarios que han sido selecciondos para realizar
    un proceso de firma, validación de documentos, atender a una solicitud, verificación de datos
--}}

<div class="main-card mb-3 card">
    <div class="card-body"><h5 class="card-title">@lang('Lista de Usuarios')</h5>
        <div class="table-responsive">

            {{--  Si es un proceso de con documento
                sino es una verificación de datos independiente del documento  --}}
            @if(isset($document))
                <table id="signers" class="mb-0 table table-striped" data-signers="@json($document->signers)">
            @else
                <table id="signers" class="mb-0 table table-striped" data-signers="@json($verificationForm->signers)">
            @endif
                <thead>
                    <tr>
                        <th>@lang('Apellidos')</th>
                        <th>@lang('Nombre')</th>
                        <th>@lang('Dirección de Correo')</th>
                        <th>@lang('Teléfono')</th>
                        <th>@lang('DNI')</th>
                        <th>@lang('Compañía')</th>
                        <th>@lang('Cargo')</th>
                        <th></th>
                    </tr>
                </thead>

                {{-- Template con la lista dinámica de firmantes --}}
                <tbody v-if="signers.length">
                    <tr v-for="(signer,index) in signers" :key="index">
                        <td data-label="@lang('Apellidos')">@{{ signer.lastname }}</td>
                        <td data-label="@lang('Nombre')">@{{ signer.name }}</td>
                        <td data-label="@lang('Email')">@{{ signer.email }}</td>
                        <td data-label="@lang('Teléfono')">@{{ signer.phone }}</td>
                        <td data-label="@lang('DNI')">@{{ signer.dni }}</td>
                        <td data-label="@lang('Compañía')">@{{ signer.company }}</td>
                        <td data-label="@lang('Cargo')">@{{ signer.position }}</td>
                        <td class="text-center">
                            <button @click.prevent="removeSigner(index)" class="btn btn-danger">
                                @lang('Eliminar')
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="8" class="text-center text-danger bold">
                            @lang('No hay registros')
                        </td>
                    </tr>
                </tbody>
                {{--/ Template con la lista dinámica de firmantes --}}

            </table>
        </div>
    </div>

    <div v-show="maxSignerExceed" class="col-md-12 mb-2 text-danger text-right bold">
        <i class="fas fa-ban"></i>
        {{--  Si es un proceso de con documento
                sino es una verificación de datos independiente del documento  --}}
        @if(isset($document))
            @lang('No puede añadir más personas con el plan de suscripción actual ":plan"',[
                'plan'  => $user->subscription->plan->name,
            ])
        @else
            @lang('Solo puede seleccionar un usuario o firmante para este proceso')
        @endif
    </div>

</div>