{{--
    Muestra una lista de planes de la aplicación
--}}

<div class="table-responsive col-md-12">    
    <table class="mb-0 table table-striped">
        <thead>
            @include('backend.partials.header-table-plans')
        </thead>
        <tbody>
            @foreach ($plans as $plan)
            <tr>
                <td data-label="#">{{$loop->iteration}}</td>
                <td data-label="@lang('Nombre')">{{$plan->name}}</td>
                <td data-label="@lang('Espacio (MB)')">{{$plan->disk_space}}</td>
                <td data-label="@lang('Teléfono')">{{$plan->signers}}</td>
                <td data-label="@lang('Empresa')">{{$plan->monthly_price}}</td>
                <td data-label="@lang('Cargo')">{{$plan->yearly_price}}</td>
                <td data-label="@lang('Espacio Utilizado')">{{$plan->change_price}}</td>
                <td data-label="@lang('Espacio Utilizado')">{{$plan->tax}}</td>
                <td data-label="@lang('Espacio Utilizado')">{{$plan->trial_period}}</td>
                <td class="text-center">
                    
                    {{-- Edita la subscripción --}}
                    <a href="@route('backend.plans.editPlans', ['id' => $plan->id])" class="btn btn-warning square"
                        data-toggle="tooltip" data-placement="top" data-original-title="@lang('Editar el plan')">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    {{-- Eliminar la subscripción --}}
                    <a href="@route('backend.plans.deletePlans', ['id' => $plan->id])" class="btn btn-danger square"
                        data-toggle="tooltip" data-placement="top" data-original-title="@lang('eliminar el plan')">
                        <i class="fa fa-trash-alt"></i>
                    </a> 
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @include('backend.partials.header-table-plans')
        </tfoot>
    </table>

</div>