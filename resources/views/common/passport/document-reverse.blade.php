{{-- Reverso del documento de identificación --}}
<div class="col-md-4 mt-1">

    <div class="card">

        <div class="card-body">

            <div v-show="webcam2">

                <div class="webcam">
                    <video  id="webcam2" autoplay muted playsinline></video>
                    <canvas id="overlay2" />
                </div>
                <canvas id="canvas2" class="d-none"></canvas>

            </div>

            <div v-show="!webcam2">

                <img :class="passport.back ? 'no-passport' : 'passport'"
                    :src="passport.back ? passport.back : '@asset('/assets/images/workspace/dni-back.png')'" alt="" />

            </div>

            <div id="ocr"></div>

            <div class="btn-group mt-2" role="group" aria-label="">

                {{-- Iniciar o identificar la cámara para el reverso --}}
                <button v-if="!webcam2 && !passport.back"
                    @click.prevent="setupWebcam(2)" type="button" class="btn btn-sm btn-success">
                    <i class="fas fa-play"></i>
                    @lang('Iniciar cámara')
                </button>
                {{-- / Iniciar o identificar la cámara --}}

                {{-- Boton para tomar foto del reverso del doc --}}
                <button v-if="webcam2 && !passport.back"
                    @click.prevent="takeReversoRealTimePhoto" type="button" class="btn btn-sm btn-info"
                >
                    <i class="fas fa-camera"></i>
                    @lang('Hacer foto')
                </button>
                {{-- /Boton para tomar foto de la cara del firmante --}}

                {{-- Elimina reverso del documento --}}
                <button v-if="passport.back"
                    @click="deleteReverso" type="button" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                    @lang('Eliminar')
                </button>
                {{-- /Elimina reverso del documento --}}

            </div>

        </div>

        

    </div>

</div>
{{--/Reverso del documento de identificación --}}