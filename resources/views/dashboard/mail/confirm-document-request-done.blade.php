@extends('dashboard.layouts.mail')

@section('content')

<p>
    @lang('Hola, :usuario', ['usuario' => $request->user->name]):
</p>

<p>
   @lang('El usuario :usuario ha completado una solicitud de documentos',
        ['usuario' => "{$signer->name} {$signer->lastname} {$signer->email}"]
    ).
</p>

<p>@lang('Puede consultar el estado de esta solicitud aquí'):</p>

<p class="text-center">
    <a class="btn btn-primary" href="@url(@route('dashboard.document.request.status', ['id' => $request->id]))">
        @lang('Acceder a la Solicitud')
    </a>
</p>

<p>
    @lang('Un Saludo')
</p>

@stop
