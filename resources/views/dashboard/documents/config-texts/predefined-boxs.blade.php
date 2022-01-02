{{-- Cajas de textos predeterminadas--}}
<b-card class="d-none d-md-block"
    {{--v-show="!box.id || !selectedBox"--}}
    :class="box.id && selectedBox ? 'mt-2':''"
    border-variant="secondary"
    header-bg-variant="primary"
    header="@lang('Cajas de textos a cumplimentar por los firmantes')"
    header-border-variant="secondary"
    header-text-variant="white"
    align="left"
    >

    <template #header>
        <i class="far fa-2x fa-object-ungroup "></i>
        <span class="ml-1">@lang('Cajas de textos predefinidas')</span>
    </template>

        <div draggable="true" id="box-initials" data-type="1" class="box">@lang('Iniciales')</div>
        <div draggable="true" id="box-fullname" data-type="2" class="box">@lang('Nombre completo')</div>
        <div draggable="true" id="box-id"       data-type="3" class="box">@lang('# Identificación')</div>
        <div draggable="true" id="box-free"     data-type="4" class="box">@lang('Texto libre')</div>
        <div draggable="true" id="box-check"    data-type="5" class="box">@lang('Casilla de verificación')</div>
        <div draggable="true" id="box-select"   data-type="6" class="box">@lang('Lista de opciones')</div>

    <template #footer>
        <b-card-text>@lang('Arrastre la caja deseada sobre el documento')</b-card-text>
    </template>

</b-card>
{{-- /Cajas de textos predeterminadas--}}