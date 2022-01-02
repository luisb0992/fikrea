{{-- Botón para subir más archivos --}}
<div>
    <file-upload
        ref="upload"
        class="btn btn-primary btn-lg"

        v-model="files"                                 {{-- Lista de archivos                  --}}

        :thread="20"                                    {{-- Número máximo de archivos              --}}
        :multiple="true"                                {{-- Subida múltiple de archivos            --}}
        :directory="false"                              {{-- No subir directorios enteros           --}}
        :drop="true"                                    {{-- permite arrastrar archivos             --}}
        :drop-directory="false"                         {{-- No arrastar directorios enteros        --}}
        :post-action="'@route('dashboard.file.save', ['id' => request()->id])'"  {{-- La ruta de subida de archivos          --}}
        :data="{ '_token': '{{csrf_token()}}' }"        {{-- El token CSRF                          --}}
        @input-filter="inputFilter"                     {{-- Un filtro para el tamaño de archivos   --}}
        @input-file="inputFile"                         {{-- Al seleccionar un archivo              --}}
    >
        <i class="fas fa-cloud-upload-alt"></i>
        @lang('Seleccionar')
    </file-upload>

    {{-- Botón detener la subida de archivos --}}
    <button v-if="$refs.upload && $refs.upload.active"
        type="button" class="btn btn-danger btn-lg" @click.prevent="$refs.upload.active = false">
        <i class="fa fa-stop" aria-hidden="true"></i>
        @lang('Detener')
    </button>
    {{--/Botón detener la subida de archivos --}}

</div>
{{-- Botón para subir más archivos --}}