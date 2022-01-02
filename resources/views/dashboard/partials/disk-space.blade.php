{{--
    Muestra el espacio ocupado por los archivos de un usuario

--}}


{{-- El espacio de almacenamiento disponible --}}
<input type="hidden" id="free-disk-space" value="{{$diskSpace->free}}" />  
<input type="hidden" id="used-disk-space" value="{{$diskSpace->usedByFiles}}" />  
<input type="hidden" id="available-disk-space" value="{{$diskSpace->available}}" />  
{{-- El espacio de almacenamiento disponible --}}

{{-- Mensaje de Alerta cuando el espacio disponible para el almacenamiento está llegando o ha llegado a su fin --}}
@if ($diskSpace->getUsedPercentage() > 85)
<div class="col-md-12 mt-2 alert alert-danger text-right bold">
    <i class="fas fa-exclamation-triangle"></i>
    @if ($diskSpace->getUsedPercentage() == 100)
        @lang('No dispone de más espacio de almacenamiento. Puede obtener más espacio subscribiéndose a un plan superior.')
    @else
        @lang('El almacenamiento disponible está apunto llenarse. Puede obtener más espacio subscribiéndose a un plan superior.')
    @endif

    @auth
    <div class="text-right mt-2">
        <a href="@route('subscription.select')" class="btn btn-lg btn-danger">
            <i class="fas fa-shopping-cart"></i>
            @lang('Ampliar Subscripción') 
        </a>
    </div>
    @else
    <div class="text-right mt-2">
        <a href="@route('dashboard.register')" class="btn btn-lg btn-danger">
            <i class="fas fa-user-check"></i>
            @lang('Completar Registro') 
        </a>
    </div>
    @endauth
</div>
@endif
{{--/Mensaje de Alerta cuando el espacio disponible para el almacenamiento está llegando o ha llegado a su fin --}}

{{-- Espacio Ocupado --}}
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">
            @lang('Espacio Ocupado')
        </h5>
        <div class="col-md-12 text-right">
            @filesize($diskSpace->used) / @filesize($diskSpace->available)
            [
                <span class="text-primary bold">
                    <span id="used-value" >{{$diskSpace->getUsedPercentage()}}</span> %
                </span>
            ]
        </div>

        <div class="progress">
            
            <div id="used-files" class="progress-bar progress-bar-animated bg-danger progress-bar-striped" role="progressbar" 
                aria-valuenow="{{$diskSpace->usedByFiles}}" aria-valuemin="0" aria-valuemax="{{$diskSpace->available}}" 
                style="width: {{$diskSpace->usedByFiles * 100 / $diskSpace->available}}%;">
            </div>
            
            <div id="udes-documents" class="progress-bar progress-bar-animated bg-warning progress-bar-striped" role="progressbar" 
                aria-valuenow="{{$diskSpace->usedByDocuments}}" aria-valuemin="0" aria-valuemax="{{$diskSpace->available}}"  
                style="width: {{$diskSpace->usedByDocuments * 100 / $diskSpace->available}}%;">
            </div>

            <div id="used-uploads" class="progress-bar progress-bar-animated bg-info progress-bar-striped" role="progressbar" 
                aria-valuenow="{{$diskSpace->usedByUploads}}" aria-valuemin="0" aria-valuemax="{{$diskSpace->available}}" 
                style="width: {{$diskSpace->usedByUploads * 100 / $diskSpace->available}}%;">
            </div>

        </div>
        
        <div class="mt-2">
            <span class="text-danger" title="@lang('Espacio ocupado por los archivos subidos')"><i class="fas fa-square"></i></span> @lang('archivos')
            <span class="text-warning" title="@lang('Espacio ocupado por los documentos para firma')"><i class="fas fa-square"></i></span> @lang('documentos')
            <span class="text-info" title="@lang('Espacio ocupado por los archivos subidos por los usuarios')"><i class="fas fa-square"></i></span> @lang('usuarios')
        </div>
    </div>
</div>
{{--/Espacio Ocupado --}}