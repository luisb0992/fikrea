{{-- Listado de documentos de identificación adjuntos --}}
<div v-if="canUploadDocuments" class="row col-md-12">
    <h5>
        <img src="@asset('/assets/images/workspace/list.png')" class="icon-image">
        @lang('Listado de documentos')
    </h5>
</div>

<div v-if="canUploadDocuments" class="col-md-12 main-card card mb-4">
    <div class="card-body table-responsive">
        <table id="files-table" class="table">

            <thead>
                @include('common.passport.header-documents-table')
            </thead>

            {{-- Cuando no hay elementos --}}
            <tbody v-if="!passports.length">
                <tr>
                    <td colspan="4" class="text-center text-danger bold">@lang('No hay ningún documento')</td>
                </tr>
            </tbody>
            {{-- / Cuando no hay elementos --}}
            
            <tbody v-else>
                <tr v-for="(passport,index) in passports" :key="index">
                    <td data-label="@lang('Documento')" >@{{passport.number}}</td>
                    <td data-label="@lang('Tipo')">@{{getDocumentTypeName(passport.type)}}</td>
                    <td data-label="@lang('Anverso')">
                        <img class="passport-thumb" :src="passport.front" alt="" />
                    </td>
                    <td data-label="@lang('Reverso')">
                        <img class="passport-thumb" :src="passport.back" alt="" />
                    </td>
                    <td data-label="@lang('Eliminar')" class="text-center">
                        <button @click="removePassport(index)" type="button" class="btn btn-danger square">
                            <i class="fa fa-trash fa-2x"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        
            <tfoot>
                @include('common.passport.header-documents-table')
            </tfoot>

        </table>
    </div>
</div>
{{--/Listado de documentos de identificación adjuntos --}}