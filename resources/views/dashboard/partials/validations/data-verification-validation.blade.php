{{-- ------------------------------
    --> Verificacion de datos
    ---------------------------- --}}
<td class="text-center">
    <div class="form-check form-check-inline my-2">
        @if (!$signer->creator)
            <div class="mt-0 mt-md-5 d-flex align-items-center">
                <div class="d-block d-sm-block d-md-none mr-3">
                    <img src="@asset('assets/images/dashboard/images/validations/verificacion-datos.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="data-verification">
                </div>
                <div>
                    @if ($signer->mustValidate($document, $validations['dataForm']))
                        <input checked class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            data-validation-id="{{ $validations['dataForm'] }}"
                            id="VD-{{ $signer->id }}" />
                    @else
                        <input class="check-2-5 validation" type="checkbox"
                            data-user-id="{{ $signer->id }}"
                            data-validation-id="{{ $validations['dataForm'] }}"
                            id="VD-{{ $signer->id }}" />
                    @endif
                </div>
                <div class="d-block d-sm-block d-md-none ml-3">
                    <label class="form-check-label text-capitalize text-info" for="VD-{{ $signer->id }}">
                        @lang('Verificacion de datos')
                    </label>
                </div>
            </div>
        @else
            <div class="text-danger mt-0 mt-md-5 d-flex align-items-center">
                <div class="d-block d-sm-block d-md-none mr-3">
                    <img src="@asset('assets/images/dashboard/images/validations/verificacion-datos.webp')" class="img-fluid rounded-circle icon-size-table-xs" alt="data-verification">
                </div>
                <div>
                    <span data-toggle="tooltip" data-placement="top" title="{{ $notAvailableMsj }}">
                        {!! $notAvailableIcon !!}
                    </span>
                </div>
                <div class="d-block d-sm-block d-md-none ml-1">
                    <label class="form-check-label text-capitalize text-info" for="VD-{{ $signer->id }}">
                        @lang('Verificacion de datos')
                    </label>
                </div>
            </div>
        @endif
    </div>
</td>