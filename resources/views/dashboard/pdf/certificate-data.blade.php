<p>
    @lang('Los siguientes datos adicionales se recopilaron durante el proceso de firma digital del documento
    :certificate',
    ['certificate' => $document->guid]
    )
</p>

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
            <td>{{ $document->name }}</td>
        </tr>
        <tr>
            <td>@lang('GUID')</td>
            <td>{{ $document->guid }}</td>
        </tr>
        <tr>
            <td>@lang('Tipo')</td>
            <td>{{ $document->type }}</td>
        </tr>
        <tr>
            <td>@lang('Tamaño')</td>
            <td>@filesize($document->size)</td>
        </tr>
        <tr>
            <td>@lang('Páginas')</td>
            <td>{{ $document->pages }}</td>
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
                <div><strong>md5</strong> : {{ $document->original_md5 }}</div>
                <div><strong>sha-1</strong> : {{ $document->original_sha1 }}</div>
            </td>
        </tr>
        <tr>
            <td>@lang('Documento firmado')</td>
            <td>
                <div>@lang('Firma Digital')</div>
                <div><strong>md5</strong> : {{ $document->signed_md5 }}</div>
                <div><strong>sha-1</strong> : {{ $document->signed_sha1 }}</div>
            </td>
        </tr>
        <tr>
            <td>@lang('Clave Criptográfica')</td>
            <td>{{ pathinfo($document->original_path, PATHINFO_FILENAME) }}</td>
        </tr>
    </tbody>
</table>
{{-- /Información del documento --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Listado de Personas que forman parte --}}
<table>
    <thead>
        <tr>
            <th colspan="3">@lang('Participantes')</th>
        </tr>
        <tr>
            <th>@lang('Firmante')</th>
            <th>@lang('Observaciones')</th>
            <th>@lang('Estado')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($document->signers as $signer)
            <tr>
                <td>
                    {{ $signer->name }} {{ $signer->lastname }}
                    <div>
                        @if ($signer->email)
                            <a href="mailto:{{ $signer->email }}">{{ $signer->email }}</a>
                        @elseif ($signer->phone)
                            <a href="tel:{{ $signer->phone }}">{{ $signer->phone }}</a>
                        @endif
                    </div>
                </td>
                <td>
                    @if ($signer->hasBeenCanceled())
                        <div>
                            <span class="bold">@date($signer->canceled_at)</span>
                            <span>
                                @lang('El proceso fue cancelado por el usuario firmante')
                            </span>
                        </div>
                        @if ($signer->canceled_subject)
                            <div>
                                {{ $signer->canceled_subject }}
                            </div>
                        @endif
                    @endif
                </td>
                <td class="text-center">
                    @if ($signer->hasDoneAllValidations())
                        @lang('Realizado')
                    @elseif ($signer->hasBeenCanceled())
                        @lang('Cancelado')
                    @else
                        @lang('Pendiente')
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{-- /Listado de Personas que forman parte --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Lista de validaciones del documento --}}
<table>
    <thead>
        <tr>
            <th colspan="3">@lang('Validaciones del Documento')</th>
        </tr>
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
                    {{ $validation->signer->name }} {{ $validation->signer->lastname }}
                </td>
                <td>
                    {{-- El tipo de validación --}}
                    @switch ($validation->validation)
                        @case(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE)
                            <h5><i class="fas fa-signature"></i> @lang('Firma manuscrita digital')</h5>
                        @break
                        @case(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION)
                            <h5><i class="fas fa-volume-up"></i> @lang('Archivo de audio')</h5>
                        @break
                        @case(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION)
                            <h5><i class="fas fa-video"></i> @lang('Archivo de video')</h5>
                        @break
                        @case(\App\Enums\ValidationType::PASSPORT_VERIFICATION)
                            <h5><i class="fas fa-id-card"></i> @lang('Documento identificativo')</h5>
                        @break
                        @case(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION)
                            <h5><i class="fas fa-desktop"></i> @lang('Captura de pantalla')</h5>
                        @break
                        @case(\App\Enums\ValidationType::FORM_DATA_VERIFICATION)
                            <h5><i class="fas fa-desktop"></i> @lang('Verificación de datos')</h5>
                        @break
                    @endswitch
                    {{-- / El tipo de validación --}}
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

