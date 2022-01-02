@extends('dashboard.layouts.mail')

@section('content')

<p>
    @lang('Hola, :usuario', ['usuario' => $validation->document->user->name]):
</p>

<p>
   @lang('El usuario :usuario ha completado un proceso de validación en su documento',
        ['usuario' => "{$validation->signer->name} {$validation->signer->lastname} {$validation->signer->email}"]
    ).
   @lang('Se detalla, a continuación, los detalles de este proceso'):
</p>

<div>
    <table class="table">
        <tbody>
            <tr>
                <td class="text-bold">@lang('Documento')</td>
                <td>{{$validation->document->name}}</td>
            </tr>
            <tr>
                <td class="text-bold">@lang('Firmante')</td>
                <td>
                    {{$validation->signer->name}}
                    {{$validation->signer->lastname}}
                    <a href="mailto:{{$validation->signer->email}}">
                        {{$validation->signer->email}}
                    </a>
                </td>
            </tr>
            <tr>
                <td class="text-bold">@lang('Validación')</td>
                <td>
                    @validation($validation)
                </td>
            </tr>
            <tr>
                <td class="text-bold">@lang('Fecha')</td>
                <td>@datetime($validation->validated_at)</td>
            </tr>
        </tbody>
    </table>
</div>

<p>@lang('Puede consultar el estado de validación de este documento aquí'):</p>

<p class="text-center">
    <a class="btn btn-primary" href="@url(@route('dashboard.document.status', ['id' => $validation->document->id]))">
        @lang('Acceder al Documento')
    </a>
</p>

<p>
    @lang('Un Saludo')
</p>

@stop
