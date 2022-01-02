{{-- La lista de documentos solicitados --}}
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">
            @lang('Documentos Solicitados')
        </h5>

        <div>
            <span class="bold">@lang('Nombre de la Solicitud')</span> : 
            <span class="text-primary">{{$request->name}}</span>
        </div>
        <div class="text-secondary">
            {!!$request->comment!!}
        </div>
        
        <div class="table-responsive mt-2">
            <table class="table">
                <thead>
                    @include('common.request.header-required-documents-list')
                </thead>
                <tbody>
                    @foreach ($request->documents as $document)
                    <tr>
                        <td data-label="@lang('Documento')">
                            {{-- Si ya se ha aportado o no --}}
                            <i v-if="documentIsAlreadyAported('{{$document->name}}')"
                                class="far check fa-check-square fa-2x text-success"
                            ></i>
                            <i v-else class="far check fa-square fa-2x text-warning"></i>
                            {{-- /Si ya se ha aportado o no --}}
                        </td>
                        <td data-label="@lang('Nombre')">
                            {{$document->name}}
                        </td>
                        <td data-label="@lang('Comentarios')" class="text-secondary">
                            {{$document->comment}}
                        </td>
                        <td data-label="@lang('Tipo')">
                            @if ($document->type)
                                <span class="text-info">
                                    @lang( $document->mimeType->description ?? $document->type )
                                </span>
                            @else
                                <span class="text-success">@lang('Cualquiera')</span>
                            @endif
                        </td>
                        <td data-label="@lang('Tamaño')">
                            @if ($document->maxsize)
                                <span class="text-info">@filesize($document->maxsize)</span>
                            @else
                                <span class="text-success">@lang('Cualquiera')</span>
                            @endif
                        </td>
                        <td data-label="@lang('Validez')">
                            @if ($document->issued_to)
                                <div>
                                    {{$document->validity}}
                                    {{ (string) \App\Enums\TimePeriod::fromValue($document->validity_unit) }}
                                </div>
                                <div class="text-danger text-nowrap">
                                    <i class="fas fa-greater-than"></i>
                                    @date($document->issued_to)
                                </div>
                            @else
                                <span class="text-success">@lang('Cualquiera')</span>
                            @endif
                        </td>
                        <td data-label="@lang('Fecha de vencimiento')">
                            @if ($document->has_expiration_date)
                            <span class="text-warning">@lang('Solicitada')</span>
                            @else
                            <span class="text-success">@lang('No solicitada')</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>   
{{--/La lista de documentos solicitados --}}