{{-- /Lista de validaciones del documento --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Firmas manuscritas efectuadas --}}
@if ($document->signs->isNotEmpty())
    @foreach ($document->signs as $sign)
        <br />
        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        {{ $sign->signer }} [ @lang('Firma') {{ $sign->code }} ]
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@lang('Firmante')</th>
                    <td>
                        {{ $sign->signer }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Firma')</td>
                    <td class="text-center">
                        <div>
                            <img src="{{ $sign->sign }}" alt="">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Página')</td>
                    <td>
                        {{ $sign->page }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{ $sign->ip }}
                        </div>
                        <div>
                            @hostname($sign->ip)
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Sistema Utilizado')</td>
                    {{-- <td>@useragent($sign->user_agent)</td> --}}
                </tr>
                {{-- Para el creador/autor del documento no se recoge la ubicación --}}
                @if (!$sign->creator)
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $sign->latitude }} {{ $sign->longitude }}
                            </div>
                            <div>
                                @if ($sign->latitude && $sign->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $sign->latitude }},{{ $sign->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                {{-- /Para el creador/autor del documento no se recoge la ubicación --}}
                <tr>
                    <td>@lang('Fecha de Firma')</td>
                    <td>@datetime($sign->signDate)</td>
                </tr>
            </tbody>
        </table>

        {{-- Próxima página --}}
        <div class="break"></div>
        {{-- / Próxima página --}}


    @endforeach

    {{-- comentario de la validacion --}}
    @foreach ($document->signers as $signer)
        @if (!$signer->creator)
            @if ($signer->getIfCommentExists(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE))
                <p>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Comentario')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE) }}"</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            @endif
        @endif
    @endforeach
    {{-- /comentario de la validacion --}}

    {{-- /Formulario de datos --}}
@endif
{{-- / Firmas manuscritas efectuadas --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Sellos estampados sobre el documento --}}
@if ($document->stamps->isNotEmpty())
    @foreach ($document->stamps as $stamp)
        <br />
        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        @lang('Sellado página :page', ['page' => $stamp->page])
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@lang('Firmante')</td>
                    <td class="text-center">
                        <div>
                            {{-- Los sellos los coloca el autor/creador del documento --}}
                            {{ $document->user->name }} {{ $document->user->lastname }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Sello')</td>
                    <td class="text-center">
                        <div>
                            <img src="{{ $stamp->stamp }}" alt="" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Página')</td>
                    <td>
                        {{ $stamp->page }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Fecha de Estampado')</td>
                    <td>@datetime($stamp->created_at)</td>
                </tr>
            </tbody>
        </table>

        {{-- Próxima página --}}
        <div class="break"></div>
        {{-- / Próxima página --}}

    @endforeach
@endif
{{-- / Sellos estampados sobre el documento --}}

{{-- Validaciones de audio efectuadas --}}
@if ($document->mustBeValidateByAudioFile())

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}


    @foreach ($document->signers as $signer)
        @foreach ($signer->audios as $audio)
            <br />
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }} [ Audio ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Validante')</th>
                        <td>{{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }}
                            {{ $signer->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Archivo')</td>
                        <td class="medium">
                            {{ $audio->path }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Grabación')</td>
                        <td>
                            {{ $audio->duration }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $audio->ip }}
                            </div>
                            <div>
                                @hostname($audio->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>@useragent($audio->user_agent)</td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $audio->latitude }} {{ $audio->longitude }}
                            </div>
                            <div>
                                @if ($audio->latitude && $audio->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $audio->latitude }},{{ $audio->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Grabación')</td>
                        <td>@datetime($audio->created_at)</td>
                    </tr>
                </tbody>
            </table>
        @endforeach

        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION))
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Comentario')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION) }}"</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        {{-- /comentario de la validacion --}}
    @endforeach
@endif
{{-- / Validaciones de audio efectuadas --}}

{{-- Validaciones de video efectuadas --}}
@if ($document->mustBeValidateByVideoFile())

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}

    @foreach ($document->signers as $signer)
        @foreach ($signer->videos as $video)
            <br />
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }} [ Video ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Firmante')</th>
                        <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }}
                            {{ $signer->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Archivo')</td>
                        <td class="medium">
                            {{ $video->path }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Grabación')</td>
                        <td>
                            {{ $video->duration }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $video->ip }}
                            </div>
                            <div>
                                @hostname($video->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>@useragent($video->user_agent)</td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $video->latitude }} {{ $video->longitude }}
                            </div>
                            <div>
                                @if ($video->latitude && $video->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $video->latitude }},{{ $video->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Grabación')</td>
                        <td>@datetime($video->created_at)</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION))
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Comentario')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION) }}"</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        {{-- /comentario de la validacion --}}
    @endforeach
@endif
{{-- / Validaciones de video efectuadas --}}

{{-- Validaciones de captura de pantalla efectuadas --}}
@if ($document->mustBeValidateByScreenCapture())

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}

    @foreach ($document->signers as $signer)
        @foreach ($signer->captures as $capture)
            <br />
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }} [ @lang('Captura de Pantalla') ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Firmante')</th>
                        <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }}
                            {{ $signer->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Archivo')</td>
                        <td class="medium">
                            {{ $capture->path }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Grabación')</td>
                        <td>
                            {{ $capture->duration }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $capture->ip }}
                            </div>
                            <div>
                                @hostname($capture->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>@useragent($capture->user_agent)</td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $capture->latitude }} {{ $capture->longitude }}
                            </div>
                            <div>
                                @if ($capture->latitude && $capture->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $capture->latitude }},{{ $capture->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Grabación')</td>
                        <td>@datetime($capture->created_at)</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION))
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Comentario')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION) }}"</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        {{-- /comentario de la validacion --}}
    @endforeach
