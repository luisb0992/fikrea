{{-- ------------------------ 
     - Editor de documento --
     ------------------------ --}}
<td class="text-center">
    <div class="form-check form-check-inline my-2">
        {{-- el check --}}
        @if ($document->canBeSigned())
            <div class="mt-0 mt-md-5 d-flex align-items-center">
                <div class="d-block d-sm-block d-md-none mr-3">
                    <img src="@asset('assets/images/dashboard/images/validations/text-box.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="text-box">
                </div>
                <div>
                    @if ($signer->mustValidate($document, \App\Enums\ValidationType::TEXT_BOX_VERIFICATION))
                        <input checked class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            @click="checkForCaptureValidation"
                            data-validation-id="{{\App\Enums\ValidationType::TEXT_BOX_VERIFICATION}}"
                            id="ED-{{ $signer->id }}" 
                        />
                    @else
                        <input class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            @click="checkForCaptureValidation"
                            data-validation-id="{{\App\Enums\ValidationType::TEXT_BOX_VERIFICATION}}"
                            id="ED-{{ $signer->id }}" 
                        />
                    @endif
                </div>

                <div class="d-block d-sm-block d-md-none ml-3">
                    <label class="form-check-label text-capitalize text-info" for="ED-{{ $signer->id }}">
                        @lang('Editor de documento')
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