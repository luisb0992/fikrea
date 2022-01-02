{{-- ------------------------------
    --> Documento identificativo
    ---------------------------- --}}
<td class="text-center">
    <div class="form-check form-check-inline my-2">
        <div class="mt-0 mt-md-5 d-flex align-items-center">
            <div class="d-block d-sm-block d-md-none mr-3">
                <img src="@asset('assets/images/dashboard/images/validations/reconocimiento-facial.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="facial-recognition">
            </div>
            <div>
                @if ($signer->mustValidate($document, $validations['passport']))
                    <input checked class="check-2-5 validation" type="checkbox"
                        data-user-id="{{ $signer->id }}"
                        data-validation-id="{{ $validations['passport'] }}" id="DI-{{ $signer->id }}" />
                @else
                    <input class="check-2-5 validation" type="checkbox"
                        data-user-id="{{ $signer->id }}"
                        data-validation-id="{{ $validations['passport'] }}" id="DI-{{ $signer->id }}" />
                @endif
            </div>
            <div class="d-block d-sm-block d-md-none ml-3">
                <label class="form-check-label text-capitalize text-info" for="DI-{{ $signer->id }}">
                    @lang('Documento identificativo')
                </label>
            </div>
        </div>
    </div>
</td>