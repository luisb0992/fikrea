<div class="modal inmodal modal-languages" id="modal-language-selector" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated flipInY">
            
            <div class="modal-header">
                <h5 class="modal-title bold">@lang('Idioma de la Página')</h5>
                <span class="sr-only">@lang('Cerrar')</span></button>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body scrollable">
                {{language()->flags()}}
            </div>

        </div>
    </div>
</div>