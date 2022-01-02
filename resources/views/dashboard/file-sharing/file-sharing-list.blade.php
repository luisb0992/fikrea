@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Se muestra la lista con las comparticiones de archivos realizadas')
    <div class="page-title-subheading">
        @lang('Cada conjunto de archivos posee un enlace de descarga que se envío a los destinatarios')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12">
    <h5 class="card-title">
        @lang('Archivos Compartidos')
    </h5>
    
    {{-- Mensajes de la aplicación --}}
    <div id="message"
        data-share-title="@config('app.name')"
        data-share-text="@lang('Se ha copiado la dirección de descarga del archivo')">
    </div>
    {{--/Mensajes de la aplicación --}}

    <div class="main-card mb-3 card">
        <div class="card-body">
            
            <div class="table-responsive">    
                <table class="mb-0 table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('Archivos')</th>
                            <th class="text-center">@lang('Número de Archivos')</th>
                            <th class="text-center">@lang('Tamaño') (kB)</th>
                            <th>@lang('Destinatarios')</th>
                            <th>@lang('Creado')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fileSharings as $fileSharing)
                        <tr>
                            <td data-label="#">
                                {{$loop->iteration}}
                            </td>
                            
                            <td data-label="@lang('Archivos')">
                                <ul class="list-unstyled">
                                    @foreach($fileSharing->files as $file)
                                        <li>
                                            @if ($file)
                                                <span>{{$file->name}}</span>
                                                <span>
                                                    <em class="text-secondary">@filesize($file->size)</em> 
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="text-center" data-label="@lang('Número de Archivos')">
                                {{$fileSharing->numFiles}}
                            </td>

                            <td class="text-center" data-label="@lang('Tamaño') (kB)">
                                {{round($fileSharing->size/1024)}}
                            </td>
                
                            <td data-label="@lang('Destinatarios')">
                                <ul class="list-unstyled">
                                @foreach($fileSharing->contacts as $contact)
                                    <li>
                                        @if ($contact->email)
                                            <a href="mailto:{{$contact->email}}">{{$contact->email}}</a>
                                        @else
                                            <a href="tel:{{$contact->phone}}">{{$contact->phone}}</a>
                                        @endif
                                    </li>
                                @endforeach
                                </ul>
                            </td>
                      
                            <td class="align-middle" data-label="@lang('Creado')">
                                @datetime($fileSharing->created_at)
                            </td>
                            
                            <td class="text-center">        
                                {{-- Descarga del archivo 
                                     @see routes/web.php  
                               --}}
                               <a href="@route('file.set.download', ['id' => $fileSharing->id])" class="btn btn-info square"
                                    data-toggle="tooltip" data-placement="top" 
                                    data-original-title="@lang('Descargar')"
                                >
                                   <i class="fas fa-file-download"></i>
                               </a>
                               
                               {{-- Ruta para compartir el archivo con otros usuarios 
                                    @see routes/web.php    
                               --}}
                               <a href="#"
                                    @click.prevent="share('@route('workspace.set.share', ['token' => $fileSharing->token])')"
                                    class="btn btn-success square"
                                    data-toggle="tooltip" data-placement="top" 
                                    data-original-title="@lang('Compartir')">
                                   <i class="fas fa-share-alt"></i>
                               </a>

                               {{-- Ver histórico de visitas y descargas de la compartición --}}
                               @if ($fileSharing->histories->isEmpty())
                                    <a href="#!" class="btn btn-warning square"
                                            data-toggle="tooltip" data-placement="top" 
                                            data-original-title="@lang('Ninguna visita registrada')">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                @else
                                    <a href="@route('dashboard.files.sharing-history', [$fileSharing->id])"  class="btn btn-warning square"
                                            data-toggle="tooltip" data-placement="top" 
                                            data-original-title="@lang('Histórico de descargas')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                                
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">@lang('Ningún elemento registrado')</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>@lang('Archivos')</th>
                            <th class="text-center">@lang('Número de Archivos')</th>
                            <th class="text-center">@lang('Tamaño') (kB)</th>
                            <th>@lang('Destinatarios')</th>
                            <th>@lang('Creado')</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{--Paginador --}}
            <div class="paginator-wrapper mt-1">
                {{$fileSharings->links()}}

                @lang('Se muestran :files de un total de :total archivos.', [
                    'files' => $fileSharings->count(),
                    'total' => $fileSharings->total(),
                ])
                
            </div>
            {{--/Paginador --}}

        </div>
    </div>
</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/dashboard/files/file-share-list.js')"></script>
@stop