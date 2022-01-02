{{--
    Librerías Javascript necesarias para el Dashboard

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos
--}}

{{-- Carga Vue.js --}}
@production
<script src="@asset('assets/js/vue/vue.min.js')"></script>
@else
<script src="@asset('assets/js/vue/vue.js')"></script>
@endproduction

{{--
    Bootstrap Vue
    @link https://bootstrap-vue.org/
--}}
<script src="@asset('assets/js/vue/polyfill.min.js')"></script>
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

{{--
    vuejs-datepicker
    @link https://www.npmjs.com/package/vuejs-datepicker
--}}
<script src="@asset('assets/js/vue/vue-datepicker/vuejs-datepicker.min.js')"></script>
<script src="@asset('assets/js/vue/vue-datepicker/es.js')"></script>

{{--
    v-tooltip
    @link https://github.com/Akryum/v-tooltip
--}}
<script src="@asset('assets/js/vue/v-tooltip.min.js')"></script>

{{-- 
    Carga axios
    @link https://github.com/axios/axios
--}}
<script src="@asset('assets/js/dashboard/vendor/axios.min.js')"></script>

{{--
    Añade el token CSRF automáticamente
    a todas las peticiones AJAX efectuadas con Axios
    @link https://laracasts.com/discuss/channels/general-discussion/how-can-add-csrf-token-in-axios-post
    @link https://laravel.com/docs/csrf#csrf-x-csrf-token
--}}
<script src="@asset('assets/js/csrf/axios.csrf.js')"></script>

{{-- Jquery y popper 
     @link https://jquery.com/    
--}}
<script src="@asset('assets/js/dashboard/vendor/jquery.min.js')"></script>
<script src="@asset('assets/js/dashboard/vendor/popper.min.js')"></script>

{{--
    Toastr 
    @link https://github.com/CodeSeven/toastr

    Configuración de librerías javascript utilizadas
    Por ejemplo: toastr
--}}
<script src="@asset('assets/js/dashboard/vendor/toastr.min.js')"></script>
<script src="@mix('assets/js/config/config.js')"></script>

{{--
    HoldOn 
    @link https://sdkcarlos.github.io/sites/holdon.html
--}}
<script src="@asset('assets/js/dashboard/vendor/HoldOn.min.js')"></script>

{{--
    interact.js 
    @link https://cdnjs.com/libraries/interact.js/1.0.2
--}}
<script src="@asset('assets/js/libs/interact.min.js')"></script>

{{--
    El script principal del dashboard
    No modificar en ningún caso
--}}
<script src="@asset('assets/js/dashboard/vendor/main.js')"></script>

{{--
    La búsqueda de documentos y archivos
--}}
<script src="@mix('assets/js/dashboard/search.js')"></script>

{{--
    Funcionalidad del botón scroll-up
--}}
<script src="@mix('assets/js/common/scroll-to-top.js')"></script>

{{-- Funcionalidades del menú --}}
<script src="@mix('assets/js/common/app-menu.js')"></script>

{{--  Manejo de comentarios globales en proceso de valdiacion o de forma independiente  --}}
<script src="@mix('assets/js/common/global-comment.js')"></script>

{{-- copiar un documento de diferntes ubicaciones en archivos  --}}
<script src="@mix('assets/js/common/copy-document.js')"></script>