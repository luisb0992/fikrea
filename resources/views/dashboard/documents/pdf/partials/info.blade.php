{{-- Información del documento --}}
<table>
    <thead>
        <tr>
            <th colspan="2">@lang('Información del Documento')</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>@lang('Documento')</td>
            <td>{{$document->name}}</td>
        </tr>
        <tr>
            <td>@lang('GUID')</td>
            <td>{{$document->guid}}</td>
        </tr>
        <tr>
            <td>@lang('Tipo')</td>
            <td>{{$document->type}}</td>
        </tr>
        <tr>
            <td>@lang('Tamaño')</td>
            <td>@filesize($document->size)</td>
        </tr>
        <tr>
            <td>@lang('Páginas')</td>
            <td>{{$document->pages}}</td>
        </tr>
        <tr>
            <td>@lang('Creado')</td>
            <td>@datetime($document->created_at)</td>
        </tr>
        <tr>
            <td>@lang('Enviado')</td>
            <td>@datetime($document->sent_at)</td>
        </tr>
        <tr>
            <td>@lang('Almacenamiento')</td>
            <td>
                {{-- Si el archivo se almacena en el servicio S3 de Amazon --}}
                @if (\Fikrea\AppStorage::isS3())
                    <a href="https://aws.amazon.com/es/s3/">Amazon Simple Storage Service (Amazon S3)</a>
                @else
                {{-- Almacenamiento en servidor local (UFS, Unix File System) --}}
                    UFS
                @endif
            </td>
        </tr>
        <tr>
            <td>@lang('Documento original')</td>
            <td>
                <div>@lang('Firma Digital')</div>
                <div><strong>md5</strong> : {{$document->original_md5}}</div>
                <div><strong>sha-1</strong> : {{$document->original_sha1}}</div>
            </td>
        </tr>
        <tr>
            <td>@lang('Documento firmado')</td>
            <td>
                <div>@lang('Firma Digital')</div>
                <div><strong>md5</strong> : {{$document->signed_md5}}</div>
                <div><strong>sha-1</strong> : {{$document->signed_sha1}}</div>
            </td>
        </tr>
        <tr>
            <td>@lang('Clave Criptográfica')</td>
            <td>{{pathinfo($document->original_path, PATHINFO_FILENAME)}}</td>
        </tr>
    </tbody>
</table>
{{--/Información del documento --}}