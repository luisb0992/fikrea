{{-- Lista de validaciones del documento --}}
<p>
    <table>
        <thead>
            <tr>
                <th colspan="3">@lang('Validaciones del Documento')</th>
            </tr>
        </thead>
    </table>
</p>

<p>
    <table>
        <thead>
            <tr>
                <th>@lang('Firmante')</th>
                <th>@lang('Validación')</th>
                <th>@lang('Realizada')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($document->validations as $validation)
            <tr>
                <td>
                    {{$validation->signer->name}} {{$validation->signer->lastname}}
                </td>
                <td>
                    {{-- El tipo de validación --}}
                    {{ \App\Enums\ValidationType::fromValue($validation->validation) }}
                    {{--/ El tipo de validación --}}
                </td>
                <td>
                    @if ($validation->validated_at)
                        @datetime($validation->validated_at)
                    @else
                        @lang('No realizada')
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table> 
</p>
{{--/Lista de validaciones del documento --}}