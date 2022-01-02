<h3>3) @lang('Observaciones')</h3>
<ul>
    <li>@lang('Algoritmo de cifrado') @config('app.cipher')</li>
    <li>@lang('Todas las fechas y horas se refieren a :zone', ['zone' => config('app.timezone')])</li>
    <li>@lang('Las posiciones recogidas se refieren al datum WGS84')</li>
</ul>
