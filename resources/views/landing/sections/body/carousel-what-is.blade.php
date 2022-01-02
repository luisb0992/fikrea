<section class="section-header-landing mt-5">
    <div class="container-fluid mt-5 px-4 py-5 py-sm-0 px-sm-0">
        <div id="carousel-header" class="carousel slide mx-sm-5 mb-sm-5 p-sm-5" data-ride="carousel">
            <div class="carousel-inner px-sm-5 pt-2">

                {{--  primer carousel  --}}
                <div class="carousel-item p-sm-5 active">
                    <div class="d-flex flex-sm-row flex-column flex-column-reverse">
                        <div class="mr-auto">

                            {{--  visible de md en adelante  --}}
                            <h1 class="text-white d-none d-sm-none d-md-block">@lang('El procedimiento más rápido y sencillo de firma digital')</h1>

                            {{--  visible solo para xs y sm  --}}
                            <h3 class="text-white d-block d-sm-block d-md-none text-center text-sm-left">@lang('El procedimiento más rápido y sencillo de firma digital')</h3>
                            <p class="text-white font-weight-normal text-center text-sm-left">
                                @lang('Cumple con la ley de firma digital y la ley de protección de datos personales')
                            </p>
                            <div class="d-sm-flex">@include('landing.sections.body.partials.action-buttons')</div>
                        </div>
                        <div class="text-sm-right text-center mt-4 mt-sm-0">
                            <img src="@asset('assets/images/landing/header/fikrea-signature.webp')" class="w-75"
                                alt="@lang('signature')">
                        </div>
                    </div>
                </div>

                {{--  segundo carousel  --}}
                <div class="carousel-item p-sm-5">
                    <div class="d-flex flex-sm-row flex-column flex-column-reverse">
                        <div class="mr-auto">

                            {{--  visible de xs y sm en adelante  --}}
                            <h1 class="text-white d-none d-sm-none d-md-block">@lang('Firma y comparte tus documentos con tus colaboradores, clientes y proveedores')</h1>

                            {{--  visible solo para xs  --}}
                            <h3 class="text-white d-block d-sm-block d-md-none text-center text-sm-left">@lang('Firma y comparte tus documentos con tus colaboradores, clientes y proveedores')</h3>
                            <p class="text-white font-weight-normal text-center text-sm-left">@lang('Es simple e intuitivo')</p>
                            <div class="d-sm-flex">@include('landing.sections.body.partials.action-buttons')</div>
                        </div>
                        <div class="text-sm-right text-center mt-2 mt-sm-0 mb-sm-4">
                            <img src="@asset('assets/images/landing/header/fikrea-signature-2.webp')" class="w-75"
                                alt="@lang('documents and signature')">
                        </div>
                    </div>
                </div>
            </div>

            {{--  seleccion de carousel  --}}
            <div class="d-flex mt-sm-4 mt-5 text-center">
                <ol class="carousel-indicators">
                    <li data-target="#carousel-header" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-header" data-slide-to="1"></li>
                </ol>
            </div>
        </div>
    </div>
</section>
