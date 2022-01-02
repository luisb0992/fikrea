{{-- Control de la captura de pantalla --}}
<div class="screen-capture">

    {{-- La ventana que muestra la captura de pantalla --}}
    <div class="col-md-12 mt-2">
        <video id="video-screen" ref="videoScreen" autoplay></video>
    </div>  
    {{--/La ventana que muestra la captura de pantalla --}}

</div>

<div class="col-md-12 mb-4 mt-2">
	<div class="btn-group text-white" role="group">
        
	    <a v-if="!recording" @click.prevent="recordCapture" class="btn btn-primary mr-1">
	    	<i class="fa fa-play"></i>
	        @lang('Iniciar grabación')
	    </a>

	    <a v-else @click.prevent="recordCapture" class="btn btn-danger">
	    	<i class="fa fa-stop"></i>
	        @lang('Detener') @{{ showTimer }}
	    </a>
	   
	</div>	
</div>



{{-- / Control de la captura de pantalla --}}