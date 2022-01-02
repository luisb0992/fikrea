{{--
    Muestra una lista de usuarios de la aplicación
--}}
<div class="table-responsive row col-md-12">    
    <table class="mb-0 table table-striped">
        <thead>
            @include('backend.partials.header-table-subscriptions')
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td data-label="#">{{$loop->iteration}}</td>
                <td data-label="@lang('Apellidos')">{{$user->lastname}}</td>
                <td data-label="@lang('Nombre')">{{$user->name}}</td>
                <td data-label="@lang('Email')">
                    <a href="mailto:{{$user->email}}">
                        {{$user->email}}
                    </a>
                </td>
                <td data-label="@lang('Teléfono')">
                    <a href="tel:{{$user->phone}}">
                        {{$user->phone}}
                    </a>
                </td>
                <td data-label="@lang('Empresa')">@exists($user->company->name)</td>
                <td data-label="@lang('Cargo')">{{$user->position}}</td>
                <td data-label="@lang('Almacenamiento')">{{$user->custom_disk_space ?? $user->subscription->plan->disk_space}}</td>
                <td data-label="@lang('Plan')"class="text-center text-success">{{$user->subscription->plan->name}}</td>
                <td data-label="@lang('Fecha Inicio')" class="text-center">@date($user->subscription->starts_at)</td>
                <td data-label="@lang('Fecha Fin')" class="text-center">@date($user->subscription->ends_at)</td>
                <td data-label="@lang('Fecha Pago')"class="text-center">@date($user->payed_at)</td>
                <td>
                    <a href="@route('backend.subscription.edit', ['id' => $user->subscription->id])" class="btn btn-warning square">
                        <i class="fa fa-edit"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center text-danger bold">
                    @lang('No hay subscripciones registradas')
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            @include('backend.partials.header-table-subscriptions')
        </tfoot>
    </table>

    {{-- Control de la Tabla --}}
    <div class="control-wrapper mt-4">

        {{--Paginador --}}
        <div class="paginator-wrapper">
            {{$users->links()}}

            @lang('Se muestran :files de un total de :total usuarios', [
                'files' => $users->count(),
                'total' => $users->total(),
            ])

        </div>
        {{--/Paginador --}}
 
    </div>
    {{--/Control de la Tabla --}}

</div>