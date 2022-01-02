{{-- Control de la Tabla --}}
<div class="control-wrapper mt-4">

    {{--Paginador --}}
    <div class="paginator-wrapper">
        {{$smses->links()}}

        @lang('Se muestran :files de un total de :total mensajes', [
            'files' => $smses->count(),
            'total' => $smses->total(),
        ])

    </div>
    {{--/Paginador --}}

</div>
{{--/Control de la Tabla --}}