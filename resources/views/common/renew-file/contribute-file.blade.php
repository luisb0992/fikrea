{{-- Formulario para aportar un documento --}}
<div class="main-card mb-3 card">
	
	{{--Seleccion de documento a aportar--}}
    <div v-if="nonAportedDocuments.length" class="card-body">

		<h5 class="card-title">
            @lang('Documento que va a renovar')
        </h5>

        <div>
        	
        	{{-- Cargar un nuevo archivo --}}
        	<b-form>

			    {{-- Selección del documento --}}
	            <b-form-group id="name-request" label="@lang('Documento')" label-for="name-request">

	                <select id="document"
	                	@change="clearIssuedDate"
	                	v-model="selected" 
		                class="form-control mt-2"
		            >
		                <option 
		                	v-for="(document, index) in documents"
		                	:value="{id: document.id, text: document.name}"
		                	>
		                    @{{document.name}}
		                </option>
		            </select>

	            </b-form-group>
		        {{-- / Selección del documento --}}

				<div class="row">
					<div class="col-md-6">
						{{-- La fecha de expedición del documento --}}
						<b-form-group id="fecha-request"
								label="@lang('Fecha de expedición')"
								label-for="fecha-request"
						>
							<vuejs-datepicker
								:language="datepickerLanguage"
								format="dd-MM-yyyy" input-class="datepicker" placeholder="@lang('Fecha de expedición')"
								v-model="issued_to"
							>
							</vuejs-datepicker>

						</b-form-group>
						{{--/La fecha de expedición del documento --}}
					</div>
					<div class="col-md-6">
						{{-- La fecha de vencimiento en caso de haberse solicitado del documento --}}
						<b-form-group id="expiration-request"
							label="@lang('Fecha de vencimiento')"
							label-for="expiration-request"
							v-if="requiredDocument && requiredDocument.has_expiration_date"
						>
							<vuejs-datepicker
								:language="datepickerLanguage"
								format="dd-MM-yyyy" input-class="datepicker" placeholder="@lang('Fecha de vencimiento')"
								:disabled-dates="disabled_dates"
								v-model="expiration_date">
							</vuejs-datepicker>

							<span v-if="cannotUploadDocument" class="text-danger text-sm">
								Debe seleccionar la fecha de vencimiento para poder aportar el documento.
							</span>
							
						</b-form-group>
						{{-- / La fecha de vencimiento en caso de haberse solicitado del documento --}}
					</div>
				</div>

	            {{-- Botón para selecciona el archivo --}}
	            <b-form-group>
					
	                <input @change="loadFile" accept="image/*;application/pdf" id="file" ref="file" name="file" type="file" class="d-none" />
	                <button
	                	@click="$refs.file.click()"
	                	type="button"
	                	:disabled="cannotUploadDocument"
	                	class="btn btn-success">
	                    @lang('Seleccionar Archivo')
	                </button>
		        	 
		        </b-form-group>
	            {{--/Botón para selecciona el archivo --}}

			     
		  	</b-form>
        	{{-- / Cargar un nuevo archivo --}}

		</div>

    </div>

	{{--Mensaje de que todo ha sido renovado--}}
    <div v-else class="card-body">
    	<span class="text-success bold">
    		@lang('Ya usted ha renovado todos los documentos requeridos').
    	</span>
    </div>
    {{--Mensaje de que todo ha sido renovado--}}

</div>
{{-- / Formulario para aportar un documento --}}
