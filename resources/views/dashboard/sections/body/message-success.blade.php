{{--

    Mensaje de alerta que se muestra cuando se guarda un formulario con éxito

--}}

@if(Session::has('message'))
<div class="row alert alert-success">
    <i class="pe-7s-like2 icon-gradient bg-mean-fruit icon"></i>
    <p>{{session('message')}}</p>
</div>
@endif
   