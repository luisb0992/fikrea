<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Oxygen&amp;display=swap" rel="stylesheet" /> 
    <style>
        body {
            font-family: 'Oxygen', sans-serif;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            font-size: 12px;
            color: grey;
            width: 100%;
            text-align: right;
        }
        footer {
            position: fixed; 
            bottom: 0; 
            left: 0; 
            font-size: 12px;
            color: grey;
            width: 100%;
            text-align: right;
        }
        .page_break { 
            page-break-before: always;
        }
        .small {
            font-size: 10px;
        }
        .legal {
            font-style: italic;
            font-size: 14px;
        }
        table {
            font-size: 20px;
            font-family: verdana;
            border-spacing: 0;
            border: 1px solid black;
            width: 100%;
        }
        th {
            color: white;
            background: black;
            padding: 5px;
            border: 1px solid black;
        }

        td {
            color: black;
            background: white;
            padding: 5px;
            border: 1px solid black;
        }
        
        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .w-100 {
            max-width: 100%;
        }

        .mt {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="text-right">
        <img class="w-100" src="@path('/assets/images/common/fikrea-large-logo.png')" alt="" />
    </div>

    <header>
        <div>
            @lang('Factura') #{{$order->order}}
        </div>
    </header>

    <footer>
        <div>
            @lang('Generada por :app', ['app' => @config('app.name')])
        </div> 
    </footer>
    
    <main>
       
        <table class="mt">
            <tr>
                <td><span class="text-bold">@lang('Factura')</span> : {{$order->order}}</td>
                <td class="text-right"><span class="text-bold">@lang('Fecha')</span> : @date($order->created_at)</td>
            </tr>
        </table>

        {{-- Identificación del Proveedor --}}
        <table class="mt">
            <tr>
                <td>
                    <p class="text-bold">@config('company.name')</p>
                    <p>@config('company.cif')</p>
                    <p>@config('company.address.street')</p>
                    <p>@config('company.address.city') @config('company.address.country')</p>
                    <p>@config('company.contact.phone') @config('company.contact.email')</p>
                </td>
            </tr>
        </table>
        {{--/Identificación del Proveedor --}}

        {{-- Identificación del Cliente --}}
        <p class="mt text-bold">
            @lang('Datos del Cliente')
        </p>
        <table>
            <tr>
                <td class="text-bold">@lang('Razón Social')</td>
                <td>@exists($order->user->billing->name)</td>
            </tr>
            <tr>
                <td class="text-bold">@lang('CIF')</td>
                <td>@exists($order->user->billing->cif)</td>
            </tr>
            <tr>
                <td class="text-bold">@lang('Dirección Postal')</td>
                <td>
                    @exists($order->user->billing->address)
                    @exists($order->user->billing->city)
                    @exists($order->user->billing->province)
                    @exists($order->user->billing->country)
                </td>
            </tr>
            <tr>
                <td class="text-bold">@lang('Contacto')</td>
                <td>@exists($order->user->billing->phone)</td>
            </tr>
        </table>
        {{--/Identificación del Cliente --}}

        <div class="page_break"></div>
        <br />

        {{-- Conceptos e importes facturados --}}
        <table class="mt">
            <tr>
                <th>
                    @lang('Concepto')
                </th>
                <th class="text-center">
                    @lang('Cantidad')
                </th>
                <th class="text-right">
                    @lang('Precio Unitario')
                </th>
                <th class="text-right">
                    @lang('Importe') @config('app.currency')
                </th>
            </tr>
            <tr>
                <td>
                    @lang('Subscripción :app', ['app' => config('app.name')])
                </td>
                <td class="text-center">
                    {{$order->units}}
                </td>
                <td class="text-right">
                    {{$order->price}} @config('app.currency')
                </td>
                <td class="text-right">
                    @number($order->price * $order->units) @config('app.currency')
                </td>
            </tr>
            <tr>
                <td>
                    @lang('Ampliación de Subscripción')
                </td>
                <td class="text-center">
                    {{$order->change_months}}
                </td>
                <td class="text-right">
                    {{-- Si ha habido cambio de plan de subscripción --}}
                    @if ($order->change_months)
                        @number($order->aditional_amount / $order->change_months) @config('app.currency')
                    @else
                        @number(0) @config('app.currency')
                    @endif
                </td>
                <td class="text-right">
                    @number($order->aditional_amount) @config('app.currency')
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    @lang('IVA') ( {{$order->plan->tax}} % )
                </td>
                <td class="text-right">
                    @number($order->tax) @config('app.currency')
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-bold">
                    @lang('Total')
                </td>
                <td class="text-right text-bold">
                    @number($order->amount) @config('app.currency')
                </td>
            </tr>

        </table>
        {{--/Conceptos e importes facturados --}}

        <div class="page_break"></div>
        <br />

        {{-- Incluye la política de devoluciones --}}
        @include('landing.modals.legal.docs.return-policy-document')
        {{--/Incluye la política de devoluciones --}}
  
    </main>     
</body>
</html>