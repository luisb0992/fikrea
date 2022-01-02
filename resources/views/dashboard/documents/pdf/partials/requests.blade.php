{{--
    Validaciones de solicitud de documentos en el documento
    Si este documento no tiene solicitudes de documentos no se muestra nada
--}}
@if ($document->mustBeValidateByDocumentRequest() && $document->requests()->count())
<p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Validaciones mediante solicitudes de documentos')</th>
            </tr>
        </thead>
    </table>    
</p>

@foreach ($document->requests() as $request)
    
    {{-- Muestro la info del certificado del proceso de solicitud de documentos --}}
    @include('dashboard.requests.pdf.certificate-data',
	    [
	    	'fromSign'	=> true
	    ]
    )
    {{-- /Muestro la info del certificado del proceso de solicitud de documentos --}}

@endforeach

@endif
