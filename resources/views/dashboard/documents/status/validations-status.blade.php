{{-- Estado de cada Validación --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-thermometer-half text-danger"></i>
        @lang('Estado de las validaciones')
    </h5>
</div>

<div class="row col-md-12">

    {{--
        Se muestra un cuadro de validaciones para cada uno de los firmantes del documento 
        lo que incluye al propio autor del documento

        @see dashboard/partials/status-validation.blade.php
    --}}
    @foreach ($document->validationTypes as $type) 
            @include('dashboard.partials.status-validation', 
            [
                'type'        => $type,
                'validations' => $document->validations->where('validation', $type)
            ]
        )
    @endforeach

</div>
{{--/ Estado de cada Validación --}}