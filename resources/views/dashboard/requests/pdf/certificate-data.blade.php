{{-- Datos en el proceso de solicitud de documentos --}}

	{{-- Estado de la solicitud de documentos --}}
	@include('dashboard.requests.pdf.partials.status')
	{{-- / Información de la solicitud de documentos --}}

    {{-- Estado de la solicitud de documentos --}}
    @include('dashboard.requests.pdf.partials.info')
    {{-- / Información de la solicitud de documentos --}}

	{{-- Listado de Personas que forman parte --}}
	@include('dashboard.requests.pdf.partials.participants')
	{{--/Listado de Personas que forman parte --}}

    {{-- Próxima página--}}
	<div class="break"></div>
	{{-- / Próxima página--}}

    {{-- Listado de Documentos requeridos --}}
    @include('dashboard.requests.pdf.partials.required-files')
    {{-- /Listado de Documentos requeridos --}}

    {{-- Lista de Documentos aportados --}}
    @include('dashboard.requests.pdf.partials.contributted-files')
    {{-- /Lista de Documentos aportados --}}

    @isset ($fromSign)
    @else
    {{-- Próxima página--}}
    <div class="break"></div>
    {{-- / Próxima página--}}
    @endisset

    {{-- Envíos realizados del documento --}}
    @include('dashboard.requests.pdf.partials.sends')
    {{--/Envíos realizados del documento --}}

{{-- /Datos en el proceso de solicitud de documentos --}}