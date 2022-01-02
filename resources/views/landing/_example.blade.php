{{--
    Ejemplo de vista con los estilos del Landing Page

    Incluye el menú, el pie de página

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos
--}}

@extends('landing.layouts.main')

{{-- Título de la Página --}}
@section('title', __('El título de la página'))

{{-- Css Personalizado --}}
@section('css')
@stop

@section('body')

{{-- Menú de la aplicación --}}
@include('landing.sections.body.menu')

{{-- Contenido --}}
<section class="ftco-section content-section mt-100">
    <div class="container">
        
        {{--
                *** COPIAME PERO NO ME SOBREESCRIBAS ***
                javieru <javi@gestoy.com>

                Escribe aquí el contenido de la página

                Nota
                ====
        
                Las plantillas de Blade sólo muestran datos que vienen ya procesados del controlador
                No llevan lógica implícta, ni se declaran variables, ni poseen código en PHP

                Las plantillas no incluyen javascript, ni estilos en línea.

                1. Trabajar con CSS con Webpack
                ===============================

                Define tu archivo de estilos en la sección Css Personalizado
                y cárgalo con la directiva @mix, por ejemplo:

                <link rel="stylesheet" href="@mix('assets/css/my-folder/my-style.css')" />

                Para ello, deberas crear un archivo sass en:

                /resources/sass/my-folder/my-style.scss

                que será transpilado a css por webpack y publicado en:

                /public/assets/css/my-folder/my-style.css

                Para ello añadir al webpack.min.js la operación:

                mi.sass('resources/sass/my-folder/my-style.scss', 'public/assets/css/my-folder');

                2. Trabajar con Javascript con Webpack
                ===================================

                Define tu archivo de javascript en el la sección de Scripts personalizados:

                <script src="@mix('assets/js/my-folder/my-script.js')" />

                El archivo:

                /resources/js/my-folder/my-script.js

                que será publicado en:

                /public/assets/js/my-folder/my-script.js

                Para ello añadir al webpack.min.js la operación:

                mix.js('resources/js/my-folder/my-script.js', 'public/assets/js/my-folder');

        --}}

    </div>
</section>
{{-- /Contenido --}}

{{-- Pié de página --}}
@include('landing.sections.body.footer')

{{-- Loader --}}
@include('landing.sections.body.loader')

@stop

{{-- Scripts Personalizados --}}
@section('scripts')
@stop
