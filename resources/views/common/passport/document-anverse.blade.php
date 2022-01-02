{{-- Anverso del documento de identificación --}}
<div class="col-md-4 mt-1">

    <div class="card">

        <div class="card-body">

            <div v-show="webcam1">

                <div class="webcam">
                    <video  id="webcam1" autoplay muted playsinline></video>
                    <canvas id="overlay1" />
                </div>
                <canvas id="canvas1" class="d-none"></canvas>

            </div>

            <div v-show="!webcam1">

                <img :class="passport.front ? 'no-passport' : 'passport'"
                    :src="passport.front ? passport.front : '@asset('/assets/images/workspace/dni-front.png')'" alt="" />

            </div>

            @if ($useFacialRecognition)
            {{-- Texto de ayuda para que encuadre su cara en la webcam --}}
            <div v-if="webcam1 && !detectedFace && !passport.front" class="p-2">
                <span class="small text-danger ">
                    @lang('Encuadre el anverso del documento y espere a que se detecte su rostro sobre este').
                </span>
            </div>
            {{-- /Texto de ayuda para que encuadre su cara en la webcam --}}
            @endif

            <div class="btn-group mt-2" role="group" aria-label="">

                {{-- Iniciar o identificar la cámara para el anverso --}}
                <button v-if="!webcam1 && !passport.front"
                    :disabled="!canInitWebCWithFR"
                    @click.prevent="setupWebcam(1)" type="button" class="btn btn-sm btn-success">
                    <i class="fas fa-play"></i>
                    @lang('Iniciar cámara')
                </button>
                {{-- / Iniciar o identificar la cámara --}}

                {{-- Boton para tomar foto del anverso del doc --}}
                <button v-if="webcam1 && !passport.front"
                    :disabled="!canTakeFacialPhoto"
                    @click.prevent="takeAnversoRealTimePhoto" type="button" class="btn btn-sm btn-success"
                >
                    <i class="fas fa-camera"></i>
                    @lang('Hacer foto')
                </button>
                {{-- /Boton para tomar foto de la cara del firmante --}}

                {{-- Elimina anverso del documento --}}
                <button v-if="passport.front"
                    @click="deleteAnverso" type="button" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                    @lang('Eliminar')
                </button>
                {{-- /Elimina anverso del documento --}}

            </div>

        </div>

        

    </div>

</div>
{{--/Anverso del documento de identificación --}}