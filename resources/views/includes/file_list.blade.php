{{-- Lista de archivos que se van a compartir --}}
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-body"><h5 class="card-title">@lang('Lista de Archivos')</h5>
            <div class="table-responsive">
                <table class="mb-0 table table-striped" style="text-align: inherit !important;">
                    <thead>
                    <tr class="d-flex">
                        <th class="col-1">#</th>
                        <th class="col-4">@lang('Nombre')</th>
                        <th class="col-1">@lang('Tipo')</th>
                        <th class="col-1 text-center">@lang('Tamaño')</th>
                        <th class="col-4">@lang('Carpeta')</th>
                        <th class="col-1">@lang('Creado')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $files->take(10) as $file )
                        <tr class="d-flex">
                            <th class="col-1" data-label="@lang('Archivo') #">{{ $loop->iteration }}</th>
                            <td class="col-4"><a href="#">{{ $file->name }}</a></td>
                            <td class="col-1" data-label="@lang('Tipo')">
                                @include('dashboard.partials.file-icon', ['type' => $file->type])
                            </td>
                            <td class="col-1 text-center" data-label="@lang('Tamaño')">@filesize( $file->size )</td>
                            <td class="col-4">{{ implode('/', $file->full_path ?? []) }}</td>
                            <td class="col-1 align-middle text-nowrap" data-label="@lang('Creado')">
                                @date( $file->created_at )
                                <div class="text-info">@time( $file->created_at )</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if ( $files->count() > 10 )
                    <div class="text-center mb-1 mt-1" @click.prevent="showAllFiles">
                        <button class="btn btn-primary">
                            + ( {{$files->count()-10}} ) @lang('archivos')
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
{{--/ Lista de archivos que se van a compartir --}}
