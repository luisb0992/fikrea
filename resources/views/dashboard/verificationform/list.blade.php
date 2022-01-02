@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- La ayuda visual --}}
@section('help')
    <div>
        @lang('Estas son las verificaciónes de datos que ha enviado')
        <div class="page-title-subheading">
            <div>
                @lang('Para verificar el estado actual de una verificación puede usar el botón')
                <i class="fas fa-thermometer-half"></i>
            </div>
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
                <h5 class="card-title">@lang('Verificación de datos')</h5>

                {{-- link para ir a una nueva verificación de datos --}}
                <div class="col-md-12 mb-2">
                    <a href="@route('dashboard.verificationform.edit')" class="btn btn-lg btn-primary square mt-1"
                        title="@lang('Crear nueva verificación de datos')">
                        <i class="far fa-newspaper"></i>
                        @lang('Nueva Verificación De Datos')
                    </a>
                </div>

                <div class="table-responsive col-md-12">
                    <table class="mb-0 table table-hover">
                        <thead>
                            @include('dashboard.verificationform.partials.header-table-list')
                        </thead>
                        <tbody>
                            @forelse ($verificationForm as $form)
                                <tr>
                                    <td data-label="@lang('Numero') #">{{ $loop->iteration }}</td>

                                    <td data-label="@lang('Nombre')">
                                        @if ($form->name)
                                            {{ $form->name }}
                                        @else
                                            <span class="text-info">@lang('verificación de datos')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Comentario')">
                                        @if ($form->comment)
                                            {!! $form->comment !!}
                                        @else
                                            <span class="text-info">@lang('Sin comentarios')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Usuario')">
                                        @foreach ($form->signers as $signer)
                                            <div>
                                                {{ $signer->lastname }} {{ $signer->name }}
                                                @if ($signer->email)
                                                    <a href="mailto:{{ $signer->email }}"> {{ $signer->email }}</a>
                                                @else
                                                    <a href="tel:{{ $signer->phone }}"> {{ $signer->phone }}</a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>

                                    {{-- Progreso de la solicitud, animado cuando se está atendiendo por un firmante --}}
                                    <td data-label="@lang('Progreso')">
                                        @if ($form->isActive())
                                            <div class="progress" style="height: 10px;" data-toggle="tooltip"
                                                data-html="true" data-original-title="{{ $form->getActivity() }}">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info w-100"
                                                    role="progressbar"></div>
                                            </div>
                                            <div class="text-secundary animated infinite pulse text-center">
                                                {!! $form->getActivity() !!}
                                            </div>
                                        @else
                                            <progress min="0" max="100" value="{{ $form->progress }}"></progress>
                                            <div class="text-center bold">{{ $form->progress }} %</div>
                                        @endif
                                    </td>
                                    {{-- /Progreso de la solicitud, animado cuando se está atendiendo por un firmante --}}

                                    <td data-label="@lang('Creada')">@datetime($form->created_at)</td>

                                    <td class="text-center">

                                        {{-- Estado --}}
                                        <a href="@route('dashboard.verificationform.status', ['id' => $form->id])"
                                            class="btn btn-primary square" data-toggle="tooltip"
                                            data-original-title="@lang('Consultar estado de la verificación')">
                                            <i class="fas fa-thermometer-half"></i>
                                        </a>

                                        {{-- Histórico en verificación de datos --}}
                                        @if ($form->visits->isEmpty())
                                            <a href="#!" class="btn btn-warning square" data-toggle="tooltip"
                                                data-original-title="@lang('Ninguna visita registrada')">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        @else
                                            <a href="@route('dashboard.verificationform.history', ['id' => $form->id])"
                                                class="btn btn-warning square" data-toggle="tooltip"
                                                data-original-title="@lang('Histórico de la verificación')">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        {{-- / Histórico en verificación de datos --}}

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-danger bold">@lang('Ninguna solicitud')</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            @include('dashboard.verificationform.partials.header-table-list')
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- /El listado de verificación de datoss --}}

        {{-- Control de la Tabla --}}
        <div class="control-wrapper">
            {{-- Paginador --}}
            @if ($verificationForm->total() > config('documents.pagination'))
                <div class="paginator-wrapper">
                    {{ $verificationForm->links() }}

                    @lang('Se muestran :rows de un total de :total archivos', [
                    'rows' => $verificationForm->count(),
                    'total' => $verificationForm->total(),
                    ])

                </div>
            @endif
            {{-- /Paginador --}}
        </div>
        {{-- /Control de la Tabla --}}

    </div>

@stop