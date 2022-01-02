{{-- Listado de las validaciones del firmante --}}
@foreach ($signer->validations()->filter(
    fn($validation) => $validation->validation != \App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION) as $validation
)
   
    @if ($validation->validation == \App\Enums\ValidationType::DOCUMENT_REQUEST_VERIFICATION)
    <li class="list-group-item-request mt-2">
    @else
    <li class="list-group-item">
    @endif
    
        <i class="far check fa-2x {{$validation->getIconStatus()}}"></i>
        @if ($validation->done())
        <span class="text text-success">
        @elseif ($validation->canceled())
        <span class="text text-danger">
        @else
        <span class="text">
        @endif

        {{-- Muestra el texto descriptivo de la validación --}}
        @validation($validation)
        {{--/Muestra el texto descriptivo de la validación --}}

        </span>

        {{-- El status de la validación --}}
        <a href="#"
            class="btn btn-workstatus d-none pull-right d-sm-block btn-{{$validation->process->workspaceStatus->getColor()}} disabled">
            {{ (string) \App\Enums\WorkspaceStatu::fromValue($validation->process->workspace_statu_id) }}
        </a><br/>
        {{-- /El status de la validación --}}
    
       
        {{-- Modales necesarias --}}
        @include('workspace.modals.cancel-request-file')
        {{--/Modales necesarias --}}

        <div class="text-right">
            
            @switch ($validation->validation)

                {{-- Cuando es una validación de editor de documentos --}}
                @case(\App\Enums\ValidationType::TEXT_BOX_VERIFICATION)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('En el documento se han fijado cajas de texto que debe cumplimentar con los textos que se describen en las mismas').
                    </div>
                    @if ($validation->signer->mustBeValidateByScreenCapture()
                        && $validation->validated)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('Usted ha grabado su pantalla durante el proceso de firma manuscrita cumpliendo con lo requerido por el solicitante').
                    </div>
                    @endif

                    @if ($validation->pending())
                        {{--Si se debe validar con captura de pantalla--}}
                        @include('workspace.home.required-capture-screen-text')
                        {{--/Si se debe validar con captura de pantalla--}}
                        
                        @include('workspace.home.access-for-doit-text')

                        <a href="@route('workspace.validate.textboxs', ['token' => $token])" class="btn btn-success">
                            @lang('ACCEDER')
                        </a>
                        <a href="#" @click.prevent="showCancelRequestModal({{$validation->id}})" class="btn btn-danger">
                            @lang('Rechazar')
                        </a>
                    @endif
                @break
                {{-- /Cuando es una validación de editor de documentos --}}

                {{-- Cuando es una validación de firma manuscrita --}}
                @case(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('En el documento se han fijado unas posiciones donde deberá realizar su firma utilizando, para ello, el dedo o el ratón').
                    </div>
                    @if ($validation->signer->mustBeValidateByScreenCapture() && $validation->validated)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('Usted ha grabado su pantalla durante el proceso de firma manuscrita cumpliendo con lo requerido por el solicitante').
                    </div>
                    @endif

                    @if ($validation->pending())
                        {{--Si se debe validar con captura de pantalla--}}
                        @include('workspace.home.required-capture-screen-text')
                        {{--/Si se debe validar con captura de pantalla--}}
                        
                        @include('workspace.home.access-for-doit-text')

                        <a href="@route('workspace.validate.signature', ['token' => $token])" class="btn btn-success">
                            @lang('ACCEDER')
                        </a>
                        <a href="#" @click.prevent="showCancelRequestModal({{$validation->id}})" class="btn btn-danger">
                            @lang('Rechazar')
                        </a>
                    @endif
                @break
                {{-- /Cuando es una validación de firma manuscrita --}}

                {{-- Cuando es una validación de audio --}}
                @case(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('Se le va a solicitar que realice una breve grabación de audio donde deberá el leer el texto propuesto').
                    </div>
                    @if ($validation->pending())      
                    
                        @include('workspace.home.access-for-doit-text')
                        
                        <a href="@route('workspace.validate.audio', ['token' => $token])" class="btn btn-success"   >   
                            @lang('ACCEDER')
                        </a>
                        <a href="#" @click.prevent="showCancelRequestModal({{$validation->id}})" class="btn btn-danger">
                            @lang('Rechazar')
                        </a>
                    @endif
                @break
                {{-- /Cuando es una validación de audio --}}
                
                {{-- Cuando es una validación de video --}}
                @case(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('Se le va a solicitar que realice una breve grabación de video donde deberá el leer el texto propuesto').
                    </div>
                    @if ($validation->pending())
                        @include('workspace.home.access-for-doit-text')

                        <a href="@route('workspace.validate.video', ['token' => $token])" class="btn btn-success">
                            @lang('ACCEDER')
                        </a>
                        <a href="#" @click.prevent="showCancelRequestModal({{$validation->id}})" class="btn btn-danger">
                            @lang('Rechazar')
                        </a>
                    @endif    
                @break
                {{-- /Cuando es una validación de video --}}
                
                {{-- Cuando es una validación con documento identificativo --}}
                @case(\App\Enums\ValidationType::PASSPORT_VERIFICATION)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('Se le va a pedir que obtenga una foto frontal suya y adjunte un documento que acredite su identidad (anverso y reverso)').
                    </div>
                    @if ($validation->pending())
                        @include('workspace.home.access-for-doit-text')
                        
                        <a href="@route('workspace.validate.passport', ['token' => $token])" class="btn btn-success">
                            @lang('ACCEDER')
                        </a>
                        <a href="#" @click.prevent="showCancelRequestModal({{$validation->id}})" class="btn btn-danger">
                            @lang('Rechazar')
                        </a>
                    @endif 
                @break
                {{-- /Cuando es una validación con documento identificativo --}}

                {{-- Cuando es una validación de verificación de datos --}}
                @case(\App\Enums\ValidationType::FORM_DATA_VERIFICATION)
                    <div class="text-secondary small mb-4 ml-5 text-justify">
                        @lang('En este proceso le solicitamos que revise la información que ha sido introducida por el solicitante
                        en cada campo del formulario y en caso de ausencia de texto, complemente usted la información que le solicita').
                        
                    </div>
                    @if ($validation->pending())
                        @include('workspace.home.access-for-doit-text')

                        <a href="@route('workspace.validate.formdata', ['token' => $token])"
                            class="btn btn-success">
                            @lang('ACCEDER')
                        </a>
                        <a href="#" @click.prevent="showCancelRequestModal({{$validation->id}})" class="btn btn-danger">
                            @lang('Rechazar')
                        </a>
                    @endif
                @break
                {{-- /Cuando es una validación de verificación de datos --}}

                {{-- Cuando es una validación de solicitud de documentos --}}
                @case(\App\Enums\ValidationType::DOCUMENT_REQUEST_VERIFICATION)

                    {{-- En el caso que se haya creado la validación y no se haya asignado la solicitud al signer --}}
                    @if ($validation->signer->request())
                        {{-- Detalles de la solicitud de documentos --}}
                        @include('workspace.home.request-details',
                            [
                                'request'   =>  $validation->signer->request(),
                                'nocomment' =>  true,
                            ]
                        )
                        {{-- /Detalles de la solicitud de documentos --}}
                        
                        @if($validation->pending())
                            @include('workspace.home.access-for-doit-text')
                            
                            <a href="@route('workspace.document.request', ['token' => $token])" class="btn btn-success">
                                @lang('ACCEDER')
                            </a>
                            <a href="#" @click.prevent="showCancelRequestModal({{$validation->id}})" class="btn btn-danger">
                                @lang('Rechazar')
                            </a>
                        @else
                            {{-- Documentos que se aportaron --}}
                            @include('workspace.home.contributed-files',
                                    [
                                        'request'   =>  $validation->signer->request()
                                    ]
                                )
                            {{-- /Documentos que se aportaron --}}
                        @endif
                    @else
                        <div class="text-warning bold small mb-4 ml-5 text-justify">
                            @lang('Hemos buscado por todos lados, pero no hemos encontrado una solicitud de documentos relacionada con esta validación').
                        </div>
                    @endif
                    {{-- /En el caso que se haya creado la validación y no se haya asignado la solicitud al signer --}}
                @break
                {{-- /Cuando es una validación de solicitud de documentos --}}
                
            @endswitch
            
        </div>

        {{--  comentario de algunos de los procesos de validacion  --}}
        @if($validation->feedback)
            <div class="mt-4">
                <div class="alert alert-primary text-justify" role="alert">
                    <div class="alert-heading mb-2 border-bottom border-primary font-weight-bold">
                        <i class="fas fa-comment"></i> @lang('Comentario')
                    </div>
                    <p>
                        <small class="font-italic">"{{ $validation->feedback->comment }}"</small>
                    </p>
                </div>
            </div>
        @endif
    </li>
@endforeach
{{-- /Listado de las validaciones del firmante --}}
