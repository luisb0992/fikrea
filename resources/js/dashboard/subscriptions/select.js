/**
 * Selección de la susbcripción
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * La lista de planes
 */
const Plan =
    {
        Trial: 0,
        Premium: 1,
        Pro: 2,
    };

new Vue({
    el: '#app',
    data: {
        current_plan: null,                         // El plan actual
        plan: null,                                 // El plan elegido
        months: 1,                                  // El número de meses del plan
    },

    /**
     * Cuando la instancia está montada
     */
    mounted: function () {
        // Obtiene los datos del plan actual
        const plan = document.getElementById('plan');

        this.current_plan = 
            {
                id: plan.dataset.currentPlan,
                remainingDays: plan.dataset.remainingDays,
                changePlanPrice: plan.dataset.changePlanPrice,
            };

        // Muestra el mensaje de ayuda
        let message = document.querySelector('.btn-contact').dataset.title;
        toastr.info(
            message, '', {
                closeButton: false,
                positionClass: 'toast-top-right',
                timeOut: 5000,
        });
    },
    methods: {
        /**
         * Selecciona el plan indicado
         *
         * @param {Plan}  plan    El plan
         *
         */
        select: function (plan) {
            this.plan = plan;
        },
        /**
         * Si el plan ha cambiado o no
         *
         * @return {Boolean}
         *
         */
        planHasChanged: function () {
            const planHasChanged = this.plan.id != this.current_plan.id;

            // Calcula el número de meses para los cuales debe imputarse un coste adicional
            // Para los 30 primeros días no se imputan costes
            // A partir de los 30 primeros días por cada mes se efectúa un incremento

            this.current_plan.changeMonths = planHasChanged
                ? parseInt(this.current_plan.remainingDays / 30)
                : 0;

            return planHasChanged;
        },
        /**
         * Al pulsar el botón de pago
         *
         * @param {Event} event
         *
         */
        pay: function (event) {
            // Espera mientras se conecta a la pasarela de pago
            HoldOn.open({
                theme: 'sk-circle',
                message: event.currentTarget.dataset.waitingMessage,
            });
        },
        /**
         * Obtiene el importe adicional de la subscripción originada por un cambio
         * a un plan superior
         *
         * @return {Number}
         *
         */
        aditionalAmount: function () {
            return this.current_plan.changeMonths * this.current_plan.changePlanPrice;
        },
    },
    computed: {
        /**
         * El precio unitario de la subscripción
         *
         * @return {Number}
         *
         */
        price: function () {
            if (!this.plan) {
                return 0;
            } else {
                return this.months < 12
                    ? this.plan.monthly_price
                    : this.plan.yearly_price;
            }
        },

        /**
         * Descuento anual emitido al seleccionar plan anual
         *
         * @return {Number}
         *
         */
        descuentoAnual: function () {
            // comprobar la cantidad en meses y si esta seleccionado  un plan
            if (this.months == 12 && this.plan) {

                // realizar el descuento del plan anual seleccionado menos (-)
                // el total del valor del plan por año
                const calculo = (this.plan.monthly_price * 12) - this.plan.yearly_price;

                // convertir a float y con dos decimales
                return parseFloat(calculo).toFixed(2);
            } else {
                return 0;
            }
        },
        /**
         * Las unidades adquiridas
         *
         * Es el número de meses en una subscripción mensual
         * y  el número de años  en una subscripción anual
         *
         * @return {Number}
         */
        units: function () {
            return this.months < 12 ? this.months : 1;
        },
        /**
         * Obtiene el importe total sin impuestos con dos decimales
         *
         * @return {Number}
         *
         */
        amountExcludedTax: function () {
            if (!this.plan) return 0;

            // Calculamos el importe sin impuestos
            let amount = this.units * this.price;

            // Si se produce un cambio desde un plan Premium a un plan Pro
            // debe incrementarse el precio por los días restantes hasta la finalización del plan
            if (this.planHasChanged()) {
                amount += this.aditionalAmount();
            }

            return amount.toFixed(2);
        },
        /**
         * Obtiene los impuestos con dos decimales
         *
         * @return {Number}
         *
         */
        tax: function () {
            return (
                (this.amountExcludedTax * (this.plan ? this.plan.tax : 0)) /
                100
            ).toFixed(2);
        },
        /**
         * Obtiene la cantidad total a pagar con impuestos incluidos
         *
         * @return {Number}
         *
         */
        amount: function () {
            return (
                parseFloat(this.amountExcludedTax) + parseFloat(this.tax)
            ).toFixed(2);
        },
    },
});
