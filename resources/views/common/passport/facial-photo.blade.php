{{-- Card donde se muestra la foto facial para el firmante cuando es exigida --}}
<div class="col-md-5 card ml-2">

    <div class="card-body">
        <div>
            <img id="imageForFacial" class="img-fluid"
                :src="image ? image : '@asset('/assets/images/workspace/anonymous-user.png')'" alt="" 
            />
        </div>

        <div class="mt-2">
        
            {{-- Borrar imagen que he hecho para tomar como foto facial en el acto --}}
            <button v-if="image" @click.prevent="deleteFacialPhoto" type="button" class="btn btn-danger">
                <i class="fas fa-trash"></i>
                @lang('Eliminar')
            </button>
            {{-- Borrar imagen que he hecho para tomar como foto facial en el acto --}}


            {{-- Tomar imagen del webcam como foto facial en el acto --}}
            @if ($useFacialRecognition)
            {{-- Texto de ayuda para que encuadre su cara en la webcam --}}
            <div v-if="webcam && !detectedFace && !image">
                <span class="small text-secondary">
                    @lang('Encuadre su rostro y espere ser detectado para tomar su foto facial').
                </span>
            </div>
            {{-- /Texto de ayuda para que encuadre su cara en la webcam --}}
            @endif

            {{-- Boton para tomar foto de la cara del firmante --}}
            <button v-if="webcam && !image"
                :disabled="!canTakeFacialPhoto"
                @click.prevent="takeFacialPhoto" type="button" class="btn btn-primary"
            >
                <i class="fas fa-camera"></i>
                @lang('Tomar foto facial')
            </button>
            {{-- /Boton para tomar foto de la cara del firmante --}}
            
           
            {{-- / Tomar imagen del webcam como foto facial en el acto --}}

        </div>

    </div>
    

</div>