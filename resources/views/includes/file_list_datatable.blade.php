{{-- Lista de los archivos que se van a compartir --}}
<div class="col-md-12">
    <div class="mb-3 card" style="width: 100%;">
        <h5 class="card-header">@lang('Lista de Archivos')</h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="mb-0 table table-striped table-hover responsive row-border"
                       style="width:100%; text-align: inherit !important;" id="static-data-datatable">
                    <thead>
                    <tr>
                        <th class="col-name min-tablet-l" style="width: 35%;">
                            <span class="d-none d-lg-block">@lang('Nombre')</span>
                        </th>
                        <th class="col-folder min-tablet-l" style="width: 30%;">@lang('Carpeta')</th>
                        <th class="col-type min-tablet-l" style="width: 10%;">@lang('Tipo')</th>
                        <th class="col-size min-tablet-l" style="width: 10%;" class="text-center">@lang('Tamaño')</th>
                        <th class="col-created_at min-tablet-l" style="width: 10%;">@lang('Creado')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $files as $file )
                        <tr>
                            <td data-label="@lang('Archivo')" id="first-column"
                                data-first-column-name="@lang('Archivo')">
                                    <span class="d-md-inline d-lg-none mr-3"
                                          style="display: inline-block; min-width: 75px; font-weight: bold;">
                                        @lang('Archivo')
                                    </span>
                                <button class="btn" @click.prevent="preview({{ $file->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="javascript:void(0)" title="{{ $file->name }}">{{ $file->name }}</a>
                            </td>
                            <td>
                                @if( null !== $file->full_path)
                                    {{ implode('/', $file->full_path ?? []) }}
                                @else
                                    @lang('PRINCIPAL')
                                @endif
                            </td>
                            <td data-label="@lang('Tipo')">
                                @include('dashboard.partials.file-icon', ['type' => $file->type])
                            </td>
                            <td class="text-center" data-label="@lang('Tamaño')">@filesize( $file->size )</td>
                            <td class="align-middle text-nowrap" data-label="@lang('Creado')">
                                @date( $file->created_at )
                                <div class="text-info">@time( $file->created_at )</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{--/ Lista de archivos que se van a compartir --}}