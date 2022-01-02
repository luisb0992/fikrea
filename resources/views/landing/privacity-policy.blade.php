<!DOCTYPE html>
<html lang="@locale">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@config('app.name') -> @lang('Política de Privacidad')</title>
</head>
<body>
    {{-- Carga la política de privacidad --}}
    @include('landing.modals.legal.docs.privacity-document')
    {{--/Carga la política de privacidad --}}
</body>
</html>