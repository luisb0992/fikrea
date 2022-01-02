<div class="app-main__outer">
    <div class="app-main__inner">
        
        <div class="app-page-title">

            {{-- Renovación de la subscripción --}}
            @include('dashboard.sections.body.subscription')
            {{--/Renovación de la subscripción --}}

            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-light icon-gradient bg-mean-fruit">
                        </i>
                    </div>
                    @section('help')
                    @show
                </div>
            </div>
        </div>

        <div class="row" id="app-content">
            @section('content')
            {{-- El contenido de la página --}}
            @show
        </div>

    </div>
</div>