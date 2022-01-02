@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- Css Personalizado --}}
@section('css')
@stop

{{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Se muestra la lista con las comparticiones de documentos realizadas')
        <div class="page-title-subheading">
            @lang('Cada conjunto de documentos posee un enlace de descarga que se envío a los destinatarios')
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div v-cloak id="app" class="col-md-12">
        <h5 class="card-title">
            @lang('Documentos Compartidos')
        </h5>

        {{-- Mensajes de la aplicación --}}
        <div id="message" data-share-title="@config('app.name')"
            data-share-text="@lang('Se ha copiado la dirección de descarga del documento')">
        </div>
        {{-- /Mensajes de la aplicación --}}

        <div class="main-card mb-3 card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="mb-0 table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('Documento')</th>
                                <th>@lang('Tipo')</th>
                                <th>@lang('Páginas')</th>
                                <th>@lang('Tamaño') (kB)</th>
                                <th>@lang('Destinatarios')</th>
                                <th>@lang('Creado')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documentSharings as $documentSharing)
                                <tr>
                                    <td data-label="#">{{ $loop->iteration }}</td>
                                    <td data-label="@lang('Documento')" class="text-info">
                                        {{ $documentSharing->document->name }}</td>
                                    <td data-label="@lang('Tipo')">
                                        @include('dashboard.partials.file-icon', ['type' =>
                                        $documentSharing->document->type])
                                    </td>
                                    <td data-label="@lang('Páginas')">{{ $documentSharing->document->pages }}</td>
                                    <td data-label="@lang('Tamaño')">@filesize($documentSharing->document->size)</td>
                                    <td data-label="@lang('Destinatarios')">
                                        <ul class="list-unstyled">
                                            @foreach ($documentSharing->contacts as $contact)
                                                <li>
                                                    <i class="fas fa-user-alt text-info"></i>
                                                    @if ($contact->email)
                                                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                                    @else
                                                        <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td data-label="@lang('Creado')">@datetime($documentSharing->created_at)</td>

                                    <td class="text-center">
                                        {{-- Descarga del documento --}}
                                        <a href="@route('workspace.document.shared.download', ['token' => $documentSharing->token])"
                                            class="btn btn-info square" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('Descargar')">
                                            <i class="fas fa-file-download"></i>
                                        </a>

                                        {{-- Ruta para compartir el documento con otros usuarios --}}
                                        <a href="#"
                                            @click.prevent="share('@route('workspace.document.share', ['token' => $documentSharing->token])')"
                                            class="btn btn-success square" data-toggle="tooltip" data-placement="top"
                                            data-original-title="@lang('Copiar URL')">
                                            <i class="fas fa-copy"></i>
                                        </a>

                                        {{-- Ver histórico de visitas y descargas de la compartición --}}
                                        @if ($documentSharing->histories->isEmpty())
                                            <a href="#!" class="btn btn-warning square" data-toggle="tooltip"
                                                data-placement="top"
                                                data-original-title="@lang('Ninguna visita registrada')">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        @else
                                            <a href="@route('dashboard.history.document.sharings', ['id' => $documentSharing->id])"
                                                class="btn btn-warning square" data-toggle="tooltip" data-placement="top"
                                                data-original-title="@lang('Histórico de descargas')">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">@lang('Ningún elemento registrado')</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>@lang('Documento')</th>
                                <th>@lang('Tipo')</th>
                                <th>@lang('Páginas')</th>
                                <th>@lang('Tamaño') (kB)</th>
                                <th>@lang('Destinatarios')</th>
                                <th>@lang('Creado')</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Paginador --}}
                @if (count($documentSharings))
                    <div class="paginator-wrapper mt-1">
                        {{ $documentSharings->links() }}

                        @lang('Se muestran :files de un total de :total archivos.', [
                        'files' => $documentSharings->count(),
                        'total' => $documentSharings->total(),
                        ])
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')

{{-- este archivo aplica tanto para archivos como para documentos --}}
<script src="@mix('assets/js/dashboard/files/file-share-list.js')"></script>
@stop
