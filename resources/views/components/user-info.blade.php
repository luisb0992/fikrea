@props(['creator', 'message' => Lang::get('Solicitud creada por')])

{{-- Identificación del usuario creator/autor del documento --}}
<div class="creator-info">
    <div class="user-image">
        @if ( $creator->image )
            <img src="data:image/*;base64,{{ $creator->image }}" alt=""/>
        @else
            <img src="@asset('assets/images/dashboard/avatars/empty-user.png')" alt=""/>
        @endif
    </div>
    <div class="user-info">
        <div><em>{{ $message }}:</em></div>
        <div class="ml-1">
            {{-- El nombre del usuario creador --}}
            <div class="bold">
                {{ $creator->name }} {{ $creator->lastname }}
            </div>
            {{--/El nombre del usuario creador --}}

            {{-- Si es una cuenta de empresa se hace constar el cargo y la empresa --}}
            <div>
                @if ( $user && $user->type == \App\Enums\UserType::BUSSINESS_ACCOUNT )
                    <div class="text-secondary">
                        <strong>{{ $creator->position }}</strong><br>
                        {{ $creator->company }}
                    </div>
                @endif
            </div>
            {{--/Si es una cuenta de empresa se hace constar el cargo y la empresa --}}

            {{-- La dirección de correo --}}
            <div class="text-lowercase">
                <a href="mailto:{{ $creator->email }}">{{ $creator->email }}</a>
            </div>
            {{--/La dirección de correo --}}

            {{-- El número de teléfono --}}
            <div>
                @if ( $creator->phone )
                    <a href="tel:{{ $creator->phone }}">{{ $creator->dial_code }} {{ $creator->phone }}</a>
                @endif
            </div>
            {{--/El número de teléfono --}}
        </div>
    </div>
</div>
{{--/Identificación del usuario creator/autor del documento --}}