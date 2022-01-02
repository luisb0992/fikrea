/**
 * Paǵina de inicio del dashboard del usuario
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
    methods: {

        /**
         * Marca la notificación como léida
         *
         * @param {Notification} notification   El id de la notificación
         * @param {redirect} redirect           Valor boolean opcinal donde dira si el usuario
         *                                      es redireccionado o no (true o false)
         */
        read : function (notification, redirect = false) {

            // boton de "ver mas"
            const btn = document.getElementById(`btnUrl-${notification}`);

            // Obtiene la url para marcar la notificación como leída
            const readNotificationRequest = document.getElementById('requests').dataset.readNotification;

            // desactivar el boton de "ver mas"
            btn.disabled = true;

            // cerrar el modal
            if (!redirect) {
                this.$bvModal.hide(`delete-notification-${notification}`);

                // Oculta la notificación
                document.querySelector(`.notification[data-id="${notification}"]`).style.display = 'none';
            }

            // Inicia la animación
            if (redirect) {
                HoldOn.open({theme: 'sk-circle'});
            }

            // Marca la notificación como leída
            Axios.post(readNotificationRequest, {
                id: notification,
            })
            .then (() => {
                console.log(`La notificación #${notification} ha sido leída`);

                // Actualiza el contador de notificaciones
                const notificationCounter = document.querySelector('.notification-counter');
                const menuNotificationCounter = document.querySelector('#menu-notification-total');

                // Obtener el total de notificaciones
                const total = parseInt(notificationCounter.innerHTML)-1;

                // en la barra de notificaciones
                notificationCounter.innerHTML = total;

                // n el menu (sidebar)
                menuNotificationCounter.textContent = total;

                // Si es una redireccion
                if (redirect) {

                    // Toma la url y redireccioa al usuario
                    const redirectUrl = document.getElementById(`redirectUrl-${notification}`).dataset.url;
                    location.href = redirectUrl;

                    // Oculta la animación
                    HoldOn.close();
                }
            })
            .catch(error => {

                // activar el boton de "ver mas"
                btn.disabled = false;

                // Oculta la animación
                HoldOn.close();

                console.error(error);
            })
        }
    }
});