{{-- El marcador donde se debe situar una firma en el documento --}}
<div data-sign-id="" class="sign-placeholder template mt-4">
    <div class="sign-placeholder-header">

        {{-- Botón para limpiar el contedor de firma --}}
        <span class="sign-placeholder-id"></span>
        
        {{-- Botón para eliminar el contenedor de firma --}}
        <button data-sign-id="" class="clear-sign-placeholder" style="margin: 0px !important;">
            <i class="fa fa-trash"></i>
        </button>

    </div>
    <div class="sign-placeholder-body">
        <canvas data-id="" class="sign"></canvas>
        <div class="signer-name">
            @if ($signer->name || $signer->lastname)
            {{$signer->name}} {{$signer->lastname}}
            @elseif ($signer->email)
            {{$signer->email}}
            @elseif ($signer->email)
            {{$signer->phone}}     
            @endif
        </div>
    </div>
</div>
{{--/ El marcador donde se debe situar una firma en el documento --}}