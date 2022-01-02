{{-- Generar URL --}}
<div class="row container">
	<div class="col-sm-12 ml-3 mb-4">
        <div class="main-card card">
            <div class="card-body">

                <b-form-group>

                    <b-input-group prepend="@lang('URL generada')" class="mt-3">
					    <b-form-input id="generated-url"
	                        id="generated-url-input"
	                        placeholder="http://"
	                        v-model="generatedUrl"
	                        disabled
	                        size="lg"
	                    ></b-form-input>
					    <b-input-group-append>
					      	<b-button @click.prevent="copyToClipboard" variant="outline-success">
					      		<i class="fa fa-copy"></i>
					      		@lang('Copiar')
					      	</b-button>
					    </b-input-group-append>
					</b-input-group>

		        </b-form-group>

            </div>
        </div>
    </div>
</div>
{{--/ Generar URL --}}
