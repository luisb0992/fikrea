@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

    {{-- El encabezado con la ayuda para la página --}}
@section('help')
    <div>
        @lang('Se muestra la lista de visitas realizadas sobre la compartición de un documento')
        <div class="page-title-subheading">
            @lang('También la cantidad de descargas de la compartición por destinatario.')
        </div>
    </div>
@stop

{{-- El contenido de la página --}}
@section('content')

    <div class="col-md-12">

        {{-- Listado de visitas de los destinatarios y usuarios --}}
        <div class="col-md-12 mb-5 mt-2">
            <div class="main-card card">
                <h5 class="px-4 pt-4">
                    <i class="fas fa-history text-info"></i>
                    @lang('Historial de Accesos y Descargas')
                </h5>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="mb-0 table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Fecha')</th>
                                    <th>@lang('Acción')</th>
                                    <th>@lang('Destinatario')</th>
                                    <th>@lang('Ip')</th>
                                    <th>@lang('Sistema')</th>
                                    <th class="text-center">@lang('Ubicación Aproximada') *</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentSharing->histories as $history)
                                    <tr>
                                        <td data-label="@lang('Fecha')">
                                            @if ($history->starts_at)
                                                @datetime($history->starts_at)
                                            @elseif ($history->downloaded_at)
                                                @datetime($history->downloaded_at)
                                            @endif
                                        </td>
                                        <td data-label="@lang('Acción')">
                                            @if ($history->starts_at)
                                                <span class="text-secondary">@lang('Acceso')</span>
                                            @elseif ($history->downloaded_at)
                                                <span class="text-success">@lang('Descarga')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Destinatario')">
                                            @if ($history->contact)
                                                {{ $history->contact->name }} {{ $history->contact->lastname }}
                                                <div>
                                                    @if ($history->contact->email)
                                                        <a
                                                            href="mailto:{{ $history->contact->email }}">{{ $history->contact->email }}</a>
                                                    @elseif ($history->contact->phone)
                                                        <a
                                                            href="tel:{{ $history->contact->phone }}">{{ $history->contact->phone }}</a>
                                                    @endif
                                                </div>
                                            @else
                                                @lang('Anónimo')
                                            @endif
                                        </td>
                                        <td data-label="@lang('Ip')">
                                            {{ $history->ip }}
                                        </td>
                                        <td data-label="@lang('Sistema')">
                                            @useragent($history->user_agent)
                                        </td>
                                        <td class="text-center" data-label="@lang('Ubicación Aproximada') *">
                                            @if ($history->position->country && $history->position->city)
                                                <div class="row">
                                                    <div class="col-md-6 col-s-12">
                                                        <div>
                                                            {{ $history->position->city }}
                                                            {{ $history->position->region }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $history->position->country }}</strong>
                                                        </div>
                                                    </div>
                                                    @if ($history->position->latitude && $history->position->longitude)
                                                        <div class="col-md-6 col-s-12">
                                                            <a target="_blank" class="btn btn-primary square"
                                                                href="https://www.google.com/maps/search/?api=1&query={{ $history->position->latitude }},{{ $history->position->longitude }}">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                            </a>
                                                        </div>
                                                    @else

                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <div class="bg-danger text-white p-2">@lang('No se ha obtenido')</div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">@lang('Ningún elemento registrado')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>@lang('Fecha')</th>
                                    <th>@lang('Acción')</th>
                                    <th>@lang('Destinatario')</th>
                                    <th>@lang('Ip')</th>
                                    <th>@lang('Sistema')</th>
                                    <th class="text-center">@lang('Ubicación Aproximada') *</th>
                                </tr>
                            </tfoot>
                            <caption class="text-right text-secondary small">
                                <span class="bold">*</span> @lang('Ubicación aproximada en relación a la dirección ip
                                utilizada en la conexión')
                            </caption>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- / Listado de visitas de los destinatarios y usuarios --}}

    @stop
