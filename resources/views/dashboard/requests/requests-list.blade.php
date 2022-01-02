{{-- Listado de documentos que se han requerido --}}
<div class="col-sm-12">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">@lang('Listado de documentos requeridos')</h5>

            <div class="table-responsive">    
                <table class="mb-0 table table-striped">
                    <thead>
                        {{--Header de la tabla--}}
                        @include('dashboard.requests.header-requests-list')
                        {{--/ Header de la tabla--}}
                    </thead>
                    <tbody>

                        <tr v-if="!documents.length">
                            <td colspan="6" class="text-center text-danger bold">
                                @lang('Ningún documento requerido')
                            </td>
                        </tr>
                         
                        <tr v-else v-for="document in documents" :key="document.id">

                            <td data-label="@lang('Documento')">
                                @{{document.name}}
                            </td>

                            <td data-label="@lang('Comentario')">
                                @{{document.comment}}
                            </td>

                            <td data-label="@lang('Tipo de Archivo')">
                                @{{document.type}}
                            </td>

                            <td data-label="@lang('Validez')">
                                @{{ showValidity(document) }}
                            </td>

                            <td data-label="@lang('Tamaño')">
                                @{{ getFileSize(document.maxsize) }}
                            </td>

                            <td data-label="@lang('Fecha Vencimiento')">
                                <span class="bg-warning text-white p-2" v-if="document.has_expiration_date">
                                    @lang('Solicitada')
                                </span>
                            </td>
                             
                            <td class="text-center">

                                {{-- Modificar la solicitud de documento al firmante--}}
                                <button class="btn btn-sm btn-warning square"
                                    @click.prevent="editRequest(document.name)">
                                    <i class="fa fa-edit"></i>
                                </button>
                                {{-- /Modificar la solicitud de documento al firmante--}}

                                {{-- Eliminar la solicitud de documento al firmante--}}
                                <button class="btn btn-sm btn-danger square"
                                    @click.prevent="removeRequest(document.name)">
                                    <i class="fa fa-trash"></i>
                                </button>
                                {{-- /Eliminar la solicitud de documento al firmante--}}
                                 
                            </td>

                        </tr>
                         
                    </tr>
                    </tbody>
                    <tfoot>
                        {{--Header de la tabla--}}
                        @include('dashboard.requests.header-requests-list')
                        {{--/ Header de la tabla--}}
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
{{-- / Listado de documentos que se han requerido --}}