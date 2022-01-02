<html lang="@locale">
<head>
    <link href="https://fonts.googleapis.com/css?family=Oxygen&display=swap" rel="stylesheet"/>
    <title>@config('app.name') → @yield('title')</title>

    <style>
        .watermark {
            position: fixed;
            bottom: 150px;
            margin-left: 200px;
            width: 640px;
            height: 480px;
            opacity: .2;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            font-size: 12px;
            color: rgb(65, 65, 65);
            width: 100%;
            text-align: right;
        }

        body {
            font-family: 'Oxygen', sans-serif;
            background-repeat: no-repeat;
            background-position: center;
            color: #042b50;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            font-size: 12px;
            color: rgb(224, 220, 220);
            width: 100%;
            height: 3rem;
        }

        .break {
            page-break-before: always;
            height: 1.5em;
        }

        .pagenum:before {
            color: rgb(2, 34, 42);
            content: counter(page);
        }

        .bold {
            font-weight: 1000;
        }

        .small {
            font-size: 10px;
        }

        .medium {
            font-size: 12px;
        }

        .legal {
            font-style: italic;
            font-size: 14px;
        }

        table {
            font-size: 20px;
            font-family: verdana;
            border-spacing: 0;
            border: 1px solid #266eb6;
            width: 100%;
        }

        th {
            color: white;
            background: #266eb6;
            padding: 5px;
            border: 1px solid #266eb6;
        }

        td {
            color: black;
            background: white;
            padding: 5px;
            border: 1px solid #266eb6;
        }

        table.no-bordered, table.no-bordered tr, table.no-bordered th, table.no-bordered td {
            border: none;
            font-size: 14px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .w-20 {
            width: 20%;
        }

        .w-25 {
            width: 25%;
        }

        .w-50 {
            width: 50%;
        }

        .w-80 {
            width: 80%;
        }

        .w-100 {
            width: 100%;
        }

        .mt {
            margin-top: 20px;
        }

        .mb {
            margin-bottom: 20px;
        }

        .ml {
            margin-left: 20px;
        }

        .mr {
            margin-right: 20px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            max-width: 150px;
            max-height: 150px;
        }

        .img-signature {
            width: 250px;
            height: 200px;
            max-width: 250px;
            max-height: 200px;
        }
    </style>
</head>
<body>
<div class="watermark">
    <img class="w-50" src="@path('/assets/images/common/cee-trusted-third-party.png')" alt=""/>
</div>

<header>
    @yield('page-header')
</header>

{{--Pié de todas las páginas --}}
<footer>
    <table class="no-bordered">
        <tr>
            <td class="small">
                @lang('Certificado por :app <a href=":url">:url</a>', ['app' => @config('app.name'), 'url' => @config('app.url')])
                |
                @config('certified.name') @config('certified.cif') |
                @config('certified.street')  @config('certified.zip')
                @config('certified.city') @config('certified.country') |
                @config('certified.record.register') |
                @lang('registro') @config('certified.record.number') |
                @lang('tomo') @config('certified.record.volume') |
                @lang('sección') @config('certified.record.section') |
                @lang('folio') @config('certified.record.invoice') |
                @lang('página') @config('certified.record.page') |
                @lang('inscripción') @config('certified.record.inscription')
            </td>
        </tr>
        <tr>
            <td class="w-100 text-right">[ <span class="pagenum"></span> ]</td>
        </tr>
    </table>
</footer>
{{--/Pié de todas las páginas --}}

{{-- Logo --}}
<div class="text-right">
    <img class="w-100" src="@path('/assets/images/common/fikrea-large-logo.png')" alt=""/>
</div>
{{--/Logo --}}

<main>
    <h1>@lang('Informe Acreditativo')</h1>
    <h1>@yield('document-guid')</h1>
    <p>@lang('Documento expedido a :date por :company', 
            [
                'date'      => now()->format('d/m/Y H:i'),
                'company'   => config('company.name'),
            ]
        )
    </p>
    <br/>
    @include('common.layouts.include.certificate-goal')
    <div class="break"></div>
    @include('common.layouts.include.certificate-certification-entity')
    <div class="break"></div>
    @include('common.layouts.include.certificate-remarks')
    <div class="break"></div>
    {{-- Sección de datos específicaos, según el tipo de certificado --}}
    <h3>4) @lang('Datos adicionales recopilados')</h3>
    @yield('document-data')
    <div class="break"></div>
    {{-- Fin de la sección de datos específicaos, según el tipo de certificado --}}

    {{-- Anexos comunes a todos los certificados --}}
    <div class="page">
        <h3>5) @lang('Anexos')</h3>
        <ul>
            {{-- Enlace de descarga --}}
            <li class="mt">
                @yield('first-attachment-customized')
            </li>
            {{-- / Enlace de descarga --}}

            {{-- Base legal --}}
            <li class="mt">
                <x-certificates.certificate-attach-ii>
                </x-certificates.certificate-attach-ii>
            </li>
            {{-- / Base legal --}}

            {{-- Próxima página--}}
            <div class="break"></div>
            {{-- / Próxima página--}}

            {{-- Herramientas Fikrea --}}
            <li class="mt">
                <x-certificates.certificate-attach-iii>
                </x-certificates.certificate-attach-iii>
            </li>
            {{-- / Herramientas Fikrea --}}

            {{-- Enlace a Ley 59/2003 --}}
            <li class="mt">
                <x-certificates.certificate-attach-iv>
                </x-certificates.certificate-attach-iv>
            </li>
            {{-- / Enlace a Ley 59/2003 --}}

            @yield('additional-attachments')

        </ul>
    </div>
    {{-- /Anexos comunes a todos los certificados --}}
</main>
</body>
</html>