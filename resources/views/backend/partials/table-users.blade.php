{{--
    Muestra una lista de usuarios de la aplicación
--}}

<div class="table-responsive col-md-12">    
    <table class="mb-0 table table-striped">
        <thead>
            @include('backend.partials.header-table-users')
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td class="text-secondary" data-label="@lang('Cuenta')">
                    @if ($user->email_verified_at)
                        @switch($user->type)
                            @case(\App\Enums\UserType::PERSONAL_ACCOUNT)
                                @lang('Personal')
                                @break
                            @case(\App\Enums\UserType::BUSSINESS_ACCOUNT)
                                @lang('Empresa')
                                @break        
                        @endswitch
                    @else
                        @lang('Invitado')
                    @endif
                </td>
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
                <td data-label="@lang('Empresa')">
                    @if ($user->type == \App\Enums\UserType::BUSSINESS_ACCOUNT)
                        {{$user->company}}
                    @endif
                </td>
                <td data-label="@lang('Cargo')">
                    @if ($user->type == \App\Enums\UserType::BUSSINESS_ACCOUNT)
                        {{$user->position}}
                    @endif
                </td>
                <td data-label="@lang('Espacio Utilizado')">@filesize($user->diskSpace->used) / @filesize($user->diskSpace->available)</td>
                <td class="text-center">
                    
                    {{-- Activa/Desactiva el usuario --}}
                    @if ($user->active)
                    <a href="@route('backend.user.disable', ['id' => $user->id])" class="btn btn-danger square"
                        data-toggle="tooltip" data-placement="top" data-original-title="@lang('Desactivar Usuario')">
                        <i class="fas fa-user-times"></i>
                    </a>
                    @else
                    <a href="@route('backend.user.enable', ['id' => $user->id])" class="btn btn-success square"
                        data-toggle="tooltip" data-placement="top" data-original-title="@lang('Activar Usuario')">
                        <i class="fas fa-user-check"></i>
                    </a>
                    @endif

                    {{-- Edita la subscripción --}}
                    @if ($user->isClient())
                    <a href="@route('backend.subscription.edit', ['id' => $user->id])" class="btn btn-warning square"
                        data-toggle="tooltip" data-placement="top" data-original-title="@lang('Editar la Subscripción')">
                        <i class="fas fa-history"></i>
                    </a>      
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-danger bold">
                    @lang('No hay clientes registrados')
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            @include('backend.partials.header-table-users')
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