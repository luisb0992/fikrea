{{-- Una tabla con el listado de archivos cargados --}}
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">
            @lang('Documentos aportados')
        </h5>   

        <div class="table-responsive">
            <table class="table table-striped">
    
                <thead>
                    @include('common.request.header-loaded-files')
                </thead>

                <tbody v-if="!files.length">
                    <tr>
                        <td colspan="8" class="text-center text-danger bold">@lang('No se han aportado archivos')</td>
                    </tr>
                </tbody>

                <tbody v-else>
                    <tr v-for="file in files">
                        <td data-label="@lang('Documento')">@{{file.document.name}}</td>
                        <td data-label="@lang('Nombre')">@{{file.name}}</td>
                        <td data-label="@lang('Tipo')">
                            @{{file.type}}
                        </td>
                        <td class="text-center">
                           <img v-show="file.hasPreview" class="img-fluid thumb" :src="file.file" alt="" />
                        </td>
                        <td data-label="@lang('Tamaño (kB)')">@{{file.size/1024 | int}}</td>
                        <td data-label="@lang('Fecha de Expedición')">
                            {{-- Si se ha exigido una fecha de expedición máxima para el documento y no se ha suministrado --}}
                            <span v-if="file.document.issued_to && !file.issued_to">
                                @lang('No definida')
                            </span>
                            {{-- Si el documento posee fecha de expedición y la fecha suministrada es anterior a la fecha de expedición exigida --}}
                            <span v-else-if="file.document.issued_to && file.issued_to <= file.document.issued_to">
                                <span class="text-danger bold p-2">
                                    @{{file.issued_to | date}}
                                </span>
                            </span>
                            {{-- En los demás casos, si se ha suminisitrado la fecha de expedición del documento --}}
                            <span v-else-if="file.issued_to">
                                <span class="text-success bold p-2">
                                   @{{file.issued_to | date}}
                                </span>
                                
                            </span>
                        </td>
                        <td data-label="@lang('Fecha de vencimiento')">
                            @{{ file.expiration_date | date }}
                        </td>
                        <td class="text-center">
                            <button @click.prevent="removeFile(file)" class="btn btn-danger square">
                                <i class="fa fa-trash fa-2x"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
{{--/Una tabla con el listado de archivos cargados --}}