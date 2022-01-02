{{-- ------------------------------
    --> Captura de pantalla
    ---------------------------- --}}
<td class="text-center">
    <div class="form-check form-check-inline my-2">
        @if ($signer->creator)
            <div class="mt-0 mt-md-5 d-flex align-items-center">
                <div class="d-block d-sm-block d-md-none mr-3">
                    <img src="@asset('assets/images/dashboard/images/validations/captura-pantalla.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="screen-capture">
                </div>
                <div class="text-danger text-center">
                    <span data-toggle="tooltip" data-placement="top" title="{{ $notAvailableMsj }}">
                        {!! $notAvailableIcon !!}
                    </span>
                </div>
                <div class="d-block d-sm-block d-md-none ml-1">
                    <label class="form-check-label text-capitalize text-info" for="CP-{{ $signer->id }}">
                        @lang('Captura de pantalla')
                    </label>
                </div>
            </div>
        @else
            <div class="mt-0 mt-md-5 d-flex align-items-center">
                <div class="d-block d-sm-block d-md-none mr-3">
                    <img src="@asset('assets/images/dashboard/images/validations/captura-pantalla.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="screen-capture">
                </div>
                <div>
                    @if ($signer->mustValidate($document, $validations['screenCapture']))
                        <input checked class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            data-validation-id="{{ $validations['screenCapture'] }}"
                            id="CP-{{ $signer->id }}" />
                    @else
                        <input @click="checkCaptureValidations"
                            data-message="@lang('Solo está permitida esta validación cuando se ha selecciondo una validación de Edición de documento o Firma manuscrita.')"
                            class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            data-validation-id="{{ $validations['screenCapture'] }}"
                            id="CP-{{ $signer->id }}" />
                    @endif
                </div>
                <div class="d-block d-sm-block d-md-none ml-3">
                    <label class="form-check-label text-capitalize text-info" for="CP-{{ $signer->id }}">
                        @lang('Captura de pantalla')
                    </label>
                </div>
            </div>
        @endif
    </div>
</td>