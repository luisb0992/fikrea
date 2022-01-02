<div class="modal inmodal modal-legal" id="modal-legal-warning" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            
            <div class="modal-header">
                <h4 class="modal-title">@lang('Aviso Legal')</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">@lang('Cerrar')</span>
                </button>
            </div>
            
            <div class="modal-body scrollable">
                <p>
                    @include('landing.modals.legal.docs.legal-warning-document')
                </p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">@lang('Cerrar')</button>
            </div>

        </div>
    </div>
</div>