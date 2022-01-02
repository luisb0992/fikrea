{{-- Cajas que debe completar el creador --}}
<div class="row col-md-12 mb-2 mt-1">
    <ul id="creatorBoxs" class="list-group" v-for="box in creatorBoxs" :key="box.code">

        <li class="list-group-item" :data-box-id="box.code"
            data-toggle="tooltip" data-placement="bottom" title="@lang('Abrir esta página')">
            
            <span class="fa-2x-text bold">
                <a href="#" @click.prevent="goPage(box.page)">
                    @lang('Caja') [ @{{box.code}} ]
                </a>
            </span>

            {{--
                Cuando es un checkbox si no está marcaddo es una opción válida también
                && box.text != 'false'
            --}}
            <span v-if="box.text && box.text != -1 || box.text == 'false'">
                <i class="fas fa-2x fa-check text-success"></i>
            </span>
            <span v-else>
                <i class="fa fa-times fa-2x text-danger"></i>
            </span>
        </li>

    </ul>
</div>
{{-- Cajas que debe completar el creador --}}