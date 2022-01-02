{{--
    Visualiza una tabla que contiene una lista de archivos subidos

    @param File[] $files                        Una lista de archivos a mostrar
    @param bool   $selection                    true si la primera columna de la tabla es para selección
                                                false para un valor autonumérico
--}}

<div class="col-md-12 table-responsive">
    <table class="mb-0 table table-striped">
        <thead>
        @include('dashboard.files.header-files-table')
        </thead>
        <tbody>
        <input type="hidden" name="selected" v-model="files"/>
        @forelse($files as $file)
            <tr>
                @if ($selection)
                    <td class="checkbox-centered" data-label="@lang('Selección')">
                        <label class="check-container">
                            <input @change.prevent="select" name="files[]" value="{{$file->id}}" v-model="files"
                                   class="form-control file" type="checkbox"/>
                            <span class="square-check"></span>
                        </label>
                    </td>
                @else
                    <th scope="row">
                        {{$loop->iteration}}
                    </th>
                @endif

                <td class="align-middle" data-label="@lang( $file->is_folder ? 'Carpeta' : 'Archivo')">
                    @if( $file->is_folder )
                        <a href="@route('dashboard.file.list', ['id' => $file->id, 'count' => request()->count])">
                            {{ $file->name }}
                        </a>
                        <div class="btn btn-action-details ml-2" data-html="true" data-toggle="tooltip"
                             data-placement="bottom" data-original-title="{{ $file->extra_data }}"
                             data-delay='{"show": 0, "hide": 1000}'>
                            <i class="fa fa-eye"></i>
                        </div>
                    @else
                        <a href="@route('file.download', ['id' => $file->id])">{{$file->name}}</a>
                    @endif
                </td>

                <td class="align-middle" data-label="@lang('Tipo')">
                    @include('dashboard.partials.file-icon', ['type' => $file->type])
                </td>

                <td class="text-center align-middle" data-label="@lang('Tamaño')">
                    @filesize($file->size)
                </td>

                <td class="align-middle text-nowrap" data-label="@lang('Creado')">
                    @date($file->created_at)
                    <div class="text-info">@time($file->created_at)</div>
                </td>
                <td class="align-middle text-nowrap" data-label="@lang('Última Actualización')">
                    @date( $file->updated_at )
                    <div class="text-info">@time( $file->updated_at )</div>
                </td>

                <td class="text-center adjust-margin">
                    <div class="btn-group-vertical" role="group" aria-label="">
                        <div class="btn-group" role="group" aria-label="">
                            <a href="@route('dashboard.files.edit', ['id' => $file->id])"
                               class="btn btn-action-edit"
                               :class="files.length > 1 ? 'disabled': ''"
                               data-toggle="tooltip" data-placement="top"
                               data-original-title="@lang('Editar')">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="@route('dashboard.files.move', ['id' => $file->id])"
                               class="btn btn-action-move" :class="files.length > 1 ? 'disabled': ''"
                               data-toggle="tooltip" data-placement="top"
                               data-original-title="@lang('Mover a carpeta')" @click="clearFiles">
                                <img src="@asset('assets/images/dashboard/images/move-file.png')" height="16" alt="">
                            </a>
                            {{-- Descarga del archivo --}}
                            <a href="@route('file.download', ['id' => $file->id])"
                               class="btn btn-action-download" :class="files.length > 1 ? 'disabled': ''"
                               data-toggle="tooltip" data-placement="top"
                               data-original-title="@lang($file->is_folder ? 'Descargar la carpeta' : 'Descargar el archivo')">
                                <i class="fas fa-file-download"></i>
                            </a>
                            {{-- Descarga del archivo --}}
                            {{-- Envía el archivo a firmar --}}
                            @if( !$file->is_folder )
                                <a href="@route('dashboard.file.sign', ['id' => $file->id])"
                                   class="btn btn-action-sign"
                                   :class="(diskSpace.free == 0 || files.length > 1) ? 'disabled': ''"
                                   data-toggle="tooltip" data-placement="top"
                                   data-original-title="@lang('Generar documento para firmar')">
                                    <i class="fas fa-signature"></i>
                                </a>
                            @endif
                            {{-- /Envía el archivo a firma --}}
                        </div>
                        <div class="btn-group mt-1" role="group" aria-label="">

                            {{--  Enviar la URL desde la que se hará la descarga y el ID del archivo seleccionado para registrar que fue compartido--}}
                            <button @click.prevent="sharingExtraData({{ $file->id }})" class="btn btn-action-copy-url"
                                    :disabled="files.length > 1" data-toggle="tooltip" data-placement="bottom"
                                    data-original-title="@lang('Copiar URL')">
                                <i class="fas fa-copy"></i>
                            </button>

                            {{-- Ruta para compartir el archivo con otros usuarios --}}
                            <button @click.prevent="shareFiles" class="btn btn-action-share"
                                    :disabled="files.length > 1" data-id="{{ $file->id }}" data-toggle="tooltip"
                                    data-placement="bottom"
                                    data-original-title="@lang($file->is_folder ? 'Compartir la carpeta' : 'Compartir el archivo')">
                                <i class="fas fa-share-alt" data-id="{{ $file->id }}"></i>
                            </button>
                            {{-- Ruta para compartir el archivo con otros usuarios --}}

                            {{--  boton para compartir en redes sociales  --}}
                            @include('common.social-networks.button-social-networks', [
                                'route' => route('workspace.set.share', ['token' => 'token']),
                                'type'  => 'file',
                                'id'    => $file->id
                            ])

                            {{-- Elimina el archivo --}}
                            <button :disabled="files.length > 1" @click.prevent="confirmRemoveFile({{$file->id}})"
                                    class="btn btn-action-remove" data-toggle="tooltip" data-placement="bottom"
                                    data-original-title="@lang($file->is_folder ? 'Eliminar la carpeta y todo su contenido' : 'Eliminar el archivo')">
                                <i class="fa fa-trash"></i>
                            </button>
                            {{-- /Elimina el archivo --}}

                            <a href="@route('dashboard.files.history', ['id' => $file->id])"
                               :class="files.length > 1 ? 'disabled': ''" class="btn btn-action-details"
                               data-toggle="tooltip" data-placement="bottom"
                               data-original-title="@lang('Historial de acciones')">
                                <i class="fa fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-danger bold">@lang('Ningún archivo registrado')</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        @include('dashboard.files.header-files-table')
        </tfoot>
    </table>
</div>