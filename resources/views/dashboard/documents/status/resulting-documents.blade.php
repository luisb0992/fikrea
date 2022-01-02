{{-- Listado de documentos resultantes de la validación --}}
<div class="col-md-12">
    <h5>
        <i class="fas fa-table text-secondary"></i> 
        @lang('Lista de Documentos')
    </h5>
</div>

<div class="col-md-12">
    <div class="main-card card">

        <div class="card-body table-responsive">
            <table id="documents" class="table table-striped">
                <thead>
                    @include('dashboard.documents.status.header-resulting-documents')
                </thead>
                <tbody>
                    
                    {{-- El documento con las cajas de textos --}}
                    @include('dashboard.documents.status.resulting.textboxs-documents')
                    {{-- /El documento con las cajas de textos --}}

                    {{-- El documento firmado --}}
                    @include('dashboard.documents.status.resulting.handwritten-documents')
                    {{-- /El documento firmado --}}

                    {{-- Los archivos de audio --}}
                    @include('dashboard.documents.status.resulting.audio-documents')
                    {{-- /Los archivos de audio --}}

                    {{-- Los archivos de video --}}
                    @include('dashboard.documents.status.resulting.video-documents')
                    {{-- /Los archivos de video --}}

                    {{-- Las capturas de pantalla --}}
                    @include('dashboard.documents.status.resulting.captures-documents')
                    {{-- /Las capturas de pantalla --}}

                    {{-- Los documentos identificativos --}}
                    @include('dashboard.documents.status.resulting.passports-documents')
                    {{-- /Los documentos identificativos --}}

                    {{-- Los documentos aportados --}}
                    @include('dashboard.documents.status.resulting.requested-documents')
                    {{-- /Los documentos aportados --}}

                    {{-- Cuando no hay registros en la tabla --}}
                    <tr v-show="noDocuments">
                        <td colspan="9" class="text-center text-danger bold">
                            @lang('No hay registros')
                        </td>
                    </tr>
                    {{--/Cuando no hay registros en la tabla --}}

                </tbody>
                <tfoot>
                    @include('dashboard.documents.status.header-resulting-documents')
                </tfoot>
            </table>
        </div>
    </div>
</div>
{{--/ Listado de documentos resultantes de la validación --}}