    <p>
        @lang('Los siguientes datos adicionales se recopilaron durante el proceso de firma digital del documento :certificate',
            ['certificate' => $document->guid]
        )
    </p>

    {{-- Información del documento --}}
    @include('dashboard.documents.pdf.partials.info')
    {{--/Información del documento --}}

    {{-- Próxima página--}}
    <div class="break"></div>
    {{-- / Próxima página--}}

    {{-- Listado de Personas que forman parte --}}
    @include('dashboard.documents.pdf.partials.participants')
    {{--/Listado de Personas que forman parte --}}

    {{-- Próxima página--}}
    <div class="break"></div>
    {{-- / Próxima página--}}

    {{-- Lista de validaciones del documento --}}
    @include('dashboard.documents.pdf.partials.validations')
    {{--/Lista de validaciones del documento --}}

    {{-- Próxima página--}}
    <div class="break"></div>
    {{-- / Próxima página--}}

    {{-- Cajas de textos completadas--}}
    @include('dashboard.documents.pdf.partials.textboxs')
    {{--/ Cajas de textos completadas --}}

    {{-- Firmas manuscritas efectuadas--}}
    @include('dashboard.documents.pdf.partials.signs')
    {{--/ Firmas manuscritas efectuadas --}}

    {{-- Sellos estampados sobre el documento--}}
    @include('dashboard.documents.pdf.partials.stamps')
    {{--/ Sellos estampados sobre el documento --}}

    {{-- Validaciones de audio efectuadas--}}
    @include('dashboard.documents.pdf.partials.audios')
    {{--/ Validaciones de audio efectuadas--}}

    {{-- Validaciones de video efectuadas--}}
    @include('dashboard.documents.pdf.partials.videos')
    {{--/ Validaciones de video efectuadas--}}

    {{-- Validaciones de capturas de pantalla--}}
    @include('dashboard.documents.pdf.partials.captures')
    {{--/ Validaciones de capturas de pantalla--}}

    {{-- Validaciones con documentos identificativos --}}
    @include('dashboard.documents.pdf.partials.passports')
    {{--/Validaciones con documentos identificativos --}}

    {{-- Validaciones con solicitud de documentos --}}
    @include('dashboard.documents.pdf.partials.requests')
    {{--/Validaciones con solicitud de documentos --}}

    {{-- Validaciones de verificación de datos --}}
    @include('dashboard.documents.pdf.partials.form-data')
    {{--/Validaciones de verificación de datos --}}

    {{-- Envíos realizados del documento --}}
    @include('dashboard.documents.pdf.partials.sends')
    {{--/Envíos realizados del documento --}}
