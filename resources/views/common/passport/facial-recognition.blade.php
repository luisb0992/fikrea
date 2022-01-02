{{-- Para reconocimiento facial del firmante --}}
<div class="row col-md-12">
    <h5>
        <img src="@asset('/assets/images/workspace/face.png')" class="icon-image">
        @lang('Foto que tomaremos como referencia a su identidad')
    </h5>
</div>

<div class="row col-md-12 mb-4">

	{{--Controles webcam --}}
    @include('common.passport.webcam-controls')
    {{--/Controles webcam --}}

    {{--
            La imagen del usuario
            Sólo si se va a usar reconocimiento facial
    --}}
    
	{{--Foto facial del firmante --}}
    {{-- @include('common.passport.facial-photo') --}}
    {{--/Foto facial del firmante --}}
     

</div>
{{-- / Para reconocimiento facial del firmante --}}
