{{--
    Header de la tabla de archivos
--}}

<tr>
    @if ($selection && $files->count() > 0)
        <th class="checkbox-centered">
            <label
                    data-toggle="tooltip"
                    data-placement="right" data-original-title="@lang('Seleccionar todos')"
                    @change.prevent="selectAll" class="check-container">
                <input class="form-control" type="checkbox"/>
                <span class="square-check"></span>
            </label>
        </th>
    @else
        <th class="">#</th>
    @endif

    <th style="width: 60%;">@lang('Archivo')</th>
    <th style="width: 10%;">@lang('Tipo')</th>
    <th class="text-center" style="width: 10%;">@lang('Tamaño')</th>
    <th style="width: 10%;">@lang('Creado')</th>
    <th style="width: 10%;">@lang('Última Actualización')</th>
    <th></th>
</tr>