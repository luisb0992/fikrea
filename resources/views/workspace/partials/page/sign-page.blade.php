{{-- La página del documento a firmar --}}
<div class="document" ref="document">

    <canvas
    	id="document" data-id="{{$signer->document->id}}"
        data-page="{{$page}}" 
        data-pdf="@route('dashboard.document.pdf', ['token' => $signer->token])"
        data-signs="@route('dashboard.document.get.signs', ['token' => $signer->token])"
        data-save="@route('workspace.document.save', ['token' => $signer->token])"
        data-redirect-to-workspace-home="@route('workspace.home', ['token' => $signer->token])">
    </canvas>
    
    {{-- El contenedor de marcadores de firmas --}}
    <div id="signs"></div>

</div>
{{--/La página del documento a firmar --}}