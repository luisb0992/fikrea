{{-- Controles de la webcam o movilcam --}}
<div class="col-md-6 card">

    <div class="card-body">

        <div v-show="webcam">

            <div class="webcam">
                <video  id="webcam" autoplay muted playsinline></video>
                <canvas id="overlay" />
            </div>

            <canvas id="canvas" class="d-none"></canvas>

        </div>
         
        <div v-show="!webcam">

            <img id="imageForFacial" class="img-fluid"
                :src="image ? image : '@asset('/assets/images/workspace/anonymous-user.png')'" alt="" 
            />

        </div>

        {{-- Tomar imagen del webcam como foto facial en el acto --}}
        @if ($useFacialRecognition)
        {{-- Texto de ayuda para que encuadre su cara en la webcam --}}
        <div v-if="webcam && !detectedFace && !image">
            <span class="small text-danger bold">
                @lang('Encuadre su rostro y espere ser detectado para tomar su foto facial').
            </span>
        </div>
        {{-- /Texto de ayuda para que encuadre su cara en la webcam --}}
        @endif

        <div class="btn-group mt-2" role="group" aria-label="">

            {{-- Iniciar o identificar la cámara --}}
            <button v-if="!webcam && !image"
                {{--
                    Si se debe hacer reconocimiento facial se debe esperar a que se carguen los modelos entrenados
                --}}
                :disabled="!canInitWebCWithFR"
                @click.prevent="setupWebcam()" type="button" class="btn btn-xs btn-success">
                <i class="fas fa-play"></i>
                @lang('Iniciar cámara')
            </button>
            {{-- / Iniciar o identificar la cámara --}}

            {{-- Boton para tomar foto de la cara del firmante --}}
            <button v-if="webcam && !image"
                :disabled="!canTakeFacialPhoto"
                @click.prevent="takeFacialPhoto" type="button" class="btn btn-primary"
            >
                <i class="fas fa-camera"></i>
                @lang('Hacer foto')
            </button>
            {{-- /Boton para tomar foto de la cara del firmante --}}

            {{-- Borrar imagen que he hecho para tomar como foto facial en el acto --}}
            <button v-if="image"
                @click.prevent="deleteFacialPhoto" type="button" class="btn btn-danger">
                <i class="fas fa-trash"></i>
                @lang('Eliminar')
            </button>
            {{-- Borrar imagen que he hecho para tomar como foto facial en el acto --}}
           
        </div>

    </div>

</div>
 