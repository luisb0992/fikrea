{{-- ------------------------------
    --> El usuario
    ------------------------------- --}}
<td class="text-center">
    <div class="mb-4 text-center">
        {{-- Cuando el firmante es el propio usuario --}}
        {{-- Usamos iconos distintos para el firmante y para el propio creador del documento --}}
        @if ($signer->creator)
            <i class="fas fa-user-cog fa-3x fa-shadow text-info"></i>
        @else
            <i class="fas fa-user fa-3x fa-shadow text-warning"></i>
        @endif
    </div>
    <h3 class="card-title text-center">{{ $signer->lastname }} {{ $signer->name }}</h3>
    <div class="text-info text-center">
        @if ($signer->email)
            <i class="far fa-envelope text-warning"></i> {{ $signer->email }}
        @elseif ($signer->phone)
            <i class="fas fa-phone-alt text-warning"></i> {{ $signer->phone }}
        @endif
    </div>
</td>