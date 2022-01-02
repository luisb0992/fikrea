@props(['numbering' => 'IV', 'app_name' => config('app.name')])

<div>
    <strong>@lang('Anexo') {{ $numbering }}</strong>
</div>

<div class="legal">
    <p>
        @lang('Ley <strong>59/2003</strong>, de 19 de diciembre <a target="_blank"
            href="https://firmaelectronica.gob.es/Home/Ciudadanos/Base-Legal.html">https://firmaelectronica.gob.es/Home/Ciudadanos/Base-Legal.html</a>')
    </p>
</div>
