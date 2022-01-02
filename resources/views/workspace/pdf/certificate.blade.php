@extends('common.layouts.certificate')

@section('page-header')
    <div>
        @lang('Informe acreditativo de proceso de firma digital') #{{$signer->document->guid}}
        [ <span class="bold">@locale</span> ]
    </div>
@endsection

@section('document-guid')
    #{{$signer->document->guid}}
@endsection

@section('document-goals')
    <p>
        @lang('Este documento certifica que :name :lastname <a href="mailto::email">:email</a> ha formado parte del
            proceso de firma y validación digital del documento :guid a través de la plataforma de firma digital
            avanzada :app. Y para que así conste donde sea necesario se emite el siguiente informe acreditativo a :date',
               [
                    'name'      => $signer->name,
                    'lastname'  => $signer->lastname,
                    'email'     => $signer->email,
                    'guid'      => $signer->document->guid,
                    'app'       => config('app.name'),
                    'date'      => now()->format('d-m-Y'),
               ]
        )
    </p>
@endsection

@section('document-data')
    <p>
        @lang('Los siguientes datos adicionales se recopilaron durante el proceso de firma digital del documento :certificate',
            ['certificate' => $signer->document->guid]
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
            <td>{{$signer->document->name}}</td>
        </tr>
        <tr>
            <td>@lang('GUID')</td>
            <td>{{$signer->document->guid}}</td>
        </tr>
        <tr>
            <td>@lang('Tipo')</td>
            <td>{{$signer->document->type}}</td>
        </tr>
        <tr>
            <td>@lang('Tamaño')</td>
            <td>@filesize($signer->document->size)</td>
        </tr>
        <tr>
            <td>@lang('Páginas')</td>
            <td>{{$signer->document->pages}}</td>
        </tr>
        <tr>
            <td>@lang('Creado')</td>
            <td>@datetime($signer->document->created_at)</td>
        </tr>
        <tr>
            <td>@lang('Enviado')</td>
            <td>@datetime($signer->document->sent_at)</td>
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
                <div><strong>md5</strong> : {{$signer->document->original_md5}}</div>
                <div><strong>sha-1</strong> : {{$signer->document->original_sha1}}</div>
            </td>
        </tr>
        <tr>
            <td>@lang('Documento firmado')</td>
            <td>
                <div>@lang('Firma Digital')</div>
                <div><strong>md5</strong> : {{$signer->document->signed_md5}}</div>
                <div><strong>sha-1</strong> : {{$signer->document->signed_sha1}}</div>
            </td>
        </tr>
        <tr>
            <td>@lang('Clave Criptográfica')</td>
            <td>{{pathinfo($signer->document->original_path, PATHINFO_FILENAME)}}</td>
        </tr>
        </tbody>
    </table>
    {{--/Información del documento --}}
    <div class="break"></div>
    {{-- Lista de validaciones del documento --}}
    <table>
        <thead>
        <tr>
            <th colspan="2">@lang('Validaciones del Documento')</th>
        </tr>
        <tr>
            <th>@lang('Validación')</th>
            <th>@lang('Realizada')</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($signer->validations() as $validation)
            <tr>
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
                        <h5><i class="fas fa-desktop"></i> @lang('verificación de datos')</h5>
                        @break
                    @endswitch
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
    {{--/Lista de validaciones del documento --}}
    <div class="break"></div>
    {{-- Firmas manuscritas efectuadas--}}
    @if ($signer->signs->isNotEmpty())
        @foreach ($signer->signs as $sign)
            <br/>
            <table>
                <thead>
                <tr>
                    <th colspan="2">
                        {{$sign->signer}} [ @lang('Firma') {{$sign->code}} ]
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                    @lang('Firmante')</th>
                    <td>
                        {{$sign->signer}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Firma')</td>
                    <td class="text-center">
                        <div>
                            <img src="{{$sign->sign}}" alt="">
                        </div>
                        <div class="text-right">
                            {{$sign->code}}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Página')</td>
                    <td>
                        {{$sign->page}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{$sign->ip}}
                        </div>
                        <div>
                            @hostname($sign->ip)
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Sistema Utilizado')</td>
                    <td>@useragent($sign->user_agent)</td>
                </tr>
                <tr>
                    <td>@lang('Ubicación')</td>
                    <td>
                        <div>
                            {{$sign->latitude}} {{$sign->longitude}}
                        </div>
                        <div>
                            @if ($sign->latitude && $sign->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{$sign->latitude}},{{$sign->longitude}}">
                                    @lang('Ver en Google Maps')
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Fecha de Firma')</td>
                    <td>@datetime($sign->signDate)</td>
                </tr>
                </tbody>
            </table>
            <br>
            {{--  comentario de la validacion  --}}
            @if($signer->getIfCommentExists(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE))
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
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE) }}
                                        "</small>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="break"></div>

        @endforeach
    @endif
    {{--/ Firmas manuscritas efectuadas --}}

    {{-- Validaciones de audio efectuadas--}}
    @if ($signer->mustValidate($signer->document, \App\Enums\ValidationType::AUDIO_FILE_VERIFICATION))
        <div class="break"></div>
        @foreach ($signer->audios as $audio)
            <br/>
            <table>
                <thead>
                <tr>
                    <th colspan="2">
                        {{$signer->name}} {{$signer->lastname}} [ Audio ]
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                    @lang('Validante')</th>
                    <td>{{$signer->name}} {{$signer->lastname}} {{$signer->dni}} {{$signer->email}}</td>
                </tr>
                <tr>
                    <td>@lang('Archivo')</td>
                    <td class="medium">
                        {{$audio->path}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Grabación')</td>
                    <td>
                        {{$audio->duration}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{$audio->ip}}
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
                            {{$audio->latitude}} {{$audio->longitude}}
                        </div>
                        <div>
                            @if ($audio->latitude && $audio->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{$audio->latitude}},{{$audio->longitude}}">
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
            <br>
            {{--  comentario de la validacion  --}}
            @if($signer->getIfCommentExists(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION))
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
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION) }}
                                        "</small>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    @endif
    {{--/ Validaciones de audio efectuadas--}}

    {{-- Validaciones de video efectuadas--}}
    @if ($signer->mustValidate($signer->document, \App\Enums\ValidationType::VIDEO_FILE_VERIFICATION))
        <div class="break"></div>
        @foreach ($signer->videos as $video)
            <br/>
            <table>
                <thead>
                <tr>
                    <th colspan="2">
                        {{$signer->name}} {{$signer->lastname}} [ Video ]
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                    @lang('Validante')</th>
                    <td> {{$signer->name}} {{$signer->lastname}} {{$signer->dni}} {{$signer->email}}</td>
                </tr>
                <tr>
                    <td>@lang('Archivo')</td>
                    <td class="medium">
                        {{$video->path}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Grabación')</td>
                    <td>
                        {{$video->duration}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{$video->ip}}
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
                            {{$video->latitude}} {{$video->longitude}}
                        </div>
                        <div>
                            @if ($video->latitude && $video->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{$video->latitude}},{{$video->longitude}}">
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
            <br>
            {{--  comentario de la validacion  --}}
            @if($signer->getIfCommentExists(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION))
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
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION) }}
                                        "</small>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    @endif
    {{--/ Validaciones de video efectuadas--}}

    {{-- Validaciones por captura de pantalla efectuadas--}}
    @if ($signer->mustValidate($signer->document, \App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION))
        <div class="break"></div>
        @foreach ($signer->captures as $capture)
            <br/>
            <table>
                <thead>
                <tr>
                    <th colspan="2">
                        {{$signer->name}} {{$signer->lastname}} [ @lang('Captura de Pantalla') ]
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                    @lang('Validante')</th>
                    <td> {{$signer->name}} {{$signer->lastname}} {{$signer->dni}} {{$signer->email}}</td>
                </tr>
                <tr>
                    <td>@lang('Archivo')</td>
                    <td class="medium">
                        {{$capture->path}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Grabación')</td>
                    <td>
                        {{$capture->duration}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{$capture->ip}}
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
                            {{$capture->latitude}} {{$capture->longitude}}
                        </div>
                        <div>
                            @if ($capture->latitude && $capture->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{$capture->latitude}},{{$capture->longitude}}">
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
            <br>
            {{--  comentario de la validacion  --}}
            @if($signer->getIfCommentExists(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION))
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
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION) }}
                                        "</small>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    @endif
    {{--/ Validaciones por captura de pantalla efectuadas--}}

    {{-- Validaciones con documentos identificativos --}}
    @if ($signer->mustValidate($signer->document, \App\Enums\ValidationType::PASSPORT_VERIFICATION))
        <div class="break"></div>
        @foreach ($signer->passports as $passport)
            <br/>
            <table>
                <thead>
                <tr>
                    <th colspan="2">
                        {{$signer->name}} {{$signer->lastname}} [ Doc: {{$passport->number}} ]
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                    @lang('Validante')</th>
                    <td> {{$signer->name}} {{$signer->lastname}} {{$signer->dni}} {{$signer->email}}</td>
                </tr>
                <tr>
                    <td>@lang('Documento')</td>
                    <td>
                        {{$passport->number}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{$passport->ip}}
                        </div>
                        <div>
                            @hostname($passport->ip)
                        </div>
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
                            {{$passport->latitude}} {{$passport->longitude}}
                        </div>
                        <div>
                            <a href="https://www.google.com/maps/search/?api=1&query={{$passport->latitude}},{{$passport->longitude}}">
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
            <br>
            {{--  comentario de la validacion  --}}
            @if($signer->getIfCommentExists(\App\Enums\ValidationType::PASSPORT_VERIFICATION))
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
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::PASSPORT_VERIFICATION) }}
                                        "</small>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    @endif
    {{--/Validaciones con documentos identificativos --}}

    {{-- Formulario de datos --}}
    @if ($signer->mustValidate($signer->document, \App\Enums\ValidationType::FORM_DATA_VERIFICATION))
        <div class="break"></div>
        @foreach ($signer->formdata->groupBy('signer_id') as $keyGroup => $groupDataForm)
            @foreach ($groupDataForm as $dataForm)
                @php
                    $type       = $dataForm->type;
                    $ip         = $dataForm->ip;
                    $userAgent  = $dataForm->user_agent;
                    $latitude   = $dataForm->latitude;
                    $longitude  = $dataForm->longitude;
                    $device     = $dataForm->device;
                    $created    = $dataForm->created_at;
                @endphp
            @endforeach
            <br/>
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
                    <td>
                    @lang('Validante')</th>
                    <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }} {{ $signer->email }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Tipo de Formulario')</td>
                    <td>
                        <div>
                            @switch ($type)
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
                            {{ $ip }}
                        </div>
                        <div>
                            @hostname($ip)
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Sistema Utilizado')</td>
                    <td>
                        @if ($device)
                            <div>
                                @userdevice($device)
                            </div>
                        @endif
                        @useragent($userAgent)
                    </td>
                </tr>
                <tr>
                    <td>@lang('Ubicación')</td>
                    <td>
                        <div>
                            {{ $latitude }} {{ $longitude }}
                        </div>
                        @if ($latitude && $longitude)
                            <div>
                                <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $latitude }},{{ $longitude }}">
                                    @lang('Ver en Google Maps')
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('Fecha de Acreditación')</td>
                    <td>@datetime($created)</td>
                </tr>
                </tbody>
            </table>
            <br/>
            <table>
                <thead>
                <tr>
                    <th colspan="5">@lang('Datos solicitados')</th>
                </tr>
                <tr>
                    <th>@lang('Pregunta')</th>
                    <th>@lang('Respuesta')</th>
                    <th>@lang('Respuesta verificada')</th>
                    <th>
                        @lang('Fecha de asignacion')
                        (@lang('por el usuario'))
                    </th>
                    <th>
                        @lang('Fecha de modificacion')
                        (@lang('por el validante'))
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($groupDataForm as $keyData => $dataForm)
                    <tr>
                        <td>{{ $dataForm->field_name }}</td>
                        <td>{{ $dataForm->formatfieldtext }}</td>
                        <td>
                            @if ($dataForm->formDataBackup)
                                {{ $dataForm->formDataBackup->new_field_text }}
                            @else
                                @lang('No ha sido modificado')
                            @endif
                        </td>
                        <td>@datetime($dataForm->created_at)</td>
                        <td>
                            @if ($dataForm->formDataBackup)
                                @datetime($dataForm->formDataBackup->created_at)
                            @else
                                @lang('No ha sido modificado')
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
        <br>
        {{--  comentario de la validacion  --}}
        @if($signer->getIfCommentExists(\App\Enums\ValidationType::FORM_DATA_VERIFICATION))
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
                                <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::FORM_DATA_VERIFICATION) }}
                                    "</small>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endif
    {{--/Formulario de datos --}}
@endsection
