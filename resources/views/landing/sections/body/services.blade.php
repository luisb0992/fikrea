<section class="ftco-section services-section bg-light">
    <div class="container-fluid px-sm-5">

        {{-- Botón de llamada a usar la aplicación de forma gratuita --}}
        @include('landing.sections.body.call-to-action-free')
        {{-- /Botón de llamada a usar la aplicación de forma gratuita --}}

        <div class="row justify-content-center my-5 py-5">
            <div class="col-md-12 text-center heading-section ftco-animate">

                <h2 class="mb-4">@lang('¿Por qué usar :app y no otra app?', ['app' => config('app.name')])</h2>
                <p class="subheading">
                    @lang(':app es la aplicación de gestión de documentos más completa del mercado', ['app' =>
                    config('app.name')])
                </p>
                <p class="subheading">
                    @lang('¿Quieres saber más sobre que te puede ofrecer nuestra app?')
                </p>
            </div>
        </div>

        {{-- Todas las imagenes fueron convertidas a formato .webp
            Utilizar este formato es recomendable para la carga de la web --}}
        {{-- card horizontal --}}
        <div class="row px-sm-5">

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <img src="@asset('assets/images/landing/services/sign-document.webp')"
                                class="card-img max-min-size-image" alt="" />
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="heading text-primary">@lang('Firme digitalmente desde cualquier lugar')</h3>
                                <p>
                                    @lang('Utiliza el sistema más completo del mercado y asegúrate con :app de que las
                                    firmas cumplan los estándares exigidos, para considerarla válida a todos los
                                    efectos.', [
                                    'app' => config('app.name')
                                    ])
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <img src="@asset('assets/images/landing/services/process-signature.webp')"
                                class="card-img max-min-size-image" />
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="heading text-primary">@lang('¡Suma a tu proceso de firma digital validaciones
                                    únicas!')
                                </h3>
                                <p>
                                    @lang('Solicita información sobre el documento a firmar,
                                    que aporte documentación con cualquier requerimiento, captura el proceso mientras lo
                                    realiza, solicita grabación de audio o video con
                                    lectura de contrato, pide que confirme o verifique sus datos e incluso solicita un
                                    reconocimiento facial junto con su documento
                                    de identificación.')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <img src="@asset('assets/images/landing/services/app-cloud-store.png')"
                                class="card-img max-min-size-image" alt="" />
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="heading text-primary">@lang('Almacenamiento en la nube')</h3>
                                <p>
                                    @lang('Gestiona tu documentación con nuestro sistema de carpetas, donde podrá subir
                                    archivos pesados, recordatorios sobre documentos. Además, podrá usar de manera
                                    gratuita hasta :maxsize :type de almacenamiento.', [
                                    'maxsize' => @config('files.max.size'),
                                    'type' => 'GB'
                                    ])
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <img src="@asset('assets/images/landing/services/sharing-files.webp')"
                                class="card-img max-min-size-image" alt="" />
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="heading text-primary">@lang('Compartir archivos de una manera fácil y
                                    segura')</h3>
                                <p>
                                    @lang('Al compartir tus archivos, sean del tamaño que sean, podrá obtener una
                                    visión de las veces que visualizo y
                                    descargo. Incluso certificar el proceso, <b>¡NADIE TE DIO MÁS!.</b>')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <img src="@asset('assets/images/landing/services/required-document.webp')"
                                class="card-img max-min-size-image" alt="" />
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="heading text-primary">
                                    @lang('Usa :app para solicitar documentación', ['app' => config('app.name')])
                                </h3>
                                <p>
                                    @lang('Requiere a tus empleados, colaboradores, la documentación que precises en un
                                    formato concreto, con la vigencia que tu definas y de manera automática')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <img src="@asset('assets/images/landing/services/data-verification.webp')"
                                class="card-img max-min-size-image" alt="" />
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="heading text-primary">
                                    @lang('Verifica y certifica los datos con nuestro sistema')
                                </h3>
                                <p>
                                    @lang('Nuestro sistema te permitirá enviar una petición a cualquier persona, para
                                    que
                                    verifiquen su información y puedas certificar dicho proceso como es en caso de
                                    <b>ALTA DE CLIENTES o PROVEEDORES,
                                        FINANCIACIONES, CONTRATACIÓN DE NUEVOS SERVICIOS.</b>')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <img src="@asset('assets/images/landing/services/create-new-document.webp')"
                                class="card-img max-min-size-image" alt="" />
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="heading text-primary">@lang('Cree su documento desde cero')</h3>
                                <p>
                                    @lang('¿Alguna vez te pidieron una autorización firmada o redactar un documento para
                                    que te lo firme alguien? Estés donde estés, cree su documento fácilmente para que tú
                                    u otra persona lo firme.')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
