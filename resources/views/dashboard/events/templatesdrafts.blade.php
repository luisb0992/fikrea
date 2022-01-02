@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- La ayuda visual --}}
@section('help')
    <div>
        @lang('Estos son los eventos que ha creado como borradores y las plantillas guardadas')
        <div class="page-title-subheading">
            {{-- <div>
                @lang('Para verificar el estado actual de una verificación puede usar el botón')
                <i class="fas fa-thermometer-half"></i>
            </div> --}}
        </div>
    </div>
@stop

{{-- El contenido de la pagina --}}
@section('content')

    {{-- El mensaje flash que se muestra cuando la operación ha tenido éxito o error --}}
    <div class="offset-md-3 col-md-6">
        @include('dashboard.sections.body.message-success')
        @include('dashboard.sections.body.message-error')
    </div>
    {{-- /El mensaje flash que se muestra cuando la operacion ha tenido éxito o error --}}

    <div id="app" class="col-md-12">

        {{-- Uso del espacio disponible --}}
        @include('dashboard.partials.disk-space')
        {{-- /Uso del espacio disponible --}}

        {{-- El listado de verificación de datoss --}}
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">@lang('Borradores de eventos')</h5>

                <div class="table-responsive col-md-12">
                    <table class="mb-0 table table-hover">
                        <thead>
                            @include('dashboard.events.partials.templatesdrafts.header-table-list')
                        </thead>
                        <tbody>
                            @forelse ($events as $event)
                                <tr>
                                    <td data-label="@lang('Numero') #">{{ $loop->iteration }}</td>

                                    <td data-label="@lang('Título')">{{ $event->title }}</td>

                                    <td data-label="@lang('Descripción')">{{ $event->formatdescription }}</td>

                                    <td data-label="@lang('Tipo')">
                                        @include('dashboard.events.partials.event-types', [
                                        'type' => $event->type
                                        ])
                                    </td>

                                    <td data-label="@lang('Fecha inicio')">@datetime($event->start_date)</td>

                                    <td data-label="@lang('Fecha cierre')">
                                        @if ($event->end_date)
                                            @datetime($event->end_date)
                                        @else
                                            @lang('No definida')
                                        @endif
                                    </td>

                                    <td class="text-center">

                                        @if ($event->image)
                                            @if($event->image->image)
                                                <img class="img-fluid" src="data:image/*;base64,{{ $event->image->image }}" style="max-width: 20%; max-height: 400px;" />
                                                @else
                                                <b-img src="{{ $event->image->url }}" fluid alt="event-header" style="max-width: 20%; max-height: 400px;">
                                                </b-img>
                                            @endif
                                        @endif

                                        @if ($event->video)
                                            @if($event->video->video)
                                                <video src="data:video/*;base64,{{ $event->video->video }}" controls style="max-width: 20%; max-height: 400px;"></video>
                                                @else
                                                <video-embed src="{{ $event->video->url }}" style="max-width: 20%; max-height: 400px;"></video-embed>
                                            @endif
                                        @endif
                                        {{-- <a href="@route()"
                                            class="btn btn-primary square" data-toggle="tooltip"
                                            data-original-title="@lang('Consultar estado de la verificación')">
                                            <i class="fas fa-thermometer-half"></i>
                                        </a> --}}

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-danger bold">@lang('No hay borradores')</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            @include('dashboard.events.partials.templatesdrafts.header-table-list')
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- /El listado de verificación de datoss --}}

        {{-- Control de la Tabla --}}
        <div class="control-wrapper">
            {{-- Paginador --}}
            @if ($events->total() > config('documents.pagination'))
                <div class="paginator-wrapper">
                    {{ $events->links() }}

                    @lang('Se muestran :rows de un total de :total archivos', [
                    'rows' => $events->count(),
                    'total' => $events->total(),
                    ])

                </div>
            @endif
            {{-- /Paginador --}}
        </div>
        {{-- /Control de la Tabla --}}

    </div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/events/templates-drafts.js')"></script>
@stop