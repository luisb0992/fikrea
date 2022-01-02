{{-- ------------------------------
    --> Archivo de video
    ---------------------------- --}}
<td class="text-center mr-5 mr-md-0">
    <div class="form-check form-check-inline my-2 mr-5 mr-md-0 pr-2 pr-md-0">
        <div class="mt-0 mt-md-5 d-flex align-items-center mr-5 mr-md-0">
            <div class="d-block d-sm-block d-md-none mr-3">
                <img src="@asset('assets/images/dashboard/images/validations/video.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="video">
            </div>
            <div>
                @if ($signer->mustValidate($document, $validations['video']))
                    <input checked class="check-2-5 validation" type="checkbox"
                        data-user-id="{{ $signer->id }}"
                        data-validation-id="{{ $validations['video'] }}" id="AV-{{ $signer->id }}" />
                @else
                    <input class="check-2-5 validation" type="checkbox"
                        data-user-id="{{ $signer->id }}"
                        data-validation-id="{{ $validations['video'] }}" id="AV-{{ $signer->id }}" />
                @endif
            </div>
            <div class="d-block d-sm-block d-md-none ml-3">
                <label class="form-check-label text-capitalize text-info" for="AV-{{ $signer->id }}">
                    @lang('Archivo de video')
                </label>
            </div>
        </div>
    </div>
</td>