{{-- Tipo de evento segun sea el caso
    devuelve el nombre del tipo del evento --}}
@switch($type)
    @case(\App\Enums\Event\EventType::VOTE)
        {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::VOTE) }}
    @break
    @case(\App\Enums\Event\EventType::SURVEY)
        {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::SURVEY) }}
    @break
    @case(\App\Enums\Event\EventType::SIGNATURE_COLLECTION)
        {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::SIGNATURE_COLLECTION) }}
    @break
    @case(\App\Enums\Event\EventType::SURVEY_AND_SIGNATURE_COLLECTION)
        {{ new \App\Enums\Event\EventType(\App\Enums\Event\EventType::SURVEY_AND_SIGNATURE_COLLECTION) }}
    @break
    @default
@endswitch
