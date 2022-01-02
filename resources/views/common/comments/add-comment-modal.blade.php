{{-- Modal para agregar un comentario a un proceso de validacion de un documento
    o un proceso independiente fuera del documento
    => (Verificacion de datos, solicitud de documentos) --}}

<template>
    <b-modal id="add-comment-modal" header-bg-variant="primary" hide-footer hide-backdrop content-class="shadow" body-bg-variant="white">

            {{-- El encabezado de la modal --}}
            <template #modal-header>

                {{-- Titulo del modal --}}
                <h5 class="modal-title text-white">

                    {{-- Es un proceso de validacion dentro de un documento --}}
                    @isset($validationType)
                        @if (!$signer->getIfCommentExists($validationType))
                            <i class="fas fa-comment"></i> @lang('Agregar un comentario al proceso')
                            <p class="text-white font-weight-light"><small>@lang('Desde aquí puede agregar un comentario al
                                    solicitante')</small></p>
                        @else
                            <i class="fas fa-comment"></i> @lang('Comentario del proceso')
                        @endif

                        {{-- Es un proceso independiente fuera de un documento
                    => (Certificacion de datos, Documentos requeridos) --}}
                    @else
                        @if (!$process->getIfCommentExists())
                            <i class="fas fa-comment"></i> @lang('Agregar un comentario al proceso')
                            <p class="text-white font-weight-light"><small>@lang('Desde aquí puede agregar un comentario al
                                    solicitante')</small></p>
                        @else
                            <i class="fas fa-comment"></i> @lang('Comentario del proceso')
                        @endif
                    @endisset
                </h5>

                {{-- cerrar el modal --}}
                <b-button size="sm" variant="link" @click="$bvModal.hide('add-comment-modal')">
                    <i class="fas fa-times text-white"></i>
                </b-button>
            </template>

            <form @submit.prevent="saveValidationComment">
                {{-- El contenido de la modal --}}
                <div class="form-group mb-2">

                    {{-- Es un proceso de validacion dentro de un documento --}}
                    @isset($validationType)
                        @if (!$signer->getIfCommentExists($validationType))
                            <label>@lang('Comentario')</label>
                            <textarea class="form-control" name="comment" rows="5" placeholder="@lang('Agregar un comentario')"></textarea>
                        @else
                            <label>@lang('Su comentario')</label>
                            <div class="alert alert-primary" role="alert">
                                "{{ $signer->getIfCommentExists($validationType) }}"
                            </div>
                        @endif

                        {{-- Es un proceso independiente fuera de un documento
                    => (Certificacion de datos, Documentos requeridos) --}}
                    @else
                        @if (!$process->getIfCommentExists())
                            <label>@lang('Comentario')</label>
                            <textarea class="form-control"name="comment" rows="5" placeholder="@lang('Agregar un comentario')"></textarea>
                        @else
                            <label>@lang('Su comentario')</label>
                            <div class="alert alert-primary" role="alert">
                                "{{ $process->getIfCommentExists() }}"
                            </div>
                        @endif
                    @endisset
                </div>
                {{-- /Datos a mostrar en modal --}}

                <div class="form-group my-2 text-right">
                    <hr>
                    {{-- cerrar el modal --}}
                    <button type="button" class="btn btn-secondary"
                        @click="$bvModal.hide('add-comment-modal')" id="closeCommentModal">@lang('Volver')</button>
                    {{-- /cerrar el modal --}}

                    {{-- Es un proceso de validacion dentro de un documento --}}
                    @isset($validationType)

                        {{-- Si no existe el comentario se muestra el boton y formulario --}}
                        @if (!$signer->getIfCommentExists($validationType))
                            @isset($token)
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="validationtype" value="{{ $validationType }}">
                                <button type="submit" class="btn btn-outline-primary" id="disabledCommentButtonModal"
                                    data-save="@route('workspace.comment.save')">
                                    @lang('Agregar Comentario')
                                </button>
                            @else
                                <input type="hidden" name="id" value="{{ $signer->id }}">
                                <input type="hidden" name="validationtype" value="{{ $validationType }}">
                                <button type="submit" class="btn btn-outline-primary" id="disabledCommentButtonModal"
                                    data-save="@route('dashboard.comment.save')">
                                    @lang('Agregar Comentario')
                                </button>
                            @endisset
                        @endif

                        {{-- Es un proceso independiente fuera de un documento
                    => (Certificacion de datos, Documentos requeridos) --}}
                    @else

                        {{-- Si no existe el comentario se muestra el boton y formulario --}}
                        @if (!$process->getIfCommentExists())
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="id" value="{{ $process->id }}">
                            <input type="hidden" name="process" value="{{ $nameProcess }}">
                            <button type="submit" class="btn btn-outline-primary" id="disabledCommentButtonModal"
                                data-save="@route('workspace.comment.save')">
                                @lang('Agregar Comentario')
                            </button>
                        @endif
                    @endisset
                </div>
            </form>
        </b-modal>
</template>
