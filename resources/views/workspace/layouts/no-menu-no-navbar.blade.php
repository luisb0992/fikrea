{{--
    Layout sin menú de la paǵina de Dashboard

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos    
--}}
<!doctype html>
<html lang="@locale">

<head>

    {{-- Google Anayltics global tag --}}
    @include('vendor.google.analytics.gtag')

    {{-- El título de la página --}}
    <title>@config('app.name') → @yield('title')</title>

    @include('dashboard.sections.head.meta')
    @include('dashboard.sections.head.csrf')
    @include('dashboard.sections.head.favicon')

    @include('dashboard.sections.head.css')

    {{-- El css personalizado --}}
    @section('css')
    @show        

</head>

<body>

    <div class="app-container app-theme-white body-tabs-shadow fixed-header">
   
        <div class="app-main">
 
            <div class="app-main__outer">
                <div class="app-main__inner">
                        
                    <div class="row">
                        @section('content')
                        {{-- El contenido de la página --}}
                        @show
                    </div>
            
                </div>
            </div>
 
        </div>
    </div>
   
    @include('dashboard.sections.body.footer')


    {{-- Modales --}}
    @include('dashboard.modals.languages.language-selector')
    {{-- /Modales --}}
    
    @include('dashboard.sections.body.scripts')
    @section('scripts')
    @show
</body>

</html>