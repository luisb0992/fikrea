@props(['numbering' => 'II', 'app_name' => config('app.name')])

<div>
    <strong>@lang('Anexo') {{ $numbering }}</strong>
</div>
<div class="legal">
    <p>
        @lang('Base Legal de la firma electrónica (<strong>Ley 59/2003, de 19 de diciembre</strong>)'):
    </p>
    <ul>
        <li>
            <strong>(Art. 3.1)</strong>
            @lang('La firma electrónica es el conjunto de datos en forma electrónica, consignados junto a otros o
                asociados con ellos, que pueden ser utilizados como medio de identificación del firmante.')
        </li>
        <li>
            <strong>(Art. 3.2)</strong>
            @lang('La firma electrónica avanzada es la firma electrónica que permite identificar al firmante y detectar
                cualquier cambio ulterior de los datos firmados, que está vinculada al firmante de manera única y a los
                datos a que se refiere y que ha sido creada por medios que el firmante puede mantener bajo su exclusivo
                control.')
        </li>
        <li>
            <strong>(Art. 3.3)</strong>
            @lang('Se considera firma electrónica reconocida la firma electrónica avanzada basada en un certificado
                reconocido y generada mediante un dispositivo seguro de creación de firma.')
        </li>
        <li>
            <strong>(Art. 3.4)</strong>
            @lang('La firma electrónica reconocida tendrá, respecto de los datos consignados en forma electrónica, el
                mismo valor que la firma manuscrita en relación con los consignados en papel.')
        </li>
    </ul>
    <p>
        @lang('Al proceso de firma del documento se han aplicado los criterios generales correspondientes a la
            <strong>firma avanzada</strong>')
    </p>
    <p>@lang('Se ha verificado:')</p>
    <ul>
        <li>@lang('La identidad del firmante')</li>
        <li>@lang('La integridad del documento firmado')</li>
        <li>@lang('Se ha garantizado el no repudio en el origen')</li>
        <li>
            @lang('La plataforma <strong>:app</strong>, ha actuado como tercero de confianza en este proceso mediante
                una aplicación segura de creación de firma', ['app' => $app_name])
        </li>
    </ul>
</div>
