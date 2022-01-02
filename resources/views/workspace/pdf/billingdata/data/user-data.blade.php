{{-- datos de la firma digital si es necesario --}}
@if ($company->signature)
    <p>
    <h4>@lang('Datos aportados')</h4>
    <table>
        <tbody>
            <tr>
                <td>@lang('Título')</td>
                <td>{{ $company->sometitle }}</td>
            </tr>
            <tr>
                <td>@lang('Comentario adicional')</td>
                <td>{{ $company->somecomment }}</td>
            </tr>
            <tr>
                <td>
                    @lang('Firma manuscrita digital del usuario <b>:user</b>', [
                    'user' => $company->user ? $company->user->getFullNameUser() : null
                    ])
                </td>
                <td>
                    <img src="@exists($company->user->config->sign->sign)" alt="signature" class="img-signature" />
                </td>
            </tr>
        </tbody>
    </table>
    </p>
@endif
{{-- /datos de la firma digital si es necesario --}}

<p>
<h4>@lang('Datos de Facturación')</h4>
<table>
    <tbody>
        <tr>
            <td>@lang('Imagen del Perfil')</td>
            <td>
                <div>
                    @if ($company->user->image)
                        <img class="profile-image" src="data:image/png;base64,{{ $company->user->image }}"
                            alt="profile" />
                    @else
                        <img class="profile-image" src="@asset('assets/images/dashboard/avatars/empty-user.png')"
                            alt="profile" />
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td>@lang('Empresa/Razón Social')</td>
            <td>@exists($company->name)</td>
        </tr>
        <tr>
            <td>@lang('CIF-NIF')</td>
            <td>@exists($company->cif)</td>
        </tr>
        <tr>
            <td>@lang('Dirección de Correo para Facturación')</td>
            <td>@exists($company->email)</td>
        </tr>
        <tr>
            <td>@lang('Dirección Postal')</td>
            <td>@exists($company->address)</td>
        </tr>
        <tr>
            <td>@lang('Código Postal')</td>
            <td>@exists($company->code_postal)</td>
        </tr>
        <tr>
            <td>@lang('Teléfono')</td>
            <td>{{ $company->dial_code ?? '---' }} / @exists($company->phone)</td>
        </tr>
        <tr>
            <td>@lang('Localidad')</td>
            <td>@exists($company->city)</td>
        </tr>
        <tr>
            <td>@lang('Provincia')</td>
            <td>@exists($company->province)</td>
        </tr>
        <tr>
            <td>@lang('País')</td>
            <td>
                @if ($company->country)
                    @foreach ($countries as $key => $country)
                        @if($key == $company->country)
                            <span>{{ $country }}</span>
                        @endif
                    @endforeach
                @else
                    <span>---</span>
                @endif
            </td>
        </tr>
    </tbody>
</table>
</p>

{{-- salto de pagina --}}
<div class="break"></div>
