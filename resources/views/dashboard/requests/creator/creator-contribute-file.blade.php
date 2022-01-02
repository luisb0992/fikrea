{{-- Data que se utiliza cuando el creador quiere subir archivos desde su nube --}}
<div id="creator-data"
	data-file-system-tree="@json($fileSystemTreeselect)"
	data-file-system="@json($fileSystem)"
	data-request-file-content="@route('dashboard.files.get.content.b64', ['file'=>'X'])"
></div>
{{-- Data que se utiliza cuando el creador quiere subir archivos desde su nube --}}

{{-- Formulario para aportar un documento --}}
<div class="main-card mb-3 card">

	{{--Seleccion de documento a aportar--}}
    <div v-if="nonAportedDocuments.length" class="card-body">
        <h5 class="card-title">
            @lang('Documento que va a aportar')
        </h5>

        <div>
        	
        	{{-- Cargar un nuevo archivo --}}
        	<b-form>

				{{-- Selección del documento --}}
				<b-form-group id="name-request"
					label="@lang('Documento a aportar')"
					label-for="name-request"
				>

					<select id="document"
	                	@change="clearIssuedDate"
	                	v-model="selected" 
		                class="form-control mt-2"
		            >
		                <option 
		                	v-for="(document, index) in nonAportedDocuments"
		                	:value="{id: document.id, text: document.name}"
		                	>
		                    @{{document.name}}
		                </option>
		            </select>
					 
					<b-form-invalid-feedback id="input-1-live-feedback">
						@lang('Debe introducir el nombre de la solicitud')
					</b-form-invalid-feedback>

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

				{{-- Checkbox para seleccionar archivo desde la nube de fikrea --}}
	            <b-form-group>
					
					<b-form-checkbox
						id="ch-file-from-fikrea"
						v-model="fileFromFikrea"
						size="lg"
						:disabled="cannotUploadDocument"
					>
					<span v-if="cannotUploadDocument" style="color: #ccc !important;">
						@lang('Seleccionar desde nube de Fikrea')
					</span>
					<span v-else style="color: #9900cc !important;">
						@lang('Seleccionar desde nube de Fikrea')
					</span>
					</b-form-checkbox>

		        </b-form-group>
				{{-- Checkbox para seleccionar archivo desde la nube de fikrea --}}

				{{-- Selección de archivo desde nube de fikrea --}}
				<div v-if="fileFromFikrea">

					<b-form-group >

						{{-- Selector de archivo --}}
						<treeselect
							v-model="fromFikreaCloud"
							{{-- :multiple="true" --}}
							:flat="true"
							:multiple=true
							:show-count="true"
							:options="optionsFikreaCloud"
							size="lg"
							placeholder="@lang('Seleccione su archivo desde la nube')..."
						/>
						{{-- / Selector de archivo --}}

					</b-form-group>

					<b-form-group >

						 <button
		                	type="button"
		                	@click.prevent="selectForTree"
		                	:disabled="!fromFikreaCloud || fromFikreaCloud.length == 0"
		                	class="btn btn-success">
		                    @lang('Seleccionar')
		                </button>

					</b-form-group>
					
				</div>
					
				{{-- Selección de archivo desde nube de fikrea --}}
				
	            {{-- Botón para seleccionar el archivo --}}
	            <b-form-group v-else>
					
	                <input @change="loadFile" accept="image/*;application/pdf" id="file" ref="file" name="file" type="file" class="d-none" />
	                <button
	                	@click="$refs.file.click()"
	                	type="button"
	                	:disabled="cannotUploadDocument"
	                	class="btn btn-success">
	                    @lang('Seleccionar Archivo')
	                </button>
		        	 
		        </b-form-group>
	            {{--/Botón para seleccionar el archivo --}}
			     
		  	</b-form>
        	{{-- / Cargar un nuevo archivo --}}

		</div>
    </div>
    {{--Seleccion de documento a aportar--}}

    {{--Mensaje de que todo ha sido aportado--}}
    <div v-else class="card-body">
    	<span class="text-success bold">
    		@lang('Ya usted ha seleccionado todos los documentos requeridos').
    	</span>
    </div>
    {{--Mensaje de que todo ha sido aportado--}}

</div>
{{-- / Formulario para aportar un documento --}}
