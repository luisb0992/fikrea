@props(['numbering' => 'III', 'app_name' => config('app.name')])

<div>
    <strong>@lang('Anexo') {{ $numbering }}</strong>
</div>

<div class="legal">
    <p>
        @lang('Fikrea además proporciona en los procesos diferentes herramientas donde poder verificar con garantía de
            cumplimiento LA AUTENTICIDAD DEL ORIGEN Y LA INTEGRIDAD DEL CONTENIDO con los estándares más estrictos a
            nivel internacional para las siguientes acciones:')
    </p>
    <ul>
        <li>
            @lang('La compartición de archivos de manera telemática a terceros mediante diferentes mecanismos,
                recopilando información trascendental para la certificación del proceso, como la comunicación al tercero
                bien por notificación o compartición directa de una URL que emplea un token identificativo para que el
                receptor sea redirigido al área de trabajo en FIKREA donde poder visualizar y descargar dicha
                información. En este acto se captura la dirección IP del receptor, el dispositivo empleado para dicha
                visita, fecha y hora en la que se desarrolla la acción (visualización, descarga, rechazo de la
                compartición, entre otras), generando un histórico del proceso.')
        </li>
        <li>
            @lang('El envío de documentación puede ser bidireccional en cualquiera de los procesos, pudiendo requerir a
                terceros una serie de elementos que aportar, cumpliendo una serie de parámetros estipulados por el
                usuario de FIKREA y que cumplen ciertas normativas de requerimientos como son fechas de expedición,
                fechas de caducidad, limitación de caracteres, entre otros, empleando el mismo sistema de
                comunicación/gestión de la información que en el punto anterior.')
            <ul>
                <li>
                    @lang('Además, la aplicación FIKREA proporciona a sus usuarios una herramienta digital donde
                        constatar la información de un tercero enviando una solicitud que contiene un grupo de elementos
                        y que ha sido previamente cumplimentada o no por parte del usuario de nuestra aplicación, para
                        que dicha persona mediante algún mecanismo acceda al área de trabajo y revise, modifique y/o
                        confirme que la información sobre su persona/entidad se ajusta y es veraz a todos los efectos,
                        pudiendo acceder en cualquier momento para su modificación o eliminación.')
                </li>
            </ul>
        </li>
        <li>
            @lang('Con todo esto, FIKREA proporciona la custodia de la información para nuestros usuarios, donde se le
                proporciona al usuario un sistema de compartición de archivos que se notifica al tercero empleando los
                mismos mecanismos/sistemas mencionados anteriormente, que le permitirá acceder al área de trabajo donde
                puede previsualizar el contenido pero nunca descargar/capturar/grabar por este tercero hasta no quedar
                resuelto el coste estipulado por nuestro usuario y que gestiona FIKREA como medio empleado, pero sin ser
                responsable a ningún efecto de reclamación en base al contenido/acuerdo alcanzado entre las partes,
                quedando únicamente como medio de envío/cobro.')
        </li>
    </ul>
</div>
