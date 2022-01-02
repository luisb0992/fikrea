{{-- Datos sobre el fondo y consumo del api de mensajería --}}
<div class="row col-md-12 ml-2">
    {{-- Créditos disponible en la api --}}
    <div class="col-md-6">
        <div class="card mb-3 widget-content bg-midnight-bloom">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">@lang('Crédito en API de mensajería') : </div>
                </div>
                <div class="widget-content-right ml-2">
                    <div class="widget-numbers text-white">
                        <span>
                            $ {{ $credits }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- /Créditos disponible en la api --}}

    {{-- Cantidad de mensajes o partes que se han enviado --}}
    <div class="col-md-6">
        <div class="card mb-3 widget-content bg-arielle-smile">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">@lang('Cantidad de mensajes real enviados') :</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white ml-2">
                        <span>
                            {{ $smsPieces }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- / Cantidad de mensajes o partes que se han enviado --}}

    {{-- Total gastado en los envíos de los mensajes --}}
    {{-- Ahora mismo oculto --}}
    <div class="col-md-4 d-none">
        <div class="card mb-3 widget-content bg-grow-early">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">
                        @lang('Gastos en envío de mensajes') :
                    </div>
                </div>
                <div class="widget-content-right ml-2">
                    <div class="widget-numbers text-white">
                        <span>
                            $ 65 @config('app.currency')
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- / Total gastado en los envíos de los mensajes --}}
</div>
{{--/Datos sobre el fondo y consumo del api de mensajería --}}