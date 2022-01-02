{{--
    Muestra una lista de facturas
--}}

<div class="table-responsive row col-md-12">    
    <table class="mb-0 table table-striped">
        <thead>
            @include('backend.partials.header-table-orders')
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td class="text-secondary bold" data-label="@lang('Factura') #">{{$order->order}}</td>
                <td data-label="@lang('Apellidos')">{{$order->user->lastname}}</td>
                <td data-label="@lang('Nombre')">{{$order->user->name}}</td>
                <td data-label="@lang('Email')">
                    <a href="mailto:{{$order->user->email}}">
                        {{$order->user->email}}
                    </a>
                </td>
                <td data-label="@lang('Teléfono')">
                    <a href="tel:{{$order->user->phone}}">
                        {{$order->user->phone}}
                    </a>
                </td>
                <td data-label="@lang('Empresa')">@exists($order->user->company->name)</td>
                <td data-label="@lang('Cargo')">{{$order->user->position}}</td>
                <td class="text-right" data-label="@lang('Importe')">{{$order->amount}} @config('app.currency')</td>
                <td class="text-danger" data-label="@lang('Plan')">{{$order->plan->name}}</td>
                <td class="text-center text-info" data-label="@lang('Pagada')">@date($order->payed_at)</td>
                <td class="text-center">
                    <a href="@route('subscription.bill', ['order' => $order->id])" class="btn btn-warning square">
                        <i class="fas fa-file-invoice"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center text-danger bold">
                    @lang('No hay facturas registrados')
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            @include('backend.partials.header-table-orders')
        </tfoot>
    </table>

    {{-- Control de la Tabla --}}
    <div class="control-wrapper mt-4">

        {{--Paginador --}}
        <div class="paginator-wrapper">
            {{$orders->links()}}

            @lang('Se muestran :files de un total de :total facturas', [
                'files' => $orders->count(),
                'total' => $orders->total(),
            ])

        </div>
        {{--/Paginador --}}
 
    </div>
    {{--/Control de la Tabla --}}

</div>