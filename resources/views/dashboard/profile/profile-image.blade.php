{{-- Imágen del Perfil --}}
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">@lang('Imagen del Perfil')</h5>
        <form id="imageForm" method="post" action="@route('dashboard.profile.image')" 
            data-message-success="@lang('Se ha guardado la nueva imagen de su perfil')"
            data-message-failed="@lang('La imagen selecciona no es válida. Compruebe que es una imagen y su tamaño')">

            <div class="form-row">

                <div class="col-md-6">
                    <div class="position-relative form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span @click="openFileBrowser()" class="input-group-text">@lang('Subir')</span>
                            </div>
                            <div class="custom-file">
                                <input @change="uploadImage" type="file" class="custom-file-input" />
                                <label id="browser" class="custom-file-label" for="browser">
                                    @{{file || '@lang('Seleccionar archivo')'}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 text-center">
                    <div class="position-relative form-group">
                        @if ($user->image)
                        <img @click="openFileBrowser()" id="profileImage" class="profile-image" :src="profileImage" data-src="data:image/*;base64,{{$user->image}}" alt="" />
                        @else
                        {{-- Si el usuario no tiene una imagen del perfil, se mostrará la imagen por defecto --}}
                        <img @click="openFileBrowser()" id="profileImage" class="profile-image" :src="profileImage" data-src="@asset('assets/images/dashboard/avatars/empty-user.png')" alt="" />
                        @endif
                    </div>
                </div>
            
            </div>

            <div class="form-row">
                <div class="col-md-12 text-info">
                    @lang('Utilice una imagen png, jpg o gif (tamaño máximo 2 Mb)')
                </div>
            </div>

        </form>

    </div>
</div>
{{--/ Imágen del Perfil --}}