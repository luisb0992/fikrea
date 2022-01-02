{{-- Muestra el visor del documento --}}

{{-- @include('dashboard.documents.config.tracert-canvas') --}}
<div class="document"
    {{-- @mousemove="mouseMoveOverCanvasEvent" --}}
    ref="document">
    {{-- Muestra el documento PDF --}}
    <canvas width="250" height="100" 
        id="pdf" v-show="true" data-id="{{$document->id}}" 
        data-pdf="@route('dashboard.document.pdf', ['token' => $document->id])"

        @dragover="handleDragOver"
        @drop="handleDrop"

        data-request="@route('dashboard.document.textboxs.save', ['id' => $document->id])"
        data-boxs="@route('dashboard.document.get.boxs', ['token' => $document->id])"
        
        {{--
            Si se ha seleccionado
            firma manuscrita o verificación de datos (formulario) o solicitud de documentos
            se debe configurar al terminar en ese orden...
         --}}
        @if ($document->mustBeValidateByHandWrittenSignature())
        data-redirect-to="@route('dashboard.document.prepare', ['id' => $document->id])"
        @elseif($document->mustBeValidateByFormData())
        data-redirect-to="@route('dashboard.document.formdata', ['id' => $document->id])"
        @elseif(!$document->isRequestValidationConfigured())
        data-redirect-to="@route('dashboard.document.request.validations', ['id' => $document->id])"
        @else
        data-redirect-to="@route('dashboard.document.list')"
        @endif

        @mousedown="select"
        class="cursor-for-sign"
    ></canvas>
    {{--/Muestra el documento PDF --}}

    {{-- El contenedor de cajas de textos --}}
    <div id="textboxs"></div>
    {{-- /El contenedor de cajas de textos --}}

</div>
{{--/ Muestra el visor del documento --}}