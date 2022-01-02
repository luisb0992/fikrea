<div class="modal inmodal modal-logout" id="modal-logout" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInUp">
            
            <div class="modal-header">
                <h4 class="modal-title">@lang('Cerrar Sesión')</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">@lang('Cerrar')</span>
                </button>
            </div>
            
            <div class="modal-body">
                <p class="text-center bold">
                    @lang('La sesión de usuario terminará ahora')
                </p>
                <p class="text-center">
                    @lang('¿Realmente desea continuar?')
                </p>
            </div>
            
            <div class="modal-footer">
                <a href="@route('dashboard.logout')" class="btn btn-success">@lang('Sí')</a>
                <a href="#" class="btn btn-danger" data-dismiss="modal">@lang('No')</a>
            </div>

        </div>
    </div>
</div>