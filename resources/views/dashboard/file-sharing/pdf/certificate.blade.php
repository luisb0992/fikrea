@extends('common.layouts.certificate')

@section('page-header')
    <div>
        @lang('Informe acreditativo de proceso de historial de acceso a compartición') #{{ $sharing->id }}
        [ <span class="bold">@locale</span> ]
    </div>
@endsection

@section('document-guid')
    #{{$sharing->id}}-{{$sharing->title}}-{{$sharing->description}}
@endsection

@section('document-goals')
    @lang('El objeto de este certificado es proporcionar la información necesaria sobre el acceso a una compartición.')
@endsection

@section('document-data')
    <div style="font-size: 9px;">
        <table class="mb-0 table table-striped" id="history-datatable">
            <thead>
            <tr>
                <th style="width: 10%;font-size:9px;">@lang('Fecha')</th>
                <th style="width: 10%;font-size:9px;">@lang('Acción')</th>
                <th style="width: 15%;font-size:9px;">@lang('Destinatario')</th>
                <th style="width: 10%;font-size:9px;">@lang('Ip')</th>
                <th style="width: 18%;font-size:9px;">@lang('Sistema')</th>
                <th style="width: 17%;font-size:9px;" class="text-center">
                    @lang('Ubicación Aproximada') <span>*</span>
                </th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            @forelse( $sharing->histories as $history )
                <tr>
                    <td data-label="@lang('Fecha')" style="font-size:9px;">
                        @if ( $history->starts_at )
                            @datetime( $history->starts_at )
                        @elseif ( $history->downloaded_at )
                            @datetime( $history->downloaded_at )
                        @endif
                    </td>
                    <td data-label="@lang('Acción')" style="font-size:9px;">
                        @if ( $history->starts_at )
                            <span class="text-secondary">@lang('Acceso')</span>
                        @elseif ( $history->downloaded_at )
                            <span class="text-success">@lang('Descarga')</span>
                        @endif
                    </td>
                    <td data-label="@lang('Destinatario')" style="font-size:9px;">
                        @if ( $history->contact )
                            {{ $history->contact->name }} {{ $history->contact->lastname }}
                            <div>
                                @if ( $history->contact->email )
                                    <a href="mailto:{{ $history->contact->email }}">{{ $history->contact->email }}</a>
                                @elseif ( $history->contact->phone )
                                    <a href="tel:{{ $history->contact->phone }}">{{ $history->contact->phone }}</a>
                                @endif
                            </div>
                        @else
                            @lang('Anónimo')
                        @endif
                    </td>
                    <td data-label="@lang('Ip')" style="font-size:9px;">
                        {{ $history->ip }}
                    </td>
                    <td data-label="@lang('Sistema')" style="font-size:9px;">
                        @useragent( $history->user_agent )
                    </td>
                    <td class="text-center" style="font-size:9px;">
                        @if ( $history->position->country && $history->position->city )
                            <div class="row">
                                <div class="col-md-6 col-s-12">
                                    <div style="font-size:9px;">
                                        {{ $history->position->city }} {{ $history->position->region }}
                                    </div>
                                    <div>
                                        <strong style="font-size:9px;">{{ $history->position->country }}</strong>
                                    </div>
                                </div>
                                @if ( $history->position->latitude && $history->position->longitude )
                                    <div class="col-md-6 col-s-12">
                                        <a target="_blank" class="btn btn-primary square" style="font-size:9px;"
                                           href="https://www.google.com/maps/search/?api=1&query={{ $history->position->latitude }},{{ $history->position->longitude }}">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center">
                                <div class="bg-danger text-white p-2" style="font-size:9px;">
                                    @lang('No se ha obtenido')
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="font-size:9px;">@lang('Ningún elemento registrado')</td>
                </tr>
            @endforelse
            <tfoot>
            <tr>
                <th style="width: 10%;font-size:9px;">@lang('Fecha')</th>
                <th style="width: 10%;font-size:9px;">@lang('Acción')</th>
                <th style="width: 15%;font-size:9px;">@lang('Destinatario')</th>
                <th style="width: 10%;font-size:9px;">@lang('Ip')</th>
                <th style="width: 18%;font-size:9px;">@lang('Sistema')</th>
                <th style="width: 17%;font-size:9px;" class="text-center">
                    @lang('Ubicación Aproximada') <span>*</span>
                </th>
            </tr>
            </tfoot>
        </table>
    </div>
@endsection

@section('first-attachment-customized')
    <x-certificates.certificate-attach-i :user="$sharing->user" :created_at="$sharing->created_at">
        <p>
            @lang('La descarga de todos los documentos que se aportaron durante este proceso pueden ser descargados
                suministrando las credenciales de acceso a la aplicación en la dirección'):
        </p>
        <p>
            <a class="btn btn-success square" href="@route('file.set.download', ['id' => $sharing->id])">
                @route('file.set.download', ['id' => $sharing->id])
            </a>
        </p>
    </x-certificates.certificate-attach-i>
@endsection
