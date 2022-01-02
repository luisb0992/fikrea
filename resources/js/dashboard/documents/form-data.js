/**
 * Configuracion/ creacion de plantillas de formulario de datos especificos
 *
 * Acciones que realiza el usuario para seleccionar, configurar o crear una plantilla de formulario de datos
 * para solicitar informacion a un usuario externo y asi verificar su validez
 *
 * @author LuisBarDev <luisbardev@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

// variables
const { default: Axios } = require('axios');

new Vue({
	el: "#app",
	data: {

		// Decidir si mostrar o no los datos de cada plantilla en el modal
		showModalFormTemplate: false,

		// Si es una nueva plantilla o no (para mostrar el selector de tipo de formulario)
		isNewFormTemplate: false,

		// Mostrar o no el formulario (una vez que ha sido cargado el formulario o reemplazado)
		isShowFormData: false,

		// Verificar si los campos del modal que contiene el formulario, fue validado o no
		isValidateFieldWithSpecificValidations: false,

		// validaciones del documento
		documentValidations: null,

		// verificar si marcar o desmarcar todos los checks
		allChecked: false,

		// Select con los datos de los firmantes
		selectSigners: [],

		// fila de inputs que se añadiran al formulario
		rowsFormData: [],

		// array de datos que contiene los formularios asignados a los firmantes
		dataFormValidated: [],

		// array de datos con los formularios y validaciones asignados en un orden
		// especifico para ser manipulados en la vista
		dataFormValidatedOnView: [],

		// devolver la fila eliminada anteriormente al formulario actualmente cargado
		retrieveRowDataForm: [],

		// validaciones para comprobar los campos de formulario a asignar al firmante
		validateFields: {
			fieldText: false
		},

		// Texto o nombre de los campos de validacion
		textFieldsName: {
			fieldName: 		document.querySelector('#fieldName').textContent,
			fieldText: 		document.querySelector('#fieldText').textContent,
			min: 			document.querySelector('#fieldMin').textContent,
			max: 			document.querySelector('#fieldMax').textContent,
			characterType: 	document.querySelector('#fieldCharacterType').textContent
		},

		// Mensajes de validacion para la app
        messages: {

			// mensajes de error o validacion
            textIsEmpty: 				messages.dataset.textIsEmpty,
            textIsAllEmpty: 			messages.dataset.textIsAllEmpty,
            signIsEmpty: 				messages.dataset.signIsEmpty,
            formDataValidateIsEmpty: 	messages.dataset.formDataValidateIsEmpty,
            formIsEmpty: 				messages.dataset.formIsEmpty,
            checksIsNotChecked:			messages.dataset.checksIsNotChecked,

			// mensajes de exito
            processComplete: 			messages.dataset.processComplete,
            successTemplate: 			messages.dataset.successTemplate,
            successValidation: 			messages.dataset.successMinMaxCharacterTypeValidation,
            loadDataVerification:		messages.dataset.loadDataVerificationComplete,

			// Mensajes de aviso / info
            infoValidation: 			messages.dataset.infoMinMaxCharacterTypeValidation,
        },
	},
	mounted() {

		// fimantes disponibles
		Object.values(this.loadSigners()).forEach(object => this.selectSigners.push(object));

		// validaciones del documento
		this.documentValidations = JSON.parse(document.querySelector('#documentValidations').dataset.documentValidations);
	},
	methods: {

		/**
		 -------------------------------------------------------------------------
		 ------------- Metodos de validacion en distintos procesos ---------------
		 -------------------------------------------------------------------------
		*/

		/**
		 * Verificar si quedan firmantes disponibles para ser añadidos a el formulario
		 *
		 * @returns				// Boolean true / false
		 */
		noSignersAvailable(){
			return document.querySelector('#select-signer').options.length === 0;
		},

		/**
		 * obtener todo los firmantes para este proceso de valdiacion
		 * @returns JSON
		 */
		loadSigners(){
			return JSON.parse(document.getElementById('signers').dataset.signers);
		},

		/**
		 * Obtiene el fimante seleccionado
		 *
		 * @returns 			Rl fimante seleccionado
		 */
		signerSelected(){
			const index = document.querySelector('#select-signer').selectedIndex;
			return document.querySelector('#select-signer').options[index];
		},

		/**
		 * Activar/desactivar los botones segun la configuracion dada
		 * del modal de configuracion del formulario
		 *
		 * @param status 		El estado de la validacion (true o false)
		 * @param index 		EL index indicado a mostrar/ocultar
		 */
		isCorrectAssignedConfigValidation(status, index){

			if (status) {
				document.querySelector(`#btnValidateModal-${index}`).disabled = true;
				document.querySelector(`#btnCloseModal-${index}`).disabled = true;
				document.querySelector(`#msjValidateModal-${index}`).style.display = 'block';
			}else{
				document.querySelector(`#btnValidateModal-${index}`).disabled = false;
				document.querySelector(`#btnCloseModal-${index}`).disabled = false;
				document.querySelector(`#msjValidateModal-${index}`).style.display = 'none';
			}
		},

		/**
		 * Vaciar la propiedad que contiene los firmantes disponibles
		 */
		isSignersEmpty(){
			if (this.noSignersAvailable()) {
				this.selectSigners = [];
			}
		},

		/**
		 * Limpiar el select de los firmantes
		 */
		cleanSelectSigners(){
			this.selectSigners = [];
		},

		/**
		 * Limpiar los inputs del formulario de datos dejandolo completamente vacio
		 */
		cleanRowsFormData(){
			this.rowsFormData = [];
		},

		/**
		 * Limpiar la data validada en el formulario para el firmante
		 */
		cleanDataFormValidated(){
			this.dataFormValidated = [];
		},

		/**
		 * limipar la propiedad que almacena una copia de la fila eliminada
		 */
		cleanRetrieveRowDataForm(){
			this.retrieveRowDataForm = [];
		},

		/**
		 * Limpiar el array que manipula la asignacion en la vista
		 */
		cleanDataFormValidatedOnView(){
			this.dataFormValidatedOnView = [];
		},

		/**
		 * Reiniciar la validacion del campo de texto
		 */
		restartValidationTextField(){
			this.validateFields.fieldText = false;
		},

		/**
		 * Reiniciar el select del tipo de formulario
		 */
		restartFormTypeSelect(){
			this.isNewFormTemplate = false;
		},

		/**
		 * Restablece el check principal que maraca todos los checks
		 */
		restartAllChecked(){
			this.allChecked = false;
		},

		/**
		 * Verificar si algun input checked y field_name contiene o no algun valor
		 *
		 * @param row 			// input a ser validado
		 * @returns Boolean
		 */
		isEmptyFieldName(row){
			return !row.fieldName || row.fieldName === null || row.fieldName.length === 0;
		},

		/**
		 * Verificar si algun input field_name contienen algun valor y esta checked
		 *
		 * @returns Boolean
		 */
		isEmptySomeFieldName(){
			return this.rowsFormData.some(row => (!row.fieldName || row.fieldName === null || row.fieldName.length === 0) && row.checked);
		},

		/**
		 * Verificar si todos los campos fueron validados antes de enviar
		 *
		 * @returns Boolean
		 */
		allFieldsWhereValidated(){
			return this.rowsFormData.every(row => row.isFieldTextValidated);
		},

		/**
		 * Verificar si todos los check estan desmarcados
		 *
		 * @returns Boolean
		 */
		isAllFieldChecked(){
			return this.rowsFormData.every(row => !row.checked);
		},

		/**
		 * Verificar si hay filas de inputs cargada al formulario
		 *
		 * @returns boolean
		 */
		dataFormRowsExist(){
			return this.rowsFormData.length;
		},


		/**
		 -------------------------------------------------------------------------------------------
		 ------------- Metodos de proceso en la creacion y validacion del formulario ---------------
		 -------------------------------------------------------------------------------------------
		*/

		/**
		 * Agregar la plantilla selecionada
		 * tanto del sistema como propias del usuario
		 *
		 * @param event					// datos del formulario
		 */
		addFormTemplateOnView(event) {

			// Validar que existan firmantes disponibles
			if (this.noSignersAvailable()) {
				toastr.error(this.messages.signIsEmpty);
				return false;
			}

			// limpiar todo el cuerpo del formulario, eliminando campos y validaciones
			this.cleanRowsFormData();

			// cargar el ID del template recibido
			const formTemplateId = event.target.dataset.formTemplateId;

			// obtener el formulario seleccionado
			// puede ser de las plantillas del sistema o de las del usuario
			// tanto particulares como empresariales pueden ser cargadas en el html
			const formTemplate = document.querySelector('#form-template-'+formTemplateId);

			// agregar el html al cuerpo principal de la vista para ser configurado
			formTemplate.querySelectorAll('input').forEach( (input, index) => {

				const spanId = input.dataset.spanId;

				const data = {
					type: 			formTemplate.querySelector('#span-type-'+formTemplateId).textContent,
					templateNumber: formTemplate.querySelector('#span-template_number-'+formTemplateId).textContent,
					fieldName: 		formTemplate.querySelector('#span-field_name-'+spanId).textContent,
					fieldText: 		formTemplate.querySelector('#span-field_text-'+spanId).textContent,
					min: 			formTemplate.querySelector('#span-min-'+spanId).textContent,
					max: 			formTemplate.querySelector('#span-max-'+spanId).textContent,
					characterType: 	formTemplate.querySelector('#span-character_type-'+spanId).textContent
				};

				this.loadRowsDataFormArray(data);
			});

			// No mostrar select del tipo de formulario
			this.restartFormTypeSelect();
		},

		/**
		 * Agregar el formulario final ya cargado o creado al firmante o usuario
		 * restando de la lista de usuarios o firmantes los que quedan por validar
		 *
		 * @param event						// evento del formulario
		 * @param validated					// si ya esta validado
		 */
		 addFormToUSerforValidate(event, validated = false){

			// Validar que existan firmantes disponibles
			if (this.noSignersAvailable()) {
				toastr.error(this.messages.signIsEmpty);
				return false;
			}

			// Verificar si existe alguna fila de inputs cargada en el formulario
			if (!this.dataFormRowsExist()) {
				toastr.error(this.messages.formIsEmpty);
				return false;
			}

			// Verificar si todos los inputs estan desmarcados
			if (this.isAllFieldChecked()) {
				toastr.error(this.messages.checksIsNotChecked);
				return false;
			}

			// Verificar si todos los inputs de field name estan vacios
			if (this.isEmptySomeFieldName()) {
				toastr.error(this.messages.textIsEmpty);
				return false;
			}

			// verificar si se validaron todos los campos
			if (!validated) {
				if (!this.allFieldsWhereValidated()) {
					this.$bvModal.show('verification-modal');
					return false;
				}
			}

			this.rowsFormData.forEach(row => {

				if (row.checked) {

					// Almacenar en el arreglo los datos validados del formulario
					// una vez filtrado los inputs checked o validos para la plantilla
					// se le asigna al firmante o usuario externo sus respectivas validaciones
					this.loadArrayDataFormValidated(row);
				}
			});

			// Creacion de html para mostrar en la vista una seccion donde se puedan visualizar los firmantes
			// añadidos con su respectivo formulario de datos validados
			this.renderHtmlOfValidatedSigner();

			// Limpiar la plantilla creada o cargada
			this.cleanRowsFormData();

			// Ocultar select de tipo de formulario
			this.restartFormTypeSelect();

			// restaurar todos los check
			this.restartAllChecked();
		},

		/**
		 * Agregar nueva fila de inputs al formulario de datos a validar
		 */
		addNewFieldToForm(){

			const $this = this;

			// Validar que existan firmantes disponibles
			if (this.noSignersAvailable()) {
				toastr.error(this.messages.signIsEmpty);
				return false;
			}

			const arrayRows = [...document.querySelectorAll('form #bodyFormTemplate .form-row')];

			if(arrayRows.length > 0){

				// statis para el check y la fila de inputs
				const checked = this.allChecked;

				// ultima fila del formulario
				const lastRow = arrayRows[arrayRows.length - 1];

				// Setear tipo y numero de plantilla para la proxima fila de inputs
				const type 			 = lastRow.querySelector('.input-type').value;
				const templateNumber = lastRow.querySelector('.template-number').value;

				this.rowsFormData.push({
					type					: type,
					templateNumber			: templateNumber,
					fieldName				: null,
					fieldText				: null,
					min						: null,
					max						: null,
					characterType			: null,
					isFieldTextValidated	: false,
					checked					: checked
				});

			}else{

				// Mensaje sino hay cargado o creado algun formulario
				toastr.error(this.messages.formIsEmpty);
			}

		},

		/**
		 * Cargar el array con los datos del formulario recibido con el firmante
		 *
		 * @param row 				// fila validada a asignar a la data final
		 */
		loadArrayDataFormValidated(row){
			this.dataFormValidated.push({
				signer_id 			: this.signerSelected().value,
				type    			: row.type,
				template_number    	: row.templateNumber,
				field_name 			: row.fieldName,
				field_text    		: row.fieldText,
				min    				: row.isFieldTextValidated ? row.min : null,
				max    				: row.isFieldTextValidated ? row.max : null,
				character_type		: row.isFieldTextValidated ? row.characterType : null
			});
		},

		/**
		 * Cargar el array de inputs del formulario de datos
		 *
		 * @param data 		Objeto con los datos necesarios
		 */
		loadRowsDataFormArray(data){
			this.rowsFormData.push({
				type					: data.type,
				templateNumber			: data.templateNumber 		? data.templateNumber : null,
				fieldName				: data.fieldName 			? data.fieldName : null,
				fieldText				: data.fieldText 			? data.fieldText : null,
				min						: data.min 					? data.min : null,
				max						: data.max 					? data.max : null,
				characterType			: data.characterType 		? data.characterType : null,
				isFieldTextValidated	: data.isFieldTextValidated ? data.isFieldTextValidated : false,
				checked					: false
			});
		},

		/**
		 * Renderiza en una propiedad todo el contenido html donde se muestra el
		 * firmante o usuario con su respectivo formulario ya validado
		 *
		 */
		renderHtmlOfValidatedSigner(){

			// firmante seleccionado
			const signer = this.signerSelected();

			// Filtrar el array solo con los datos del firmante seleccionado
			const filterSingerDataForm = this.dataFormValidated.filter(row => row.signer_id == signer.value);

			// formato valido para mostrar en la tabla de datos
			filterSingerDataForm.forEach(row => {
				switch (row.character_type) {
					case 'string':
						row.tipo_caracter = document.querySelector('#onlyLetter').textContent;
						break;
					case 'numeric':
						row.tipo_caracter = document.querySelector('#onlyNumber').textContent;
						break;
					case 'special':
						row.tipo_caracter = document.querySelector('#onlySpecial').textContent;
						break;
					default:
						row.character_type;
						break;
				}
			});

			// cargar los datos a mostrar en la vista
			this.dataFormValidatedOnView.push({
				signer_id 			: signer.value,
				nameSigner			: signer.textContent.trim(),
				dataValidated		: filterSingerDataForm
			});

			// Eliminar firmante del select
			document.querySelector('#select-signer').remove(document.querySelector('#select-signer').selectedIndex);
			this.isSignersEmpty();	// Vaciar o no la propiedad de firmantes
		},

		/**
		 * Crear plantilla desde cero, esto añadira un fila con los campos del formulario
		 * y el tipo de formulario
		 */
		addNewFormTemplate(){

			// Validar que existan firmantes disponibles
			if (this.noSignersAvailable()) {
				toastr.error(this.messages.signIsEmpty);
				return false;
			}

			// Mostrar select del tipo de formulario
			this.isNewFormTemplate = true;

			// Limpiar los inputs y agregar la nueva fila
			this.cleanRowsFormData();

			// Cargar el array con los datos de la fila de inputs
			const data = {
				type: document.querySelector('#select-template').value
			}

			this.loadRowsDataFormArray(data);
		},

		/**
		 * Cambiar el tipo de plantilla para los valores
		 * de los inputs cargados para la nueva plantilla
		 *
		 * @param event  			// Evento con el valor del select
		 */
		changeValueInType(event){

			const arrayRows = [...document.querySelectorAll('form #bodyFormTemplate .form-row')];

			arrayRows.forEach(row => {
				row.querySelector('.input-type').value = event.target.value;
			});
		},

		/**
		 * Validar el campo "Texto" con unas respectivas validaciones asignadas por el usuario
		 * como pueden ser "minimo", "maximo", "tipo de caracter"
		 *
		 * @param index 			index de la posicion del elemento
		 */
		validateFieldWithSpecificValidations(index){

			// cerrar modal
			this.$bvModal.hide('validationModal-'+index);

			if (this.rowsFormData[index]) {
				this.rowsFormData[index].isFieldTextValidated = true;
				toastr.success(this.messages.successValidation);
			}else{
				console.error(`No existe el index [${index}] especificado`);
			}
		},

		/**
		 * Manipular el cierre del modal de validaciones asignadas al usaurio
		 * verificar si ya se ha validado los datos o no previamente
		 *
		 * @param index 			index de la posicion del elemento
		 */
		closeModalValidationField(index){

			// cerrar modal
			this.$bvModal.hide('validationModal-'+index);

			if (this.rowsFormData[index]) {

				if (this.rowsFormData[index].isFieldTextValidated == false) {
					toastr.info(this.messages.infoValidation);
				}

			}else{
				console.error(`No existe el index [${index}] especificado`);
			}
		},

		/**
		 * Validar si el campo maximo es mayor al minimo
		 * o si la validacion es aceptable
		 *
		 * @param index 				index de la posicion del elmento seleccionado
		 */
		 isMinMaxValidationValid(index){

			if (this.rowsFormData[index]) {

				// posibles validaciones
				const min 			= this.rowsFormData[index].min;
				const max 		 	= this.rowsFormData[index].max;

				// validar primero el min y el max valor
				if ((!max && min) || (max == 0 && min > 0) || (max == 0 && min == 0)) {
					this.isCorrectAssignedConfigValidation(true, index);
					return false;
				}

				// validaciones adicionales
				const minGreater 	= parseInt(min) >= parseInt(max);
				const isMinNumber 	= typeof parseInt(min) === 'number';
				const isMaxNumber 	= typeof parseInt(max) === 'number';

				// validaciones faltantes
				if ((minGreater) || (!isMinNumber || !isMaxNumber)) {
					this.isCorrectAssignedConfigValidation(true, index);
				}else{
					this.isCorrectAssignedConfigValidation(false, index);
				}

			}else{
				return console.error(`No existe el index [${index}] especificado`);
			}
		},

		/**
		 * Eliminar todos los datos relacionados a esa asignacion de formulario
		 * hacia un firmante
		 *
		 * @param index 			index de la posicion en dataFormValidatedOnView
		 * @param signerId 			id del fimrante
		 */
		removeFormAssignment(index, signerId){

			// eliminar datos en el array que se manipula la vista
			this.dataFormValidatedOnView.splice(index, 1);

			// Eliminar del array de objetos principal donde se envian añ backend
			this.dataFormValidated = this.dataFormValidated.filter(object => object.signer_id != signerId);

			// devolver el firmante nuevamente al select principal
			const signer = Object.values(this.loadSigners()).filter(object => object.id == signerId);
			this.selectSigners.push(signer[0]);

			// reestablecer el check principal
			this.restartAllChecked();
		},

		/**
		 * editar todos los datos relacionados a esa asignacion de formulario
		 * hacia un firmante
		 *
		 * @param index 			index de la posicion en dataFormValidatedOnView
		 * @param signerId 			id del fimrante
		 */
		editFormAssignment(index, signerId){

			// limpiar todo el cuerpo del formulario, eliminando campos y validaciones
			this.cleanRowsFormData();

			// datos filtraods por el firmante para ser validados nuevamente
			const filterDataForm = this.dataFormValidated.filter(object => object.signer_id == signerId);

			// iteracion y asignacion de datos a la propiedad rowsFormData
			// para ser editados
			filterDataForm.forEach(object => {
				const data = {
					type 				 : object.type,
					templateNumber 		 : object.template_number,
					fieldName 			 : object.field_name,
					fieldText 			 : object.field_text,
					min 				 : object.min,
					max 				 : object.max,
					characterType 		 : object.character_type
				};

				this.loadRowsDataFormArray(data);
			});

			// eliminar todo rastro de la antigua asignacion
			this.removeFormAssignment(index, signerId);
		},

		/**
		 * Eliminar la fila de inputs del array de formulario de datos
		 *
		 * @param index 		index de la posicion del array
		 */
		deleteDataFormRow(index){

			// limpiar la copia
			this.cleanRetrieveRowDataForm();

			// almacenar la fila eliminada por si se quiere recuperar
			this.retrieveRowDataForm.push(this.rowsFormData[index]);

			// eliminar toda la fila
			this.rowsFormData.splice(index, 1);

			// Abrir modal de confirmacion
			this.$bvModal.show('retrieve-row-modal');
		},

		/**
		 * Recuperar la fila anteriormente eliminada y agregarla al formulario
		 */
		retrieveRowAndAddIntoForm(){

			// agregar la fila eliminada al formulario principal
			this.rowsFormData.push(this.retrieveRowDataForm[0]);

			// Cerrar el modal
			this.$bvModal.hide('retrieve-row-modal');
		},

		/**
		 * Cerrar el modal de aviso de validaciones incmpletas y
		 * continuar con el proceso de validacion
		 */
		closeModalAndContinue(){

			// Cerrar modal
			this.$bvModal.hide('verification-modal');

			// continuar con el proceso asignado true a la validacion
			this.addFormToUSerforValidate(true, true);
		},

		/**
		 * Cambiar estado de los inputs tipo check para el formulario cargado
		 */
		changeCheckStatus(){

			// cambiar el estado de la propiedad principal
			this.allChecked = !this.allChecked;

			// cambiar el estado de todos los demas inputs del formulario
			this.allChecked ? this.rowsFormData.map(object => object.checked = true) : this.rowsFormData.map(object => object.checked = false);
		},

		/**
		 * Cargar la vrificacion de datos que fue realizada anteriormente
		 */
		loadDataVerification(){

			// eliminar datos en el array que manipula la vista
			this.cleanDataFormValidatedOnView();

			// Eliminar del array de objetos principal donde se envian al backend
			this.cleanDataFormValidated();

			// vaciar todos los firmantes
			this.cleanSelectSigners();

			// reestablecer el check principal
			this.restartAllChecked();

			// Limpiar el formulario previamente cargado o configurado
			this.cleanRowsFormData();

			// obtener los datos previamente guardados y darle el formato apropiado
			const formDataValidation = JSON.parse(document.querySelector('#formDataValidation').dataset.formDataValidation);

			// Convertir a un array valido y agregar toda la configuracion a las propiedades
			Object.entries(formDataValidation).forEach(signer => {

				// formatear y cargar el array principal
				signer[1].forEach(row => {
					this.dataFormValidated.push({
						signer_id 			: row.signer_id,
						type    			: row.type,
						template_number    	: row.template_number,
						field_name 			: row.field_name,
						field_text    		: row.field_text,
						min    				: row.min,
						max    				: row.max,
						character_type		: row.character_type
					});
				});

				// datos del firmante para ser cargados a la vista
				let nameSIgner = Object.values(this.loadSigners()).filter(object => object.id == signer[0]);
				nameSIgner = nameSIgner[0].name ? `${nameSIgner[0].name} ${nameSIgner[0].lastname}` : `${nameSIgner[0].email}`;

				// filtrar los datos por firmante para ser cargados en la vista
				const filterSingerDataForm = this.dataFormValidated.filter(row => row.signer_id == signer[0]);

				// formato valido del tipo de caracter para mostrar en la tabla de datos
				this.formatDataFormAssignedToTheSigner(filterSingerDataForm);

				// cargar datos al array que se manipula en la vista
				this.dataFormValidatedOnView.push({
					signer_id 			: signer[0],
					nameSigner			: nameSIgner,
					dataValidated		: filterSingerDataForm
				});
			});

			// Mensaje de proceso completado
			toastr.success(this.messages.loadDataVerification);
		},

		/**
		 * Dar un formato apropiado al tipo de caracter para ser renderizado
		 * en la vista y tener una visual limpia
		 *
		 * @param signerDataForm 			array de datos
		 */
		formatDataFormAssignedToTheSigner(signerDataForm){
			signerDataForm.forEach(row => {
				switch (row.character_type) {
					case 'string':
						row.tipo_caracter = document.querySelector('#onlyLetter').textContent;
						break;
					case 'numeric':
						row.tipo_caracter = document.querySelector('#onlyNumber').textContent;
						break;
					case 'special':
						row.tipo_caracter = document.querySelector('#onlySpecial').textContent;
						break;
					default:
						row.character_type;
						break;
				}
			});
		},

		/**
		 * Guardar validacion de fomulario de datos para los firmantes
		 *
		 * @param event 			// Evento del elemento
		 */
		saveFormDataValidation(event){

			if (!this.dataFormValidated.length) {
				toastr.error(this.messages.formDataValidateIsEmpty);
				return false;
			}

			 // Obtiene la url a la que se redirige tras guardar las validaciones
			const dataset = {
				saveAndContinue: 			event.currentTarget.dataset.saveContinue,             			// Valor para guardar y continuar (true o false)
				toSaveFormdataValidation: 	event.currentTarget.dataset.saveFormdataValidation,   			// Guardar formulario de datos
				toDocumentRequest: 			event.currentTarget.dataset.redirectToDocumentRequest,          // Página de solictud de documentos
				toList: 					event.currentTarget.dataset.redirectToList             			// Página para listar los documentos
			};

			HoldOn.open({theme: 'sk-circle'}); // Inicio de animacion

			// Envía la validacione y los firmantes junto asu formulario de datos
			axios.post(dataset.toSaveFormdataValidation, {
				formDataValidate: this.dataFormValidated,
				saveAndContinue: dataset.saveAndContinue
			})
			.then(() => {

				HoldOn.close();									// Detener animacion
				toastr.success(this.messages.processComplete);	// Mensaje de proceso completado

				const documentRequest = this.documentValidations.filter(validation => validation.DOCUMENT_REQUEST_VERIFICATION).length > 0;

				// Si existe validacion de solicitud de documentos
				if (documentRequest) {
					location.href = dataset.toDocumentRequest;
				}else{
					location.href = dataset.toList;
				}
			})
			.catch(() => {
				HoldOn.close(); // Detener animacion
				console.error(error);
			});
		},
	},

	computed: {

		/**
		 * 	Activar/desactivar botones para continuar y guardar validacion
		 *
		 * @returns Boolean
		 */
		isDisabled() {
			return !this.selectSigners.length && this.dataFormValidated.length > 0 ? false : true;
		},

		/**
		 * Activar / desactivar botones si hay o no firmantes disponibles
		 *
		 * @returns Boolean
		 */
		isNoSingerAvailable() {
			return this.selectSigners.length ? false : true;
		}
	}
});
