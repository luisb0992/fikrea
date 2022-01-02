/**
 * Edición de la subcripción de usuario
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 * 
 */

new Vue({
    el: '#app',
    components: {
        vuejsDatepicker
    },
    data: 
        {
            // El idioma que aplica al selector de fechas (datepicker)
            // Se obtiene del atributo lang del elemento html de la página
            //
            // La propiedad datepickerLanguage para el idioma español es un JSON:
            //
            // vdp_translation_es.js
            //
            // @link https://www.npmjs.com/package/vuejs-datepicker
            datepickerLanguage : eval(
                `vdp_translation_${document.querySelector('html').getAttribute('lang')}.js`
            ),

            // El usuario de la subscripción
            user : 
                {
                    custom_disk_space   : null,
                },

            // La subscripción del usuario
            subscription:
                {
                    id                  : null,         // El id de la subscripción
                    plan_id             : null,         // El id del plan de subcripción
                    starts_at           : null,         // La fecha de inicio de la subscripción
                    ends_at             : null,         // La fecha de finalización de la subscripción
                },
            // El formulario con los datos de la subscripción
            form: document.getElementById('subscription'),
        },
    /**
     * Cuando la instancia está montada
     * 
     * 
     */
    mounted: function () {
        // Carga la subscripción del usuario
        this.subscription = JSON.parse(this.form.dataset.subscription);
        // Carga el usuario
        this.user         = JSON.parse(this.form.dataset.user);
    }
});
