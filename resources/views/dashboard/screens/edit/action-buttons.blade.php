{{-- Los botones de Acción --}}
<div class="col-md-12 mb-4">

    <div class="btn-group" role="group">
        
        <a href="#!" :class="!captures.length ? 'disabled':''" 
            @click.prevent="save" class="btn btn-lg btn-success mr-1"
            data-save-request="@route('dashboard.screen.saveall')"
            data-redirect-request="@route('dashboard.screen.list')"
        >
            @lang('Finalizar')
        </a>

        <a href="@route('dashboard.home')" class="btn btn-lg btn-danger">
            @lang('Cancelar')
        </a>
       
    </div>

</div>
{{--/Los botones de Acción --}}