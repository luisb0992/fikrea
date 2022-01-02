{{-- Firmas que debe firmar el creador --}}
<div class="row col-md-12 mb-2 mt-1">
    <ul class="list-group" v-for="sign in creatorSigns" :key="sign.code">

        <li class="list-group-item" 
            data-toggle="tooltip" data-placement="bottom" title="@lang('Abrir esta página')">
            
            <span class="fa-2x-text bold">
                <a href="#" @click.prevent="goPage(sign.page)">
                    @lang('Firma') [ @{{sign.code}} ]
                </a>
            </span>

            <span v-if="sign.sign">
                <i class="fas fa-2x fa-check text-success"></i>
            </span>
            <span v-else>
                <i class="fa fa-times fa-2x text-danger"></i>
            </span>
        </li>

    </ul>
</div>
{{--/Firmas que debe firmar el creador --}}