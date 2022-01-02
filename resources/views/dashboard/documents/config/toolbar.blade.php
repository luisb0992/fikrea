{{-- Barra de Herramientas --}}
<div class="toolbar">
    
    {{-- Página --}}
    <div class="toolbar-item page">
        <span class="bold">@lang('Página')</span>:
        <input @change="goPage(page)" type="number" maxlength="3" min="1" v-model.number="page" />
        <span class="separator">@lang('de')</span>    
        <span id="pages">@{{pages ?? 1}}</span>
    </div>
    {{-- /Página --}}

    {{-- Barra de herramientas; siguiente, anterior, zoom --}}
    <div class="toolbar-item">
        {{-- Botones navegación por el documento --}}
        <span class="btn-group" role="group" aria-label="@lang('Navegación')">
            <button @click.prevent="pagePrev" class="btn btn-warning btn-transition"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Página anterior')">
                <i class="fas fa-step-backward"></i>
            </button>
            <button @click.prevent="pageNext" class="btn btn-warning btn-transition"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Página siguiente')">
                <i class="fas fa-step-forward"></i>
            </button>
        </span>
        {{-- /Botones navegación por el documento --}}

        {{-- Botones para el zoom --}}
        <span class="btn-group" role="group" aria-label="@lang('Opciones zoom')">
            <button @click.prevent="zoomMinus" class="btn btn-warning btn-transition"
                :disabled="fit"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Reducir')">
                <i class="fas fa-search-minus"></i>
            </button>
            <button v-if="!fit" @click="zoomFit" class="btn btn-warning btn-transition"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ajustar a pantalla')">
                <i class="fas fa-expand"></i>
            </button>
            <button v-else @click="zoomReset" class="btn btn-warning btn-transition"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ajustar a tamaño original')">
                <i class="fas fa-expand-arrows-alt"></i>
            </button>
            <button @click.prevent="zoomPlus" class="btn btn-warning btn-transition"
                :disabled="fit"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ampliar')">
                <i class="fas fa-search-plus"></i>
            </button>
        </span>
        {{-- / Botones para el zoom --}}

        {{-- Botones selección de herramienta --}}
        <span class="btn-group" role="group" aria-label="@lang('Herramientas')">
            <button @click.prevent="selectTool(Tool.Sign)" type="button" class="btn btn-warning btn-transition" :class="tool == Tool.Sign ? 'active': ''"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Fijar firma')">
                <i class="fas fa-signature"></i>
            </button>
            <button @click.prevent="selectTool(Tool.Stamp)" type="button" class="btn btn-warning btn-transition" :class="tool == Tool.Stamp ? 'active': ''"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Estampar sello')">
                <i class="fas fa-stamp"></i>
            </button>
        </span>
        {{-- / Botones selección de herramienta --}}

    </div>
    {{-- /Barra de herramientas; siguiente, anterior, zoom --}}

    {{-- Escala --}}
    <div class="toolbar-item scale">
        <span class="bold">@lang('Escala')</span>:
        <span>@{{ Math.round( scale * 100) | int }} %</span>
    </div>
    {{-- /Escala --}}

</div>
{{-- /Barra de Herramientas --}}	