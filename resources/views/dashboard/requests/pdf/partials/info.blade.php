{{-- Información de la solicitud de documentos --}}
<p>
<table>
    <thead>
        <tr>
            <th colspan="2">@lang('Información de la solicitud')</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>@lang('Nombre')</td>
            <td>{{ $request->name }}</td>
        </tr>
        <tr>
            <td>@lang('Comentarios')</td>
            <td>{{ $request->comment }}</td>
        </tr>
        <tr>
            <td>@lang('Creada')</td>
            <td>@datetime($request->created_at)</td>
        </tr>
    </tbody>
</table>
</p>
{{-- / Información de la solicitud de documentos --}}

{{-- comentario de la validacion --}}
@if ($request->getIfCommentExists())
    <br>
    <div>
        <table>
            <thead>
                <tr>
                    <th>@lang('Comentario Adicional')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p>
                            <small>"{{ $request->getIfCommentExists() }}"</small>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endif
{{-- /comentario de la validacion --}}
