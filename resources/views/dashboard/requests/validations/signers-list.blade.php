{{-- Listado de firmantes a seleccionar para requerir documentos --}}
<div class="col-sm-12 col-md-3">
    
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">@lang('Listado de firmantes')</h5>

            <div>    
                <table>
                    <tbody>
                        @foreach($signers as $signer)
                        <tr>
                            <td>
                                <div>
                                    <label class="check-container mr-2 mt-2">
                                        <input class="form-control validation"
                                            type="checkbox"
                                            value="{{$signer->id}}" 
                                            v-model="checkedSigners" />
                                        <span class="square-check pull-left"></span>
                                    </label>
                                </div>
                            </td>

                            <td>
                                <div>
                                    <span class="text bold mb-0">{{ $signer->name }} {{ $signer->lastname }} </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Mostrar cuando no se ha seleccionado firmante --}}
        <div class="card-footer" v-if="!showTextSignerAlert">
            <span class="text-danger bold">
                @lang('Debe seleccionar al menos un firmante')
            </span>
        </div>
        {{-- /Mostrar cuando no se ha seleccionado firmante --}}
    </div>  

</div>
{{-- Listado de firmantes a seleccionar para requerir documentos --}}