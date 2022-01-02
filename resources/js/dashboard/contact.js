/**
 * Gestión del contacto con el cliente
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos 
 */

new Vue({
    el: '#app',
    data: {
        email   : '',                   // La dirección de correo o teléfono
        subject : '',                   // El asunto
        content : '',                   // El contenido
        acceptUseConditions: false,     // La aceptación de las condiciones de uso
        success : false,                // Si el mensaje de contacto ha sido procesado con éxito o no
    },
    /**
     * Cuando la instancia ha sido montada
     *  
     */
    mounted: function () {

        // Fija la entrada en el campo de contacto
        document.getElementById('email').focus();
    },
    methods: {

        /**
         * Procesa el formulario de contacto
         * 
         * @param {Event} event
         * 
         */
        contact: function (event) {
            event.preventDefault();

            // La información de contacto
            const contact = this;


            HoldOn.open({theme: 'sk-circle'});

            // Realiza la petición
            axios.post(form.action, {
                email   : contact.email,        // La dirección de correo
                subject : contact.subject,      // El asunto
                content : contact.content       // El contenido
            })
            .then(response => {
                console.info(response.data.message);
                // Se indica que la información de contacto ha sido enviada con éxito
                contact.success = true;
                HoldOn.close();
            })
            .catch(error => {
                console.log(error);
                HoldOn.close();
            });
        }
    },
    computed: {
        /**
         * Habilita/Deshabilita el botón para enviar el formulario de contacto
         * en función de si se han aceptado o no las condiciones de uso
         * 
         */
        isDisabled: function() {
            return !this.acceptUseConditions;
        }
    }
});