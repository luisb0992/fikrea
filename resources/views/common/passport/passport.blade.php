<div v-cloak id="app" class="col-md-12">

    {{-- Los datos de la app para vue js --}}
    @include('common.passport.data')
    {{-- / Los datos de la app para vue js --}}

    {{-- Los botones de Acción --}}
    @include('common.passport.action-buttons')
    {{--/Los botones de Acción --}}

    {{-- Controles de cámara y foto facial --}}
    @include('common.passport.facial-recognition')
    {{-- / Controles de cámara y foto facial --}}

    {{--Documento de Identificación --}}
    @include('common.passport.document')
    {{--/Documento de Identificación --}}

    {{-- Listado de documentos de identificación adjuntos --}}
    @include('common.passport.documents-list')
    {{--/Listado de documentos de identificación adjuntos --}}

    {{-- Los botones de Acción --}}
    @include('common.passport.action-buttons')
    {{--/Los botones de Acción --}}

</div>
