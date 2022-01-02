{{-- Opciones de caja de texto seleccionada --}}
<b-card v-if="box.id && selectedBox"
    header="@lang('Detalles de caja de texto seleccionada.')"
    header-text-variant="white"
    header-tag="header"
    header-bg-variant="dark"
    {{--:footer="box.signer.name"--}}
    footer-tag="footer"
    footer-bg-variant="success"
    footer-text-variant="white"
    footer-border-variant="dark"
    title=""
    style="max-width: 20rem;"
    >

    <template #header>
        <i class="fas fa-2x fa-list-alt"></i>
        <span class="ml-1">@lang('Detalles de caja de texto seleccionada')</span>
    </template>

    <b-form v-on:submit.prevent>

        {{-- Check para mostrar caja con id y firmante--}}
        <b-form-checkbox @change="showHideDetailsBox(box)" v-model="box.moreDetails" size="lg">
            @lang('Vista ampliada')
        </b-form-checkbox>
        {{-- /Check para mostrar caja con id y firmante--}}

        {{-- Configuración de la caja que debe cumplimentar el externo --}}
        <div v-if="!box.signer.creator">

            <b-form-group label="@lang('Título')">

                <b-form-input
                    v-model="box.title"
                    type="text"
                    required
                ></b-form-input>

            </b-form-group>
            
        </div>
        {{-- /Configuración de la caja que debe cumplimentar el externo --}}

        {{-- Código --}}
        <b-form-group label="@lang('Código')">
            <b-form-input
                v-model="box.code"
                type="text"
                disabled
            ></b-form-input>
        </b-form-group>
        {{-- / Código --}}

        {{-- Texto --}}
        <b-form-group v-show="box.signer.creator && [1,2,3,4].indexOf(box.type) != -1" label="@lang('Texto')">
            <b-form-input
                v-model="box.text"
                type="text"
                disabled
            ></b-form-input>
            <div v-if="box.rules.maxLength > box.text.length">
                <span
                    class="small d-block text-right"
                    :class="box.rules.maxLength - box.text.length < 5 ? 'text-warning':'text-secondary'"
                >
                    @{{box.rules.maxLength - box.text.length}} @lang('caracteres restantes')
                </span>
            </div>
            <div v-else>
                <span class="small d-block text-right text-success">
                    @lang('Longitud de texto completada')
                </span>
            </div>
        </b-form-group>
        {{-- / Texto --}}

        {{-- Oculto --}}
        {{-- Pos X y Pos Y --}}
        <div class="row d-none">
            <div class="col-md-6">
                {{-- Pos X --}}
                <b-form-group label="@lang('Pos X')">
                    <b-form-input
                        v-model="box.x"
                        type="text"
                        disabled
                    ></b-form-input>
                </b-form-group>
                {{-- / Pos X --}}
            </div>
            <div class="col-md-6">
                {{-- Pos Y --}}
                <b-form-group label="@lang('Pos Y')">
                    <b-form-input
                        v-model="box.y"
                        type="text"
                        disabled
                    ></b-form-input>
                </b-form-group>
                {{-- / Pos Y --}}
            </div>
        </div>
        {{-- / Pos X y Pos Y --}}

        {{-- Oculto --}}
        {{-- Ancho y Alto --}}
        <div class="row d-none">
            <div class="col-md-6">
                {{-- Ancho --}}
                <b-form-group label="@lang('Ancho')">
                    <b-form-input
                        v-model="box.width"
                        type="text"
                        disabled
                    ></b-form-input>
                </b-form-group>
                {{-- / Ancho --}}
            </div>
            <div class="col-md-6">
                {{-- Pos Y --}}
                <b-form-group label="@lang('Alto')">
                    <b-form-input
                        v-model="box.height"
                        type="text"
                        disabled
                    ></b-form-input>
                </b-form-group>
                {{-- / Pos Y --}}
            </div>
        </div>
        {{-- / Ancho y Alto --}}

        {{-- Reglas de restricciones sobre la caja de texto --}}
        <b-form-group v-if="[1,2,3,4].indexOf(box.type) != -1">

            <span class="text-primary h4">@lang('Restricciones')</span>

            <b-form-checkbox :disabled="onlyMe(1)" v-model="box.rules.numbers">@lang('Números') (1,2,3...)</b-form-checkbox>
            <b-form-checkbox :disabled="onlyMe(2)" v-model="box.rules.letters">@lang('Letras')  (A,a,b...)</b-form-checkbox>
            <b-form-checkbox :disabled="onlyMe(3)" v-model="box.rules.specials">@lang('Especiales') (@,$,%...)</b-form-checkbox>
            
            <b-form-group label="@lang('Longitud máxima')" class="d-none">

                <b-form-input
                    v-model="box.rules.maxLength"
                    type="text"
                    {{-- Para prevenir que se envíe el formulario @keypress.enter.prevent --}}
                    @keypress.enter.prevent="changeMaxLength(box)"
                ></b-form-input>
                <span class="small text-secondary">@lang('Presione Enter para ajustar la caja de texto')</span>

                {{-- Check para ajustar el tamaño máximo al ancho de la caja--}}
                <b-form-checkbox class="d-none" @change="fitMaxLenght(box)" v-model="box.fitMaxLenght" size="sm">
                    @lang('Ajustar al ancho de la caja')
                </b-form-checkbox>
                {{-- /Check para ajustar el tamaño máximo al ancho de la caja--}}


            </b-form-group>

        </b-form-group>
        {{-- / Reglas de restricciones sobre la caja --}}

        {{-- Cuando no es una caja de texto --}}
        <b-form-group v-else>

            {{-- Si es un select --}}
            <div v-if="box.type == 6">
                <span class="text-primary h4">@lang('Opciones')</span>
                <ul>
                    <li v-if="option != ''"
                        v-for="(option, index) in box.options.split(';')"
                        :key="option"
                    >
                        @{{ option }}
                        <button @click.prevent="removeOptionToBox(box, index)" class="btn btn-small btn-danger rounded-circle">
                            <i class="fa fa-trash"></i>
                        </button>
                    </li>
                </ul>

                <b-form-group>

                    <b-form-input
                        type="text"
                        v-model="newOptionText"
                        placeholder="@lang('Nueva opción')"
                        @keypress.enter.prevent {{-- Para prevenir que se envíe el formulario --}}
                    ></b-form-input>

                    {{-- Botón para agregar opción --}}
                    <button @click.prevent="addOptionToBox(box)" class="btn btn-primary btn-sm mt-1">
                        @lang('Adicionar opción')
                    </button>
                    {{-- /Botón para agregar opción --}}

                </b-form-group>

            </div>
            {{-- /Si es un select --}}


            {{-- Si es un checkbox --}}
            <div v-if="box.type == 5">
                <span class="text-primary h4">@lang('Opciones checkbox')</span>
            </div>
            {{-- /Si es un checkbox --}}

        </b-form-group>
        {{-- /Cuando no es una caja de texto --}}

        {{--Button Group para opciones con caja de texto seleccionada--}}
        <div class="btn-group" role="group" aria-label="">
            <div class="btn-group" role="group" aria-label="">
                
                {{-- Botón para eliminar la caja de texto --}}
                <button @click.prevent="deleteBox(box)" class="btn btn-danger btn-square"
                    data-toggle="tooltip" data-placement="top" data-original-title="@lang('Eliminar')"
                >
                    <i class="fa fa-trash"></i>
                </button>
                {{-- /Botón para eliminar la caja de texto --}}

                {{-- Botón para eliminar la caja de texto --}}
                <button @click.prevent="clearBox(box)" class="btn btn-warning btn-square"
                    data-toggle="tooltip" data-placement="top" data-original-title="@lang('Limpiar')"
                >
                    <i class="fas fa-broom"></i>
                </button>
                {{-- /Botón para eliminar la caja de texto --}}

                {{-- Botón para cerrar el panel de estas opciones --}}
                <button @click.prevent="selectedBox = false" class="btn btn-secondary btn-square"
                    data-toggle="tooltip" data-placement="top" data-original-title="@lang('Cerrar')"
                >
                    <i class="fas fa-times-circle"></i>
                </button>
                {{-- /Botón para cerrar el panel de estas opciones --}}
            
            </div>
        </div>
        {{--/Button Group para opciones con caja de texto seleccionada--}}

    </b-form>

    <template #footer>
        <b-card-text>@lang('Firmante') : @{{box.signer.name}}</b-card-text>
    </template>

</b-card>
{{-- / Opciones de caja de texto seleccionada --}}
