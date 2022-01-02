@props(['numbering' => 'I', 'user', 'app_name' => config('app.name'), 'created_at' => now()])

<div>
    <strong>@lang('Anexo') {{ $numbering }}</strong>
</div>
<div class="legal">
    <p>
        @lang('El documento es propiedad de :authorship y ha sido registrado en :app con fecha :date',
            [
                'authorship' => "{$user->name} {$user->lastname}",
                'app'        => config('app.name'),
                'date'       => $created_at->format('d/m/Y')
            ]
        )
    </p>
    {{ $slot }}
</div>
