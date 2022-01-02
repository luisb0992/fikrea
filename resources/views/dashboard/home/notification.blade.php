{{-- Muestra notificación --}}

{{-- modal si desea eliminar y marcar la notificacion --}}
@include('dashboard.home.partials.info-delete-notification-modal')

<div class="col-sm-12 col-md-6 col-lg-4  notification" data-id="{{ $notification->id }}">

    <div class="card mb-3 widget-content">
        <div class="widget-content-wrapper">
            <div class="widget-content-left">

                <div class="widget-heading text-right p-2 {{ $notification->view_style }} rounded">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="mr-1">@datetime($notification->created_at)</span>
                            <b-button v-b-modal.delete-notification-{{ $notification->id }} pill variant="light">
                                <i class="fa fa-times text-danger"></i>
                            </b-button>
                        </div>
                    </div>
                </div>

                <div class="font-weight-bold mt-2 text-justify">
                    {!! $notification->title !!}.
                </div>

                <div class="mt-3 mb-2 text-justify">
                    <span class="text-muted">{!! preg_replace('/<a(.*)>(.*)<\/a>/i', "$2", $notification->message) !!}.</span>

                    {{-- si el proceso fue cancelado --}}
                    @if ($notification->type === \App\Enums\NotificationTypeEnum::CANCELLED)
                        @if ($notification->reasonCacel)
                            <hr>
                            <div class="font-weight-bold mb-1">@lang('El usuario mencionó lo siguiente'):</div>
                            <span class="text-danger font-weight-bold">
                                "{{ $notification->reasonCacel->reason }}"
                            </span>
                        @endif
                    @endif
                </div>

                <div class="bg-light p-1 rounded">
                    <div class="mt-2 p-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 font-italic">
                                @lang('Pulse <b>ver</b> para más información')
                            </div>
                            <div>
                                <buttton id="btnUrl-{{ $notification->id }}" type="button"
                                    @click.prevent="read({{ $notification->id }}, true)"
                                    class="btn btn-outline-primary">
                                    @lang('Ver') <i class="fas fa-long-arrow-alt-right"></i>
                                </button>
                            </div>

                            {{-- Url a redireccionar si es necesario --}}
                            <span id="redirectUrl-{{ $notification->id }}" data-url="{{ $notification->url }}"
                                class="d-none"></span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="widget-content-right">
                <div class="widget-numbers text-warning">
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</div>
{{-- / Muestra notificación --}}
