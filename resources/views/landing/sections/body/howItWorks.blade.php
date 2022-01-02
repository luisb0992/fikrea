<section class="ftco-section">
    <h2 class="d-none">how it works</h2>
    <div class="container">
        <div class="row justify-content-center mb-5 pb-2">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <span class="subheading">@lang('Funcionalidad')</span>
                <h2 class="mb-4">@lang('Casos de éxito')</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 nav-link-wrap mb-5 pb-md-5 pb-sm-1 ftco-animate">
                <div class="nav ftco-animate nav-pills justify-content-center text-center" id="v-pills-tab"
                    role="tablist" aria-orientation="vertical">

                    <a class="nav-link active mb-4" id="v-pills-nube-tab" data-toggle="pill" href="#v-pills-nube"
                        role="tab" aria-controls="v-pills-nube" aria-selected="true">@lang('Almacenamiento en la
                        Nube')</a>

                    <a class="nav-link mb-4" id="v-pills-performance-tab" data-toggle="pill" href="#v-pills-performance"
                        role="tab" aria-controls="v-pills-performance" aria-selected="false">@lang('Comparticion de
                        Archivos')</a>

                    <a class="nav-link mb-4" id="v-pills-nextgen-tab" data-toggle="pill" href="#v-pills-nextgen"
                        role="tab" aria-controls="v-pills-nextgen" aria-selected="false">@lang('Subir y Firmar')</a>

                    <a class="nav-link mb-4" id="v-pills-extra-tab" data-toggle="pill" href="#v-pills-extra" role="tab"
                        aria-controls="v-pills-extra" aria-selected="false">@lang('Validaciones Extra')</a>

                    <a class="nav-link mb-4" id="v-pills-request-tab" data-toggle="pill" href="#v-pills-request"
                        role="tab" aria-controls="v-pills-request" aria-selected="false">@lang('Solicitud de
                        Documentos')</a>

                    <a class="nav-link mb-4" id="v-pills-date-tab" data-toggle="pill" href="#v-pills-date" role="tab"
                        aria-controls="v-pills-date" aria-selected="false">@lang('Verificación de Datos')</a>

                    <a class="nav-link mb-4" id="v-pills-effect-tab" data-toggle="pill" href="#v-pills-effect"
                        role="tab" aria-controls="v-pills-effect" aria-selected="false">@lang('Certificamos los
                        Procesos')</a>

                </div>
            </div>
            <div class="col-md-12 align-items-center ftco-animate">

                <div class="tab-content ftco-animate" id="v-pills-tabContent">

                    <div class="tab-pane fade show active" id="v-pills-nube" role="tabpanel"
                        aria-labelledby="v-pills-nube-tab">
                        <div class="d-md-flex">
                            <div class="one-forth align-self-center">
                                <img src="@asset('assets/images/landing/services/app-cloud-store.webp')"
                                    class="img-fluid border" alt="">
                            </div>
                            <div class="one-half ml-md-5 align-self-center">
                                <h2 class="mb-4">@lang('Almacenamiento en la Nube')</h2>
                                <p>
                                    @lang('Como plataforma de gestión de documentos y archivos de todo tipo, le
                                    ofrecemos un sistema de almacenamiento en la nube, donde clasificar por clientes u
                                    otro tipo, crear alertas sobre ficheros o carpetas, crear notas,.......Poner orden y
                                    tenerlo siempre a su alcance, nunca pudo ser más fácil.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-performance" role="tabpanel"
                        aria-labelledby="v-pills-performance-tab">
                        <div class="d-md-flex">
                            <div class="one-forth order-last align-self-center">
                                <img src="@asset('assets/images/landing/services/sharing-files.webp')" class="img-fluid border"
                                    alt="">
                            </div>
                            <div class="one-half order-first mr-md-5 align-self-center">
                                <h2 class="mb-4">@lang('Comparticion de Archivos')</h2>
                                <p>
                                    @lang('Desde Fikrea, podrá compartir un archivo o múltiple a x persona,
                                    compartiendo una URL para la descarga o bien insertando los datos de las personas
                                    que deben recibir dicho archivo/s. En todo momento, usted podrá saber si dicha
                                    compartición ha sido vista, descargada e incluso emitir un certificado del
                                    proceso.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade show" id="v-pills-nextgen" role="tabpanel"
                        aria-labelledby="v-pills-nextgen-tab">
                        <div class="d-md-flex">
                            <div class="one-forth align-self-center">
                                <img src="@asset('assets/images/landing/services/sign-document.webp')" class="img-fluid border"
                                    alt="">
                            </div>
                            <div class="one-half ml-md-5 align-self-center">
                                <h2 class="mb-4">@lang('Subir y Firmar')</h2>
                                <p>
                                    @lang('Con Fikrea podrá lanzar procesos certificados y de total validez legal, al
                                    cumplir con los máximos estándares exigidos en materia de seguridad y protocolos
                                    criptográficos, que darán a su empresa una herramienta única.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-extra" role="tabpanel" aria-labelledby="v-pills-extra-tab">
                        <div class="d-md-flex">
                            <div class="one-forth order-last align-self-center">
                                <img src="@asset('assets/images/landing/services/process-signature.webp')"
                                    class="img-fluid border" alt="">
                            </div>
                            <div class="one-half order-first mr-md-5 align-self-center">
                                <h2 class="mb-4">@lang('Validaciones Extra')</h2>
                                <p>
                                    @lang('Dentro de un proceso de firma, no solo podrá echar un garabato digitalmente
                                    sobre el documento que quiera, además podrá incluir en el proceso x validaciones
                                    extras, como el reconocimiento facial de firmantes, solicitud de aporte de documento
                                    de identificación, grabación contractual de audio o video, requerir X documentos a
                                    terceros, acreditar datos de titularidad y mucho más.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade show" id="v-pills-request" role="tabpanel"
                        aria-labelledby="v-pills-request-tab">
                        <div class="d-md-flex">
                            <div class="one-forth align-self-center">
                                <img src="@asset('assets/images/landing/services/required-document.webp')"
                                    class="img-fluid border" alt="">
                            </div>
                            <div class="one-half ml-md-5 align-self-center">
                                <h2 class="mb-4">@lang('Solicitud de Documentos')</h2>
                                <p>
                                    @lang('Con nuestra herramienta de solicitud de documentos, esta ganando un sistema
                                    rápido y sencillo, donde lanzar una comunicación a terceros, donde les solicite la
                                    documentación fijando entre los parámetros deseables para que FIKREA controle por
                                    usted que se cumplan como es en la fecha de expedición del documento, fecha de
                                    caducidad del mismo con alerta a los interesados de que deben actualizar, formato
                                    deseado y ocupación del archivo,.....y mucho más!!.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-date" role="tabpanel" aria-labelledby="v-pills-date-tab">
                        <div class="d-md-flex">
                            <div class="one-forth order-last align-self-center">
                                <img src="@asset('assets/images/landing/services/data-verification.webp')"
                                    class="img-fluid border" alt="">
                            </div>
                            <div class="one-half order-first mr-md-5 align-self-center">
                                <h2 class="mb-4">@lang('Verificación de Datos')</h2>
                                <p>
                                    @lang('Dentro de un proceso de firma, no solo podrá echar un garabato digitalmente
                                    sobre el documento que quiera, además podrá incluir en el proceso x validaciones
                                    extras, como el reconocimiento facial de firmantes, solicitud de aporte de documento
                                    de identificación, grabación contractual de audio o video, requerir X documentos a
                                    terceros, acreditar datos de titularidad y mucho más.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-effect" role="tabpanel" aria-labelledby="v-pills-effect-tab">
                        <div class="d-md-flex">
                            <div class="one-forth align-self-center">
                                <img src="@asset('assets/images/landing/services/certificate.webp')" class="img-fluid border"
                                    alt="">
                            </div>
                            <div class="one-half ml-md-5 align-self-center">
                                <h2 class="mb-4">@lang('Certificamos los Procesos')</h2>
                                <p>
                                    @lang('Al usar Fikrea, siempre es sinónimo de seguridad en el proceso. Cuando usted
                                    comparta, requiera X documentos a terceros o realice un proceso de firma donde se
                                    realicen X validaciones, usted esta consiguiendo obtener un certificado del proceso
                                    donde recogemos datos que le dara la certeza y seguridad, frente a reclamaciones o
                                    incidencias futuras. Utilice Fikrea y asegure sus procesos.')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
