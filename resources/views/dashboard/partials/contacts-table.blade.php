{{-- Listado de contactos --}}
<div class="table-responsive">
    <table class="mb-0 table table-striped">
        <thead>
            <tr>
                <th></th>
                <th>@lang('Apellidos')</th>
                <th>@lang('Nombre')</th>
                <th>@lang('Dirección de Correo')</th>
                <th>@lang('Teléfono')</th>
            </tr>
        </thead>
        <tbody>

            @forelse($user->contacts as $contact)
                <tr>
                    <td class="text-center">
                        <a href="#" data-name="{{ $contact->name }}" data-lastname="{{ $contact->lastname }}"
                            data-email="{{ $contact->email }}" data-phone="{{ $contact->phone }}"
                            data-dni="{{ $contact->dni }}" data-id="{{ $contact->id }}"
                            data-company="{{ $contact->company }}" data-position="{{ $contact->position }}"
                            @click.prevent="addContactToSigners" class="btn btn-warning"
                            :class="maxSignerExceed ? 'disabled':  ''">
                            @lang('Añadir')
                        </a>
                    </td>

                    <td data-label="@lang('Apellidos')">{{ $contact->lastname }}</td>
                    <td data-label="@lang('Nombre')">{{ $contact->name }}</td>
                    <td data-label="@lang('Email')">
                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                    </td>
                    <td data-label="@lang('Teléfono')">
                        {{ $contact->phone }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">@lang('No hay contactos')</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{-- /Listado de contactos --}}
