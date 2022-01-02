{{-- Obtiene los tipos de caracteres permitidos legibles para los usuarios --}}
@switch($type)
    @case('string')
        @lang('Solo letras')
        @break
    @case('numeric')
        @lang('Solo números')
        @break
    @case('special')
        @lang('Solo letras y caracteres especiales (no números)')
        @break
    @default
        {{ $type }}
@endswitch