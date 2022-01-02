{{--
    Muestra una lista de sms que se han enviado en la aplicación
--}}
<div class="table-responsive col-md-12">    
    <table class="mb-0 table table-striped">
        <thead>
            @include('backend.sms.header-table-smses')
        </thead>
        <tbody>
            @forelse ($smses as $sms)
            <tr>
                <td>
                	{{$loop->iteration}}
                </td>
                
                <td data-label="@lang('Destinatario')">
                	@if ($sms->sendable instanceof \App\Models\Signer)
                	<span class="text-nowrap">
	                	{{ $sms->sendable->name }}
	                	{{ $sms->sendable->lastname }}
                	</span>
                	<div class="text-info">
                		{{ $sms->sendable->phone }}
                	</div>
                	@else
                        @if ($sms->sendable instanceof \App\Models\FileSharingContact)
                            <span class="nowrap">
                                {{ $sms->sendable->name }}
                                {{ $sms->sendable->lastname }}
                            </span>
                            <div class="text-info">
                                {{ $sms->sendable->phone }}
                            </div>
                        @else
                        'TODO'    
                        @endif
                	@endif
                </td>

                <td data-label="@lang('Partes')">
                	<span class="bold">
                		{{$sms->pieces}} ({{ strlen($sms->text) }})
                	</span>
                </td>

                <td data-label="@lang('Texto')">
                	@if ($sms->text)
                		{{$sms->text}}
                	@endif
                </td>
                
                <td data-label="@lang('Enviado')" class="text-nowrap">
                	@if ($sms->sended_at)
                		@date($sms->sended_at)
                		<div class="text-info">
                			@time($sms->sended_at)
                		</div>
                	@endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-danger bold">
                    @lang('No hay mensajes de texto registrados')
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            @include('backend.sms.header-table-smses')
        </tfoot>
    </table>

    {{-- Control de la Tabla --}}
    @include('backend.sms.table-controls')
    {{-- / Control de la Tabla --}}

</div>