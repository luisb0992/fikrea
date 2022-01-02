<h3>2) @lang('Datos de la Entidad Certificadora')</h3>
<p>@lang('Los datos aquí mostrados han sido verificados por :company.', ['company' => @config('company.name')])</p>
<table>
    <thead>
    <tr>
        <th colspan="2">@lang('Entidad Certificadora')</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>@lang('Nombre')</td>
        <td>@config('certified.name')</td>
    </tr>
    <tr>
        <td>@lang('CIF')</td>
        <td>@config('certified.cif')</td>
    </tr>
    <tr>
        <td>@lang('Registro Mercantil')</td>
        <td>
            <table class="no-bordered">
                <tr>
                    <td colspan="4">
                        @lang('Entidad dada de alta en el :register a :date', [
                            'register' => config('certified.record.register'),
                            'date'     => config('certified.record.date')
                        ])
                    </td>
                <tr>
                    <td class="bold">@lang('Registro')</td>
                    <td class="bold">@lang('Tomo')</td>
                    <td class="bold">@lang('Sección')</td>
                    <td class="bold">@lang('Folio')</td>
                    <td class="bold">@lang('Página')</td>
                    {{-- <td class="bold">@lang('Asiento')</td>--}}
                    <td class="bold">@lang('Inscripción')</td>
                </tr>
                <tr>
                    <td>@config('certified.record.number')</td>
                    <td>@config('certified.record.volume')</td>
                    <td>@config('certified.record.section')</td>
                    <td>@config('certified.record.invoice')</td>
                    <td>@config('certified.record.page')</td>
                    {{-- <td>@config('certified.record.seat')</td>--}}
                    <td>@config('certified.record.inscription')</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>@lang('Dirección')</td>
        <td>
            @config('certified.street')
            @config('certified.zip')
            @config('certified.city')
            @config('certified.country')
        </td>
    </tr>
    <tr>
        <td>@lang('Representante')</td>
        <td>
            @config('certified.representation.name')
            (@config('certified.representation.cif'))
            <div>
                @lang('en calidad de :position', ['position' => config('certified.representation.position')])
            </div>
            <div class="text-right">
                <img src="@config('certified.representation.sign')" alt=""/>
            </div>
        </td>
    </tr>
    {{-- Si la entidad está certificada según regalemento UE 910 2014 de la CEE --}}
    @if(config('certified.ue_910_2014'))
        <tr>
            <td>@lang('UE 910 2014')</td>
            <td>
                <img src="data:image/png;base64,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"
                     alt=""/>
            </td>
        </tr>
    @endif
    {{-- Si la entidad está certificada según regalemento UE 910 2014 de la CEE --}}
    </tbody>
</table>
