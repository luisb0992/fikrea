/**
 * Selecciona las validaciones de los usuarios
 *
 * Son las acciones que debe realizar un usuario para aprobar un documento
 * 
 * @author javieru  <javi@gestoy.co>
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
    data: {

        validations: [],        // Una lista con las validaciones de cada firmante

    },
    /**
     * Cuando la instancia está montada
     * 
     * 
     */
    mounted: function () {
        this.arrayFromList();
    },
    methods: {

        /**
         * Chequea por si se desmarca la opción de firma manuscrita
         * para revisar por la validación de captura de pantalla
         * 
         * @param {Event} event
         */
        checkForCaptureValidation: function (event) {
            // Obtengo el firmante
            const signer = event.target.dataset.userId;

            // Verifico si se desmarca esta validación de firma manuscrita
            if (!event.target.checked) {

                // aquí desmarco la opción de validación con captura de pantalla
                // guardando su estado por si vuelvo a marcar esta opción
                let captureStatus = false;
                document.querySelectorAll('.validation').forEach(validation => {
                    if (validation.dataset.userId == signer && validation.dataset.validationId == 5) {
                        captureStatus = validation.checked;
                        validation.checked = false;
                    }
                });
                localStorage.setItem(`validation-capture-${signer}`, captureStatus);

            } else {
                // Acá verificamos que haya estado seleccionada la opción de captura de pantalla
                // para activarla nuevamente
                const status = localStorage.getItem(`validation-capture-${signer}`);
                if (status === "true") {
                    // Activamos la captura de pantalla
                    document.querySelectorAll('.validation').forEach(validation => {
                        if (validation.dataset.userId == signer && validation.dataset.validationId == 5) {
                            validation.checked = true;
                        }
                    });
                    // Eliminamos del storage
                    localStorage.removeItem(`validation-capture-${signer}`);
                }
            }
        },

        /**
         * Chequea si el firmante 'signer' ha seleccionado la
         * validación por firma manuscrita
         * 
         * @param int signer El id del firmante
         */
        checkHandWritingValidationFor: function (signer) {
            // Obtengo todas las validaciones de este firmante
            let myValidations = [];
            document.querySelectorAll('.validation').forEach(_validation => {
                
                if (_validation.dataset.userId == signer) {
                    myValidations.push(
                        {
                            validation  : _validation.dataset.validationId,
                            selected    : _validation && _validation.checked? _validation.checked : false,
                        }
                    );
                }
            });

            // Obtengo la validación por firma manuscrita @see \App\Enums\ValidationType
            const handWriting = myValidations.filter(validation => validation.validation == 1) || null;
            return (handWriting === undefined || handWriting === [])? false : handWriting[0].selected;
        },

        /**
         * Chequea si el firmante 'signer' ha seleccionado la
         * validación por edición de documento
         * 
         * @param int signer El id del firmante
         */
        checkDocumentEditorValidationFor: function (signer) {
            // Obtengo todas las validaciones de este firmante
            let myValidations = [];
            document.querySelectorAll('.validation').forEach(_validation => {
                
                if (_validation.dataset.userId == signer) {
                    myValidations.push(
                        {
                            validation  : _validation.dataset.validationId,
                            selected    : _validation && _validation.checked? _validation.checked : false,
                        }
                    );
                }
            });

            // Obtengo la validación por firma manuscrita @see \App\Enums\ValidationType
            const documentEditor = myValidations.filter(validation => validation.validation == 8) || null;
            return (documentEditor === undefined || documentEditor === [])? false : documentEditor[0].selected;
        },
        
        /**
         * Chequea que la captura de pantalla se pueda seleccionar
         * solamente cuando se ha seleccionado validación por:
         * - editor de documento
         * - firma manuscrita
         * 
         * @param {Event} event
         */
        checkCaptureValidations: function (event) {
            // Al marcar esta opción debemos garantizar que solo se active si yo como firmante
            // seleccioné la firma manuscrita sobre este documento o la edición de documento
            if (event.target.checked) {
                const canCheck = this.checkHandWritingValidationFor(event.target.dataset.userId)
                    || this.checkDocumentEditorValidationFor(event.target.dataset.userId);

                if (canCheck) {
                    event.target.checked = true;
                    return true;
                } else {
                    event.target.checked = false;
                    toastr.warning(event.target.dataset.message);
                    return false;
                }
            }
            return false;
        },

        /**
         * Guarda las validaciones del documento
         * y redirige según las validaciones seleccionadas siguiendo el orden :
         * 1 ) editor de documentos
         * 2 ) firma manuscrita
         * 3 ) certificación de datos
         * 4 ) solicitud de documentos
         * 
         * @param {Event} event
         */
        saveValidations: function (event) {

            // Obtiene la url para guardar las validaciones
            const request  = event.currentTarget.dataset.requestSaveValidations;

            // Obtiene la url a la que se redirige tras guardar las validaciones
            const redirect =
                {
                    toTextboxs: event.currentTarget.dataset.redirectToTextboxs,     // Página de config de cajas de textos
                    toSign: event.currentTarget.dataset.redirectToSign,             // Página de config de firma del documento
                    toFormData: event.currentTarget.dataset.redirectToFormdata,     // Página de formulario de datos
                    toConfigDocumentsRequest: event.currentTarget.dataset.redirectToConfigDocumentsRequest,   // Página de config de Solicitudes de documentos
                    toList: event.currentTarget.dataset.redirectToList,             // Página de lista de documentos
                };

            // Obtener las validaciones
            document.querySelectorAll('.validation').forEach(validation => {
                this.validations.push(
                    {
                        user        : validation.dataset.userId,
                        validation  : validation.dataset.validationId,
                        selected    : validation.checked
                    }
                );
            });

            // Muestra la animación
            HoldOn.open({theme: 'sk-circle'});

            // Envía las validaciones y los firmantes (para poder especificar el tipo de firmante)
            axios.post(request, {
                validations : this.validations,
            })
            .then(resp => {
                const validateTextboxs  = this.validations.filter(validation => validation.validation == 8 && validation.selected).length > 0;
                const validateSign      = this.validations.filter(validation => validation.validation == 1 && validation.selected).length > 0;
                const validateFormData  = this.validations.filter(validation => validation.validation == 7 && validation.selected).length > 0;
                const validateRequestDocument= this.validations.filter(validation => validation.validation == 6 && validation.selected).length > 0;

                // Si ha seleccionado validación de cajas de textos
                if (validateTextboxs) {
                    // Como hay que configurar cajas de textos
                    // Redirigimos a la página para configurar cajas de textos
                    location.href = redirect.toTextboxs;

                // Si ha seleccionado validación de firma manuscrita
                } else if (validateSign) {
                    // Como hay firmas manuscritas que realizar y/o configurar
                    // Redirige a la página para configurar la firma del documento
                    location.href = redirect.toSign;

                // Si se ha seleccionado validación de verificación de datos (formulario)
                } else if(validateFormData) {
                    // Redirige a la pagina del formulario de datos
                    location.href = redirect.toFormData;

                // Si se ha seleccionado alguna validación por Solicitud de Documentos
                } else if (validateRequestDocument) {
                    // Como hay validaciones de Solicitud de Documentos que configurar
                    // Redirige a la página para configurar las Solicitud de Documentos
                    location.href = redirect.toConfigDocumentsRequest;

                } else {
                    // Como no hay ninguna de las validaciones anteriores a realizar y/o configurar
                    // Redirige a la página de lista de documentos
                    location.href = redirect.toList;
                }

                // Oculta la animación
                HoldOn.close();
            })
            .catch((error) => {
                // Oculta la animación
                HoldOn.close();
                console.error(error);
            });
        },

        /**
         * Comprueba se esta cambiando de ruta
         *       
         * Verifica si se esta cambiando de ruta al oprimir cualquiera opcion del sidebar 
         * Envia el modal con la pregunta, si esta seguro de cambiar de ruta
         */
        arrayFromList: function () {
            var self = this;
            //Array con cada value:
            var dataValue = [...document.querySelectorAll(`ul.vertical-nav-menu li`)]
                .forEach((element) => {
                    if (element.className != "app-sidebar__heading") {
                        let href = element.childNodes[1].href;
                        element.addEventListener("click", async (e) => {
                            e.preventDefault();
                            await self.$bvModal.show('link-sidebar-modal');
                            let link = document.getElementById('linkSidebarModal');
                            link.href = href;
                        });
                    }
                });
        }
    }
 });