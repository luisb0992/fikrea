{{--
    Librerías Javascript necearias paara el Dashboard

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos
--}}

{{-- Carga Vue.js

     La versión que corresponde según estamos en entorno de producción o desarrollo
--}}
@production
{{-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js"></script> --}}
<script src="@asset('assets/js/vue/vue.min.js')"></script>
@else
{{-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> --}}
<script src="@asset('assets/js/vue/vue.js')"></script>
@endproduction

{{--
     Bootstrap Vue
     
     @link https://bootstrap-vue.org/ 
--}}
<script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
{{-- <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script> --}}
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

{{--
    vuejs-datepicker

    @link https://www.npmjs.com/package/vuejs-datepicker
--}}
@production
<script src="https://unpkg.com/vuejs-datepicker"></script>
<script src="https://unpkg.com/vuejs-datepicker/dist/locale/translations/@locale.js"></script>
@else
<script src="@asset('assets/js/vue/vue-datepicker/vuejs-datepicker.min.js')"></script>
<script src="@asset('assets/js/vue/vue-datepicker/es.js')"></script>
@endproduction



{{--
    v-tooltip

    @link https://github.com/Akryum/v-tooltip
--}}

<script src="https://unpkg.com/v-tooltip"></script>

{{-- 
    Carga axios

    @link https://github.com/axios/axios
--}}
<script src="@asset('assets/js/dashboard/vendor/axios.min.js')"></script>

{{-- Añade el token CSRF automáticamente
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
--}}
<script src="@asset('assets/js/dashboard/vendor/toastr.min.js')"></script>

{{--
    HoldOn 
    @link https://sdkcarlos.github.io/sites/holdon.html
--}}
<script src="@asset('assets/js/dashboard/vendor/HoldOn.min.js')"></script>

{{--
    interact.js 
    @link https://cdnjs.com/libraries/interact.js/1.0.2
--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/interact.js/1.0.2/interact.min.js" integrity="sha512-Ipef/NhGC7SlCer4041clfWKsriVhnGMcS15cVrl9j/NFtNmqeK28tbfOFwASlSfy4j8XjcpVw4HXvTvzgA8rA==" crossorigin="anonymous"></script>

{{--
    El script principal del dashboard

    No modificar en ningún caso
--}}
<script src="@asset('assets/js/dashboard/vendor/main.js')"></script>

{{--
    Configuración de librerías javascript utilizadas

    Por ejemplo: toastr
--}}
<script src="@mix('assets/js/config/config.js')"></script>

{{--
    La búsqueda de documentos y archivos
--}}
<script src="@mix('assets/js/dashboard/search.js')"></script>
