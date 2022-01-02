{{-- ------------------------------
    --> Archivo de audio
    ---------------------------- --}}
<td class="text-center">
    <div class="form-check form-check-inline my-2">
        <div class="mt-0 mt-md-5 d-flex align-items-center">
            <div class="d-block d-sm-block d-md-none mr-3">
                <img src="@asset('assets/images/dashboard/images/validations/audio.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="audio">
            </div>
            <div>
                @if ($signer->mustValidate($document, $validations['audio']))
                    <input checked class="check-2-5 validation" type="checkbox"
                        data-user-id="{{ $signer->id }}"
                        data-validation-id="{{ $validations['audio'] }}" id="AA-{{ $signer->id }}" />
                @else
                    <input class="check-2-5 validation" type="checkbox"
                        data-user-id="{{ $signer->id }}"
                        data-validation-id="{{ $validations['audio'] }}" id="AA-{{ $signer->id }}" />
                @endif
            </div>
            <div class="d-block d-sm-block d-md-none ml-3">
                <label class="form-check-label text-capitalize text-info" for="AA-{{ $signer->id }}">
                    @lang('Archivo de audio')
                </label>
            </div>
        </div>
    </div>
</td>