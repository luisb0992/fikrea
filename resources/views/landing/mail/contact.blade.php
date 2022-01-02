@extends('landing.layouts.mail')

@section('content')

{{--
    Mensaje para el administrador de un contacto creado
    desde el formulario de contacto
--}}

<p>
	@lang('Hola, Administrador'):
</p>
<p>
    El usuario <strong><a href="mailto:{{$contact->email}}">{{$contact->email}}</a></strong> desea contactar contigo. 
</p>

<div>
    <table class="table">
        <thead>
            <tr>
                <th colspan="2">@lang('Contacto')</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-info">@lang('Dirección de Correo') :</td>
                <td>
                    <a href="{{$contact->email}}">
                        {{$contact->email}}
                    </a>
                </td>
            </tr>
            <tr>
                <td class="text-info">@lang('Asunto') :</td>
                <td>{{$contact->subject}}</td>
            </tr>
            <tr>
                <td class="text-info">@lang('Contenido') :</td>
                <td>{{$contact->content}}</td>
            </tr>
        </tbody>
    </table>
</div>
<p class="text-right">
    @lang('Gracias')
</p>
@stop
