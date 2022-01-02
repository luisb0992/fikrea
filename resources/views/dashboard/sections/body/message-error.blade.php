{{--

    Mensaje de alerta que se muestra cuando se guarda un formulario con éxito

--}}

@if(Session::has('error'))
<div class="row alert alert-danger">
    <i class="pe-7s-close-circle icon-gradient bg-mean-fruit icon"></i>
    <p>{{session('error')}}</p>
</div>
@endif
   