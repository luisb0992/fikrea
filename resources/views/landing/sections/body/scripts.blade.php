{{-- Jquery, plugins de jquery y otra librerías necesarias para el landing --}}
<script src="@asset('assets/js/landing/vendor/jquery.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/jquery-migrate-3.0.1.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/popper.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/bootstrap.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/jquery.easing.1.3.js')"></script>
<script src="@asset('assets/js/landing/vendor/jquery.waypoints.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/jquery.stellar.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/owl.carousel.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/jquery.magnific-popup.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/aos.js')"></script>
<script src="@asset('assets/js/landing/vendor/jquery.animateNumber.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/bootstrap-datepicker.js')"></script>
<script src="@asset('assets/js/landing/vendor/jquery.timepicker.min.js')"></script>
<script src="@asset('assets/js/landing/vendor/scrollax.min.js')"></script>

{{-- Principal del landing --}}
<script src="@asset('assets/js/landing/vendor/main.js')"></script>

{{-- Carga vue.js
     Utilizando una versión u otra optimizada según estemos en desarrollo o producción 
     @link https://es.vuejs.org/v2/guide/installation.html   
--}}
@production
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js"></script>
@else
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
@endproduction

{{-- Carga axios 
    @link https://github.com/axios/axios
--}}
<script src="@asset('assets/js/landing/vendor/axios.min.js')"></script>
