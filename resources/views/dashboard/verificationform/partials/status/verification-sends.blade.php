{{-- Listado de los envíos realizados --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-share-alt text-success"></i>
        @lang('Lista de Envíos Realizados')
    </h5>
</div>

<div class="col-md-12 mb-4">
    <div class="main-card card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    @include('dashboard.documents.status.header-sending-list')
                </thead>
                <tbody>
                    @forelse ($verificationForm->sharings as $sharing)
                        <tr>
                            <td data-label="@lang('Fecha de Envío')">
                                @date($sharing->sent_at)
                                <div class="text-info">
                                    @time($sharing->sent_at)
                                </div>
                            </td>
                            <td data-label="@lang('Destinatarios')">
                                @foreach ($sharing->signers->filter(fn($signer) => !$signer->creator) as $signer)
                                    <div>
                                        @if ($signer->name || $signer->lastname)
                                            {{ $signer->name }} {{ $signer->lastname }}
                                        @elseif ($signer->email)
                                            {{ $signer->email }}
                                        @else
                                            {{ $signer->phone }}
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td data-label="@lang('Tipo de envío')">
                                {{ \App\Enums\DocumentSharingType::fromValue($sharing->type ?? 0) }}
                            </td>
                            <td data-label="@lang('Atendido')">
                                @if ($sharing->visited_at)
                                    @date($sharing->visited_at)
                                    <div class="text-info">
                                        @time($sharing->visited_at)
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-danger bold">
                                @lang('No hay registros')
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @include('dashboard.documents.status.header-sending-list')
                </tfoot>
            </table>
        </div>
    </div>
    {{-- /Listado de los envíos realizados --}}

    {{-- Botón para el reenvío de la verificación de datos --}}
    <div class="mt-4 mb-4 text-right">

        {{-- Sólo se puede hacer un reenvío de la verificación diariamente
            Si la verificación ya ha sido enviada hoy, el botón se mostrará deshabilitado --}}
        @if ($verificationForm->sharingHasBeenSentToday())
            <button class="btn btn-success disabled no-events">
                <i class="fas fa-ban"></i>
                @lang('Envío Realizado')
            </button>
        @else
            @if ($verificationForm->signers()->first()->verificationFormIsDone())
                <button class="btn btn-success disabled no-events">
                    <i class="fas fa-ban"></i>
                    @lang('verificación realizada')
                </button>
            @else
                <button @click.prevent="sendVerification" :disabled="verificationWasSharing"
                    data-send-verificationform="@route('dashboard.verificationform.send', ['id' => $verificationForm->id])"
                    class="btn btn-success">
                    <i class="fas fa-check"></i> @lang('Volver a Enviar')
                </button>
            @endif
        @endif
    </div>
    {{-- Botón para el reenvío de la verificación de datos --}}
</div>
