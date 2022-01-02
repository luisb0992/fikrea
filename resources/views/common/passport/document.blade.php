{{--Documento de Identificación --}}
<div v-if="canUploadDocuments" class="row col-md-12">
    <h5>
        <img src="@asset('/assets/images/workspace/document.png')" class="icon-image">
        @lang('Detalles de nuevo documento')
    </h5>
</div>

<div v-if="canUploadDocuments" class="row col-md-12">
            
    {{-- Anverso del documento de identificación --}}
    @include('common.passport.document-anverse')
    {{--/Anverso del documento de identificación --}}

    {{-- Reverso del documento de identificación --}}
    @include('common.passport.document-reverse')
    {{--/Reverso del documento de identificación --}}

    {{-- Datos del documento de identificación --}}
    @include('common.passport.document-data')
    {{--/Datos del documento de identificación --}}

</div>
{{--/Documento de Identificación --}}