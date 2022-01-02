<thead class="thead-white">
    <tr class="text-center">
        <th class="text-capitalize text-info">
            @lang('Usuario o firmante')
        </th>

        {{-- Editor de documento --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/text-box.webp')" class="img-fluid rounded-circle icon-size-table" alt="text-box">
            <p class="mt-1">@lang('Editor de documento')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('El usuario debe completar las cajas de textos en los lugares indicados del documento.')">@lang('Ayuda')</button>
        </th>
        {{-- /Editor de documento --}}

        {{-- firma manuscrita --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/firma-digital.webp')" class="img-fluid rounded-circle icon-size-table" alt="signature">
            <p class="mt-1">@lang('Firma manuscrita digital')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('El usuario debe realizar una firma en los lugares indicados del documento.')">@lang('Ayuda')</button>
        </th>

        {{-- captura de pantalla --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/captura-pantalla.webp')" class="img-fluid rounded-circle icon-size-table" alt="screen-capture">
            <p class="mt-1">@lang('Captura de pantalla')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('El usuario deberá realizar una grabación de su pantalla mientras realiza el proceso de validación de firma manuscrita.')">@lang('Ayuda')</button>
        </th>

        {{-- verificacion de datos --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/verificacion-datos.webp')" class="img-fluid rounded-circle icon-size-table" alt="data-verification">
            <p class="mt-1">@lang('Verificación de datos')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('Verifique los datos a nivel particular o empresarial, donde se
                permitirá a la persona confirmar, editar o complementar campos del tipo
                nombre, dirección, etc.')">@lang('Ayuda')</button>
        </th>

        {{-- documento identificativo --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/reconocimiento-facial.webp')" class="img-fluid rounded-circle icon-size-table" alt="facial-recognition">
            <p class="mt-1">@lang('Documento identificativo')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('El usuario debe aportar un documento identificativo por el anverso y el
                reverso, además de una foto frontal obtenida en ese preciso momento. Esta validación solamente puede ser realizar en un móvil o tablet.')">@lang('Ayuda')</button>
        </th>

        {{-- solicitud de documentos --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/solicitud-documentos.webp')" class="img-fluid rounded-circle icon-size-table" alt="request-documents">
            <p class="mt-1">@lang('Solicitud de documentos')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('El usuario deberá aportar los documentos que usted precise. Por ejemplo, puede solicitar su documento nacional de identidad, su
                curriculum vitae, etc.')">@lang('Ayuda')</button>
        </th>

        {{-- archivo de audio --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/audio.webp')" class="img-fluid rounded-circle icon-size-table" alt="audio">
            <p class="mt-1">@lang('Archivo de audio')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('El usuario deberá realizar una grabación de audio donde se indica que
                esta de acuerdo o que valida el documento.')">@lang('Ayuda')</button>
        </th>

        {{-- archivo de video --}}
        <th class="text-capitalize text-info">
            <img src="@asset('assets/images/dashboard/images/validations/video.webp')" class="img-fluid rounded-circle icon-size-table" alt="video">
            <p class="mt-1">@lang('Archivo de video')</p>
            <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="tooltip" data-placement="bottom"
                title="@lang('El usuario deberá realizar una grabación de video donde se indica que
                esta de acuerdo o que valida el documento.')">@lang('Ayuda')</button>
        </th>
    </tr>
</thead>