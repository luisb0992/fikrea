{{-- La ayuda para la página --}}
<div>

    {{-- Información del proceso --}}
    @if ($signer->document)
        <div class="text-info small">
            @lang('Proceso de Firma #:guid / :date', [
                'guid' => $signer->document->guid,
                'date' => $signer->document->created_at->format('d-m-Y')
                ]
            )
        </div>
    @endif
    {{--/Información del proceso --}}

    {{-- Si se ha generado la url o el signer para la solicitud de documentos --}}
    @if ($signer->name == config('request.user.name') and $signer->email == config('request.user.email'))
    
        @lang('Hola, usted a recibido este enlace generado por el creador del documento').
    
    @else
        {{-- Si se ha enviado la solicitud a un contacto --}}
        {{-- Ayuda para el usuario firmante --}}
        @lang('Hola, :name <a href="mailto::email">:email</a>', [
            'name'  => $signer->name ?? $signer->email,
            'email' => $signer->email,
        ])
    
    @endif
 
    <div class="page-title-subheading">
        @lang('Este es su espacio de trabajo en :app', ['app' => config('app.name')]).
        @lang('A continuación se le indican las acciones que tiene pendientes de realizar')
    </div>
    {{--/Ayuda para el usuario firmante --}}

</div>
{{-- /La ayuda para la página --}}
