{{-- Una tabla con los archivos de Video --}}
<div class="col-md-12 my-4">
    <div class="table-responsive card">
        <table class="table table-striped">

            <thead>
                <tr>
                    <th>@lang('Nombre de archivo')</th>
                    <th>@lang('Duración') (s)</th>
                    <th>@lang('Tamaño')</th>
                    <th class="text-center">@lang('Ver')</th>
                    <th></th>
                </tr>
            </thead>

            <tbody v-if="!captures.length">
                <tr>
                    <td colspan="6" class="text-center text-danger bold">@lang('No hay capturas de pantalla')</td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr v-for="capture in captures">
                    
                    <td data-label="@lang('Nombre de archivo')">@{{ capture . filename }}</td>
                    <td data-label="@lang('Duración')">@{{ capture . duration }}</td>
                    <td data-label="@lang('Tamaño')">@{{ getFileSize(capture.size) }}</td>
                    <td data-label="@lang('Ver')" class="text-center">
                        <video :src="capture.video" class="video-thumb" controls></video>
                        <div class="text-center bold">
                            @{{ capture . duration }}
                        </div>
                    </td>
                    <td data-label="@lang('Opciones')" class="text-center">
                        <button @click.prevent="editCapture(capture)" class="btn btn-warning square">
                            <i class="fa fa-edit fa-2x"></i>
                        </button>
                        <button @click.prevent="removeCapture(capture)" class="btn btn-danger square">
                            <i class="fa fa-trash fa-2x"></i>
                        </button>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
</div>
{{-- /Una tabla con los archivos de Video --}}