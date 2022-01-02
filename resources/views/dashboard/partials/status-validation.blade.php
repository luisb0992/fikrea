{{--
    Visualiza el estado de una validación

    @param Type          $type                  El tipo de validación   
    @param Validations[] $validations           La lista de validaciones del tipo dado
--}}
<div class="col-md-4">
    <div class="main-card mb-3 card">
        <div class="card-body text-center">

            <div class="mt-4">
             
                {{-- El tipo de validación --}}
                @switch ($type)
                    @case(\App\Enums\ValidationType::TEXT_BOX_VERIFICATION)
                        <h5><i class="fas fa-edit"></i> @lang('Editor de documento')</h5>
                        @break
                    @case(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE)
                        <h5><i class="fas fa-signature"></i> @lang('Firma manuscrita digital')</h5>
                        @break
                    @case(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION)
                        <h5><i class="fas fa-volume-up"></i> @lang('Archivo de audio')</h5>
                        @break
                    @case(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION)
                        <h5><i class="fas fa-video"></i> @lang('Archivo de video')</h5>
                        @break
                    @case(\App\Enums\ValidationType::PASSPORT_VERIFICATION)
                        <h5><i class="fas fa-id-card"></i> @lang('Documento identificativo')</h5>
                        @break
                    @case(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION)
                        <h5><i class="fas fa-desktop"></i> @lang('Captura de pantalla')</h5>
                        @break
                    @case(\App\Enums\ValidationType::DOCUMENT_REQUEST_VERIFICATION)
                        <h5><i class="metismenu-icon pe-7s-back-2"></i> @lang('Solicitud de documento')</h5>
                        @break
                    @case(\App\Enums\ValidationType::FORM_DATA_VERIFICATION)
                        <h5><i class="fas fa-clipboard-list"></i> @lang('verificación de datos')</h5>
                        @break
                @endswitch
                {{--/ El tipo de validación --}}

                {{-- La lista de firmantes --}}
                <ul class="list-group signers">
                    @forelse ($validations as $validation)
                    <li class="list-group-item text-left">

                        <i class="far check fa-2x {{$validation->getIconStatus()}}"></i>

                        <span class="signer">
                            {{$validation->signer->name}} {{$validation->signer->lastname}}
                        
                            @if ($validation->signer->email)
                            <a href="mailto:{{$validation->signer->email}}">{{$validation->signer->email}}</a>
                            @elseif ($validation->signer->phone)
                            <a href="tel:{{$validation->signer->phone}}">{{$validation->signer->phone}}</a>
                            @endif

                        </span>

                        <div>
                            <span class="text-secondary">
                                @if ($validation->validated_at)
                                    @datetime($validation->validated_at)

                                @elseif ($validation->signer->creator)
                                <div class="text-right mt-4">
                                    @switch ($type)
                                        @case(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION)
                                            <a href="@route('dashboard.audio', ['id' => $validation->id])" class="btn btn-success">
                                                @lang('Validar Ahora')
                                            </a>
                                            @break
                                        @case(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION)
                                            <a href="@route('dashboard.video', ['id' => $validation->id])" class="btn btn-success">
                                                @lang('Validar Ahora')
                                            </a>
                                            @break
                                        @case(\App\Enums\ValidationType::PASSPORT_VERIFICATION)
                                            <a href="@route('dashboard.passport', ['id' => $validation->id])" class="btn btn-success">
                                                @lang('Validar Ahora')
                                            </a>
                                            @break
                                        @case(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION)
                                            <a href="@route('dashboard.screen', ['id' => $validation->id])" class="btn btn-success">
                                                @lang('Validar Ahora')
                                            </a>
                                            @break
                                        @case(\App\Enums\ValidationType::DOCUMENT_REQUEST_VERIFICATION)
                                            <a href="@route('dashboard.request', ['id' => $validation->id])" class="btn btn-success">
                                                @lang('Validar Ahora')
                                            </a>
                                            @break
                                    @endswitch
                                </div>
                                @endif
                            </span>
                       </div>
                    </li>
                    @empty
                    <li class="list-group-item text-left">
                        <span class="text-danger bold">
                            @lang('No se ha configurado ninguna firma para el documento')
                        </span>
                    </li>
                    @endforelse
                </ul>
                {{--/La lista de firmantes --}}
            </div>
        
        </div>
    </div>
</div>