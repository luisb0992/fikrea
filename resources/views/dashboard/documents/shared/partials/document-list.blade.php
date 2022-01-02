{{-- Lista de documentos que se van a compartir --}}
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">@lang('Lista de Documentos')</h5>
            <h6 class="text-muted">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> @lang('Se compartirá el documento, acompañado de los archivos que
                    se han utilizado en la validación, el certificado generado y el archivo original (sin firmar)')
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </h6>
            <div class="table-responsive">
                <table class="mb-0 table table-striped" style="text-align: inherit !important;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('Nombre')</th>
                            <th>@lang('Tipo')</th>
                            <th>@lang('Páginas')</th>
                            <th>@lang('Tamaño')</th>
                            <th>@lang('Creado')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents->take(10) as $document)
                            <tr>
                                <th data-label="@lang('Documento') #">{{ $loop->iteration }}</th>
                                <td data-label="@lang('Nombre')"><a href="#">{{ $document->name }}</a></td>
                                <td data-label="@lang('Tipo')">
                                    @include('dashboard.partials.file-icon', ['type' => $document->type])
                                </td>
                                <td data-label="@lang('Páginas')">{{ $document->pages }}</td>
                                <td data-label="@lang('Tamaño')">@filesize($document->size)</td>
                                <td class="align-middle text-nowrap" data-label="@lang('Creado')">
                                    @date( $document->created_at )
                                    <div class="text-info">@time( $document->created_at )</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($documents->count() > 10)
                    <div class="text-center mb-1 mt-1">
                        <button class="btn btn-primary" @click.prevent="showAllDocuments">+ ( {{ $documents->count() - 10 }} ) @lang('documentos')</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
{{-- / Lista de documentos que se van a compartir --}}
