{{-- Estado de la solicitud de documentos --}}
@include('dashboard.verificationform.partials.pdf.partials.status')
{{-- / Información de la solicitud de documentos --}}

{{-- Estado actual --}}
@include('dashboard.verificationform.partials.pdf.partials.info')
{{-- / Estado actual --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Listado de Personas que forman parte --}}
@include('dashboard.verificationform.partials.pdf.partials.participants')
{{-- /Listado de Personas que forman parte --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Envíos realizados --}}
@include('dashboard.verificationform.partials.pdf.partials.sends')
{{-- /Envíos realizados --}}
