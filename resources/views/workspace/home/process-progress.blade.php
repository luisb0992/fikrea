{{-- Progreso de la tarea cuando se trata de la validación de un documento --}}
@if ($signer->document)
<div class="progress-info">
    
    {{--Progreso del proceso de validación--}}
    <span class="text bold text-info">@lang('Progreso actual en todo el proceso')</span>
    <span class="text text-secondary">{{$signer->document->progress}}%</span>
    <progress class='document-progress' min="0" max="100" value="{{$signer->document->progress}}"></progress>
	{{--/Progreso del proceso de validación--}}

    {{--Progreso de las validaciones del firmante--}}
    <span class="text bold text-success">@lang('Progreso actual en sus validaciones')</span>
    <span class="text text-secondary">{{$signer->progress}}%</span>
    <progress class='signer-progress' min="0" max="100" value="{{$signer->progress}}"></progress>
    {{--/Progreso de las validaciones del firmante--}}

</div>
@endif
{{--/Progreso de la tarea --}}