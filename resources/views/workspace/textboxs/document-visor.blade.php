{{-- Muestra el visor del documento --}}

{{-- @include('dashboard.documents.config.tracert-canvas') --}}
<div class="document"
    ref="document">
    {{-- Muestra el documento PDF --}}
    <canvas width="250" height="100" 
        id="pdf" v-show="true" data-id="{{$document->id}}" 
        data-pdf="@route('dashboard.document.pdf', ['token' => $document->id])"

        data-request="@route('workspace.textboxs.save', ['token' => $signer->token])"
        {{-- Url para obtener las cajas del firmante --}}
        data-boxs="@route('dashboard.document.get.boxs', ['token' => $signer->token])"
        
    ></canvas>
    {{--/Muestra el documento PDF --}}

    {{-- El contenedor de cajas de textos --}}
    <div id="textboxs"></div>
    {{-- /El contenedor de cajas de textos --}}

</div>
{{--/ Muestra el visor del documento --}}