@endif
{{-- / Validaciones de video efectuadas --}}


{{-- Validaciones con documentos identificativos --}}
@if ($document->mustBeValidateByPassport())

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}

    @foreach ($document->signers as $signer)
        @foreach ($signer->passports as $passport)
            <br />
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }} [ Doc: {{ $passport->number }} ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Validante')</th>
                        <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }}
                            {{ $signer->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Documento')</td>
                        <td>
                            {{ $passport->number }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $passport->ip }}
                            </div>
                            <div>
                                @hostname($passport->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Reconocimiento Facial')</td>
                        <td>
                            @switch ($passport->face_recognition)
                                @case(-1)
                                    @lang('No realizado')
                                @break
                                @case(0)
                                    @lang('Reconocimiento facial negativo')
                                @break
                                @case(+1)
                                    @lang('Reconocimiento facial positivo')
                                @break
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>@useragent($passport->user_agent)</td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $passport->latitude }} {{ $passport->longitude }}
                            </div>
                            <div>
                                <a
                                    href="https://www.google.com/maps/search/?api=1&query={{ $passport->latitude }},{{ $passport->longitude }}">
                                    @lang('Ver en Google Maps')
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Acreditación')</td>
                        <td>@datetime($passport->created_at)</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::PASSPORT_VERIFICATION))
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Comentario')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::PASSPORT_VERIFICATION) }}"</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        {{-- /comentario de la validacion --}}
    @endforeach

@endif
{{-- /Validaciones con documentos identificativos --}}

{{-- Validaciones de formulario de datos --}}
@if ($document->mustBeValidateByFormData())
    <div class="break"></div>
    @foreach ($document->signers as $signer)
        @foreach ($signer->formdata as $key => $formdata)
            @if ($key === 0)
                <br />
                <table>
                    <thead>
                        <tr>
                            <th colspan="2">
                                {{ $signer->name }} {{ $signer->lastname }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@lang('Validante')</th>
                            <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }}
                                {{ $signer->email }}</td>
                        </tr>
                        <tr>
                            <td>@lang('Tipo de Formulario')</td>
                            <td>
                                <div>
                                    @switch ($formdata->type)
                                        @case(\App\Enums\FormType::PARTICULAR_FORM)
                                            @lang('Formulario Particular')
                                        @break
                                        @case(\App\Enums\FormType::BUSINESS_FORM)
                                            @lang('Formulario Empresarial')
                                        @break
                                    @endswitch
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('Dirección IP')</td>
                            <td>
                                <div>
                                    {{ $formdata->ip }}
                                </div>
                                <div>
                                    @hostname($formdata->ip)
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('Sistema Utilizado')</td>
                            <td>@useragent($formdata->user_agent)</td>
                        </tr>
                        <tr>
                            <td>@lang('Ubicación')</td>
                            <td>
                                <div>
                                    {{ $formdata->latitude }} {{ $formdata->longitude }}
                                </div>
                                @if ($formdata->latitude && $formdata->longitude)
                                    <div>
                                        <a
                                            href="https://www.google.com/maps/search/?api=1&query={{ $formdata->latitude }},{{ $formdata->longitude }}">
                                            @lang('Ver en Google Maps')
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('Fecha de Acreditación')</td>
                            <td>@datetime($formdata->created_at)</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        @endforeach
        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::FORM_DATA_VERIFICATION))
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Comentario')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::FORM_DATA_VERIFICATION) }}"</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        {{-- /comentario de la validacion --}}
    @endforeach
@endif
{{-- /Validaciones de formulario d datos --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}

{{-- Envíos realizados del documento --}}
<table>
    <thead>
        <tr>
            <th colspan="3">
                @lang('Envíos realizados de este documento')
            </th>
        </tr>
        <tr>
            <th>@lang('Fecha de Envío')</th>
            <th>@lang('Destinatarios')</th>
            <th>@lang('Envío')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($document->sharings as $sharing)
            <tr>
                <td>@datetime($sharing->sent_at)</td>
                <td>
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
                <td class="text-center">
                    @switch($sharing->type)
                        @case(0)
                            @lang('Auto')
                        @break
                        @case(1)
                            @lang('Manual')
                        @break
                    @endswitch
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{-- /Envíos realizados del documento --}}

{{-- Próxima página --}}
<div class="break"></div>
{{-- / Próxima página --}}
