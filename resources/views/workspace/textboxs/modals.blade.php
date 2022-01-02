{{-- Modales --}}
    {{-- 
        Modal de documento no completado
        Se muestra cuando no se han realizado todas las firmas requeridas    
    --}}
    @include('workspace.modals.document-not-textboxs-completed')

    {{--
        Modal de documento firmando con éxito
        Se muestra cuando el proceso de firma ha concluido satisfactoriamente
    --}}
    @include('workspace.modals.document-signed')
{{-- / Modales --}}
