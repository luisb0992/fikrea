{{--
    Visualiza un cuadro con las validaciones que debe realizar un firmante de un documento
    Cada validación es una acción que el usuario debe efectuar para aprobar un documento
--}}

<div class="row no-gutters w-100">
    <div class="col my-4">
        <div class="table-responsive bg-white">
            <table class="table table-hover">
                @include('dashboard.partials.validations.table-header-validations')
                <tbody>
                    @foreach ($document->signers as $signer)
                        <tr>
                            {{-- El usuario --}}
                            @include('dashboard.partials.validations.user-in-validation')
                            {{-- /El usuario --}}

                            {{-- Editor de documento --}}
                            @include('dashboard.partials.validations.document-editor-validation')
                            {{-- /Editor de documento --}}

                            {{-- Firma manuscrita --}}
                            @include('dashboard.partials.validations.handwritten-signature-validation')
                            {{-- /Firma manuscrita --}}

                            {{-- Captura de pantalla --}}
                            @include('dashboard.partials.validations.screen-capture-validation')
                            {{-- /Captura de pantalla --}}

                            {{-- Verificación de datos --}}
                            @include('dashboard.partials.validations.data-verification-validation')
                            {{-- /Verificación de datos --}}

                            {{-- Documento identificativo --}}
                            @include('dashboard.partials.validations.passport-validation')
                            {{-- /Documento identificativo --}}

                            {{-- Solicitud de documentos --}}
                            @include('dashboard.partials.validations.document-request-validation')
                            {{-- /Solicitud de documentos --}}

                            {{-- Archivo de audio --}}
                            @include('dashboard.partials.validations.audio-validation')
                            {{-- /Archivo de audio --}}

                            {{-- Archivo de video --}}
                            @include('dashboard.partials.validations.video-validation')
                            {{-- /Archivo de video --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
