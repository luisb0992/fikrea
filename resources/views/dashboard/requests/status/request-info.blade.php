{{-- Información de la solicitud de documentos --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-info text-info"></i>
        @lang('Información de la Solicitud')
    </h5>
</div>

<div class="main-card card">

    <div class="card-body">
            
        <div class="col-md-12">

            {{-- Nombre y fecha de la solicitud --}}
            <div class="row">
                <div class="col-md-10">
                    <label for="name" class="bold">@lang('Nombre')</label>
                    <input id="name" type="text" value="{{$request->name}}" class="form-control" readonly />
                </div>

                <div class="col-md-2">  
                    <label for="created" class="bold">@lang('Creada')</label>
                    <div class="input-group">
                        <input id="created" type="text" value="@date($request->created_at)" class="form-control" readonly />
                        <div class="input-group-prepend">
                            <i class="btn btn-secondary fa fa-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
            {{--/Nombre y fecha de la solicitud --}}

            {{-- Comentarios de ls solicitud --}}
            <div class="row">
                <div class="col-md-12 mt-4">
                    <label for="comment" class="bold">@lang('Comentarios')</label>
                    <textarea class="form-control" id="comment" rows="4" readonly>{!!$request->comment!!}</textarea>
                </div>
            </div>
            {{--/Comentarios de la solicitud --}}

            {{-- Lista de documentos requeridos en la solicitud --}}
            <div class="row">

                <div class="col-md-12 mt-4">
                    <label for="documents" class="bold">@lang('Documentos Requeridos')</label>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Nombre')</th>
                                    <th>@lang('Comentarios')</th>
                                    <th>@lang('Tipo')</th>
                                    <th>@lang('Tamaño')</th>
                                    <th>@lang('Validez')</th>
                                    <th>@lang('Fecha Vencimiento')</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($request->documents as $document)
                                <tr>
                                    <td data-label="@lang('Nombre')" class="text-primary">
                                        {{$document->name}}
                                    </td>
                                    <td data-label="@lang('Comentarios')" class="text-secondary">
                                        {{$document->comment}}
                                    </td>
                                    <td data-label="@lang('Tipo')">
                                        @if ($document->type)
                                            <span class="bg-warning text-white p-2">
                                                @lang( $document->mimeType->description ?? $document->type )
                                            </span>
                                        @else
                                            <span class="text-success">@lang('Cualquiera')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Tamaño')">
                                        @if ($document->maxsize)
                                            <span class="bg-danger text-white p-2">@filesize($document->maxsize)</span>
                                        @else
                                            <span class="text-success">@lang('Cualquiera')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Validez')">
                                        @if ($document->issued_to)
                                            <div>
                                                {{$document->validity}}
                                                @switch ($document->validity_unit)
                                                    @case (\App\Enums\TimePeriod::DAY)
                                                        @lang('días')
                                                        @break
                                                    @case (\App\Enums\TimePeriod::MONTH)
                                                        @lang('meses')
                                                        @break
                                                    @case (\App\Enums\TimePeriod::YEAR)
                                                        @lang('años')
                                                        @break
                                                @endswitch
                                            </div>
                                            <div class="text-danger">
                                                <i class="fas fa-greater-than"></i>
                                                @date($document->issued_to)
                                            </div>
                                       
                                        @else
                                            <span class="text-success">@lang('Cualquiera')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Vence')">
                                        @if ($document->has_expiration_date)
                                            <span class="text-danger bold">
                                                @lang('Solicitada')
                                            </span>
                                        @else
                                            <span>
                                                @lang('Sin solicitar')
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fa fa-check fa-2x text-success"></i>    
                                    </td> 
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>@lang('Nombre')</th>
                                    <th>@lang('Comentarios')</th>
                                    <th>@lang('Tipo')</th>
                                    <th>@lang('Tamaño Máximo')</th>
                                    <th>@lang('Validez')</th>
                                    <th>@lang('Fecha Vencimiento')</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            {{--/Lista de documentos requeridos en la solicitud --}}

        </div>
    </div>
</div>
{{--/Información de la solicitud de documentos --}}