{{--
    Layout de la paǵina de Dashboard

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos    
--}}
<!doctype html>
<html lang="@locale">

<head>

    {{-- El título de la página --}}
    <title>@config('app.name') → @yield('title')</title>

    @include('backend.sections.head.meta')
    @include('backend.sections.head.csrf')
    @include('backend.sections.head.favicon')

    @include('backend.sections.head.css')

    {{-- El css personalizado --}}
    @section('css')
    @show        

</head>

<body>

    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
      
        @include('backend.sections.body.navbar')
        
        <div class="app-main">

            @include('backend.sections.body.menu')
 
            @include('backend.sections.body.content')
 
        </div>
    </div>
   
    @include('backend.sections.body.footer')


    {{-- Modales --}}
    @include('dashboard.modals.languages.language-selector')
    {{-- /Modales --}}
    
    @include('backend.sections.body.scripts')
    @section('scripts')
    @show
</body>

</html>