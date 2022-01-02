{{-- ------------------------------
    --> Firma manuscrita
    ---------------------------- --}}
<td class="text-center">
    <div class="form-check form-check-inline my-2">
        {{-- el check --}}
        @if ($document->canBeSigned())
            <div class="mt-0 mt-md-5 d-flex align-items-center">
                <div class="d-block d-sm-block d-md-none mr-3">
                    <img src="@asset('assets/images/dashboard/images/validations/firma-digital.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="signature">
                </div>
                <div>
                    {{-- Inicialmente, cuando todavía no se han establecido validaciones,
                esta opción aparece marcada para todos los firmantes --}}
                    @if ($signer->mustValidate($document, $validations['handWrittenSignature']) || $document->validations->count() == 0)
                        <input checked class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            @click="checkForCaptureValidation"
                            data-validation-id="{{ $validations['handWrittenSignature'] }}"
                            id="FM-{{ $signer->id }}" />
                    @else

                        <input class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            @click="checkForCaptureValidation"
                            data-validation-id="{{ $validations['handWrittenSignature'] }}"
                            id="FM-{{ $signer->id }}" />
                    @endif
                </div>
                <div class="d-block d-sm-block d-md-none ml-3">
                    <label class="form-check-label text-capitalize text-info" for="FM-{{ $signer->id }}">
                        @lang('Firma manuscrita')
                    </label>
                </div>
            </div>
        @else
            <div class="text-danger mt-0 mt-md-5 ml-5 ml-md-0">
                @lang('Este archivo no puede ser firmado')
            </div>
        @endif
    </div>
</td>