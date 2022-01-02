/**
 * Firma de la página de un documento
 * 
 * @author    javieru <javi@gestoy.com>
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
    data: {
        // La información del documento
        document    : document.getElementById('document').dataset,
        
        page        : null,         // La paǵina actual
        pages       : null,         // El número de páginas del documento
        scale       : 1,            // La escala actual del documento
        maxScale    : 2,            // La máxima escala del documento (al hacer zoom)
        minScale    : 1,            // La mínima escala del documento (al hacer zoom)
        url         : null,         // La dirección URL del documento PDF cargado
        pdf         : null,         // Referencia al documento PDF cargado
        canvas      : null,         // El elemento canvas
        context     : null,         // El contexto del canvas
        viewport    : null,         // El viewport

        placeholder :               // Las dimensiones de un contenedor de firma
             {                      // cuando se muestra a escala unidad
                 width  : 250,
                 height : 150,
                 canvas :
                    {
                        width   : 240,      // La anchura del elemento canvas
                        height  :  80,      // La altura  del elemento canvas
                    }
             },

        signs       : null,           // La lista de firmas del documento
        signed      : null,           // La lista de firmas realizadas

        position    :               // La posición del usuario firmante datum WGS84
            {
                latitude : null,
                longitude: null
            },
        
        visit : null,               // La visita del usuario firmante

        messages: document.getElementById('messages').dataset,  // Los mensajes
        
        recording       : false,                                // true si está grabando video, false en caso contrario
        mediaRecorder   : null,                                 // El mediarecorder
                                                    
        timer   : null,                                         // El cronómetro
        time    : 0,                                            // El tiempo en segundos de la grabación
        maxTime : document.getElementById('capture')
                    .dataset.maxRecordTime,                     // El máximo tiempo que se puede grabar en segundos

        capture : null,                                         // La captura de la pantalla mientras se realiza la grabación
        captures: [],                                           // Arreglo con las capturas realizadas en el proceso
        canSign : true,                                         // Determina si se puede firmar sobre las cajas de firma o no
                                                                // cuando no se ha solicitado captura de pantalla es true
        desktop : null,                                         // Si el firmante está en una pc
    },
    
    /**
     * Cuando tenemos la instancia montada
     *
     */
    mounted: function () {
 
        const $this = this;

        // Obtenemos si el documento debe validarse con captura de pantalla
        $this.canSign = !document.getElementById('capture')
            .dataset.mustBeValidatedByScreenCapture;

        // Intentamos obtener la geolocalización del firmante
        $this.getCurrentPosition()
            .then(() => {
                console.info(`[INFO] Se ha obtenido la posición del firmante con éxito`);
            })
            .catch(() => {
                console.info(`[INFO] No se ha podido obtener la posición del firmante`);
            });
        
        // Obtenemos el registro de la visita del usuario a la página
        $this.visit = JSON.parse(document.getElementById('visit').dataset.visit);

        // Obtenemos si estamos en desktop
        $this.desktop = JSON.parse(document.getElementById('data').dataset.desktop);

        // Obtiene la URL de acceso al documento PDF a firmar
        $this.url  = $this.document.pdf;

        // Obtiene la página del documento a firmar
        $this.page = parseInt($this.document.page);

        /**
         * Carga asíncrona del documento PDF
         */
        const loadingTask = pdfjsLib.getDocument($this.url);

        loadingTask.promise.then(function(pdf) {

            // Fija el número de páginas del documento
            $this.pages = pdf.numPages;
            $this.pdf   = pdf;
        
            /**
             * Espera a la obtención de las firmas existentes
             * ante de cargar la primera página del documento
             */    
            $this.loadSigns()
                .then(() => {
                    // Carga la página del documento
                    $this.render();
                })
                .catch(error => {
                    console.error(error);
                });
        });
    },
    methods: {
        /**
         * Va a la página indicada
         * 
         * @param {Number} page     El número de página del documento
         * 
         */
        goPage: function (page) {
            
            // Obtiene el número de página
            page = parseInt(page);

            // Control del valor respecto al número de páginas del documento
            if (page < 1) {
                page = 1;
            } else if (page > this.pages) {
                page = this.pages;
            }

            this.page = page;
            this.render(); 
        },
        /**
         * La página siguiente del documento
         * 
         */
        pageNext: function () {
            if (this.page == this.pages) return;
            ++this.page;
            this.render();
        },
        /**
         * La página anterior del documento
         * 
         */
        pagePrev: function () {
            if (this.page == 1) return;
            --this.page;
            this.render();
        },
        /**
         * Ampliar la paǵina
         * 
         */
        zoomPlus: function () {
            if (this.scale == this.maxScale) return;
            this.scale*=2;
            this.render();
        },
        /**
         * Reducir la página
         * 
         */
        zoomMinus: function () {
            if (this.scale == this.minScale) return;
            this.scale/=2;
            this.render();
        },
        /**
         * Carga las firmas previamente guardadas del documento
         * 
         * @return {Promise}    Una promesa
         */
        loadSigns: function () {

            const $this = this;

            // La url de la petición para obtener las firmas
            const requestSigns = $this.document.signs;

            return new Promise((resolve, reject) => {
                axios.post(requestSigns)
                    .then(response => {
                        // Obtiene las firmas
                        let signs = response.data;
                        
                        signs.forEach(sign => {
                            // Establece los datos del firmante
                            sign.signer =
                                {
                                    id      : sign.signer_id,   // El id del firmante
                                    name    : sign.signer       // El nombre, email o teléfono del firmante
                                                                // según los datos que se propocionaron
                                };
                            // Estable el id de la firma
                            sign.id     = sign.code;
                        });

                        $this.signs = signs;

                        resolve(signs);
                    })
                    .catch (error => {
                        reject(error);
                    });
            });
        },
        /**
         * Dibuja todos los contenedores de firma registrados en una página del documento
         * 
         * @param {Number} page     El número de página
         */
        drawSignPlaceholdersInPage: function (page) {
            
            // Elimina los contenedor de firma que pueda haber
            this.clearSignPlaceholders();

            // Coloca los contenedores de firma marcados en la página
            this.signs.forEach(sign => {
                if (sign.page == page) {
                    this.drawSignPlaceholder(sign);
                }
            });
        },
        /**
         * Elimina todos los contenedores de firma del documento
         *
         */
        clearSignPlaceholders: function () {
            document.getElementById('signs').innerHTML = '';
        },
        /**
         * Dibuja un contenedor de firma
         * 
         * @param {Sign} sign   Una firma 
         */
        drawSignPlaceholder: function (sign) {

            const $this = this;

            // Toma la plantilla del marcador, la clona
            const signPlaceholder = document.querySelector('.sign-placeholder.template');
            let placeholder = signPlaceholder.cloneNode(true);
            
            // Elimina la clase plantilla del contenedor de firma clonado
            placeholder.classList.remove('template');

            // Define el color del contenedor de firma
            // A cada firmante se le asigna un color  definidos por la sucesión de clases: [color-0 .. color-9]
            placeholder.classList.add(`color-${sign.signer.id % 10}`);

            // Define la anchura y altura del contenedor de firma en función del grado de zoom (escala)
            placeholder.style.width  = `${this.placeholder.width  * this.scale}px`;
            placeholder.style.height = `${this.placeholder.height * this.scale}px`;

            //
            // El canvas también debe ser redimensionado
            //
            // Ecuaciones de transformación paramétricas:
            // 
            // Wσ = W1  · σ
            // Hσ = 150 · σ - 70
            //
            // Siendo:
            //  σ  la escala actual del documento, σ = 1,2,...
            //  W1 es el ancho del elemento de firma a escala σ = 1
            placeholder.querySelector('canvas').style.width  = `${this.placeholder.canvas.width  * this.scale}px`;
            placeholder.querySelector('canvas').style.height = `${this.scale * 150 - 70}px`;

            placeholder.querySelector('canvas').width  = this.placeholder.canvas.width  * this.scale;
            placeholder.querySelector('canvas').height = this.placeholder.canvas.height * this.scale;

            // Completa el contenedor de firma
            placeholder.dataset.signId = sign.id;

            placeholder.querySelector('.sign-placeholder-id').innerHTML = sign.id;
            placeholder.querySelector('.clear-sign-placeholder').dataset.signId = sign.id;

            // Añade el evento para limpiar el contenedor de firma al presionar el botón de "papelera"
            placeholder.querySelector('.clear-sign-placeholder').addEventListener('click', this.clearSignPlaceHolder);

            // Define el contenedor de firma (canvas)
            let signCanvas = placeholder.querySelector('.sign');
       
            signCanvas.dataset.id = sign.id;
          
            // Si exite ya una firma la muestra
            if (sign.sign) {
                
                // Carga la imagen de la firma
                let image = new Image();
                image.src = sign.sign;

                // Cuando la imagen ha sido cargada, se dibuja sobre el canvas
                image.onload = () => {
                    signCanvas
                        .getContext('2d')
                        .drawImage(image, 0, 0, image.width * $this.scale, image.height * $this.scale);
                }
            }

            // Activa SignaturePad sobre el contenedor (canvas) de firma si se puede firmar sobre el documento
            // Cuando se debe grabar pantalla en el proceso, primero se debe iniciar por el firmante
            if ($this.canSign) {
                // Elimino la acción de alertar cuando quiero firmar
                removeEventListener('mousedown', placeholder.querySelector('canvas'));
                // Habilito poder firmar sobre el documento
                $this.enableSignPad(sign, signCanvas);
            } else {
                // Cuando no se puede firmar es porque debe activar la grabación de pantalla
                // por lo que se muestra al firmante lo que debe hacer antes de firmar
                placeholder.querySelector('canvas').addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    toastr.warning($this.messages.mustInitScreenCaptureRecording);
                });
            }

            // Posiciona el marcador y lo hace visible
            placeholder.style.top     = `${sign.y * this.scale + this.canvas.offsetTop}px`;
            placeholder.style.left    = `${sign.x * this.scale + this.canvas.offsetLeft}px`;
            placeholder.style.display = 'block';
            
            // Añade la firma a la lista de firmas
            document.getElementById('signs').appendChild(placeholder);
        },

        /**
         * Habilita la caja de firma para poder firmarse
         *
         */
        enableSignPad: function (sign, signCanvas) {
            const $this = this;
            sign.pad = new SignaturePad(signCanvas, {
                /**
                 * Cuando se termina de trazar la firma
                 *
                 */
                onEnd: function () {

                    // Obtiene el contenido del Canvas en una nueva imagen
                    let image = new Image();
                    image.src = signCanvas.toDataURL();

                    // Cuando la imagen está creada
                    image.onload = () => {

                        // Crea un nuevo elemento canvas
                        let newCanvas = document.createElement('canvas');

                        // y dibuja la imagen debidamente redimensionada a su nivel de escala (zoom)
                        newCanvas
                            .getContext('2d')
                            .drawImage(image, 0, 0, image.width / $this.scale, image.height / $this.scale);

                        // Se guarda la firma en base 64n ya debidamente escalada (a escala 1:1)
                        sign.sign = newCanvas.toDataURL();

                    }
                }
            });
        },

        /**
         * Limpia un contenedor de firma
         * 
         * Ocurre cuando se presiona el botón "papelera" del contenedor
         * 
         * @param {Event} event
         */
        clearSignPlaceHolder: function (event) {
            
            // Obtiene el id del contenedor de firma a limpiar
            let signId = event.currentTarget.dataset.signId;

            // Encuentra el contedor de firma y lo limpia
            const found = this.signs.find(sign => sign.id === signId);

            if (found.pad && this.canSign) {
                found.sign = null;
                found.pad.clear();
            }
        },
        /**
         * Obtiene la posición actual
         * 
         * @returns {Promise}       Una promesa con la posición calculada o un error
         */
        getCurrentPosition: function() {
            
            const $this = this;

            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        $this.position.latitude  = pos.coords.latitude;
                        $this.position.longitude = pos.coords.longitude;
                        resolve($this.position);
                    },
                    error => {
                        reject(error);
                    }, 
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            });
        },
        /**
         * Obtiene la página del documento PDF
         *
         */
        render: function () {

            const $this = this;

            $this.pdf.getPage($this.page).then(function(page) {
                
                /**
                 * Determina el viewport del documento
                 */
                $this.viewport = page.getViewport({ scale: $this.scale });

                /**
                 * Prepara el canvas utilizando las dimensiones del viewport obtenido
                 */
                $this.canvas  = document.getElementById('document');
                $this.context = $this.canvas.getContext('2d');

                /**
                 * Dimensiona el elemento canvas en función del viewport
                 */
                $this.canvas.height = $this.viewport.height;
                $this.canvas.width  = $this.viewport.width;
               
                /**
                 * Renderiza la página
                 */
                const renderContext =
                    {
                        canvasContext: $this.context,
                        viewport     : $this.viewport,
                    };

                page.render(renderContext);

                $this.drawSignPlaceholdersInPage($this.page);
            });
        },
        /**
         * Guarda el documento firmado
         *
         */
        saveSignedDocument: function () {
            
            const $this = this;

            // Verificar si se está grabando
            if ($this.recording) {
                // Oculto el preview de la captura de pantalla
                $this.$refs.videoScreen.style.display = 'none';
                // No se puede firmar si no se está grabando
                $this.canSign = false;
                $this.recording = false;
                $this.stopTimer();
            }

            // Obtiene las firmas realizadas
            $this.signed = $this.signs.filter(sign => {
                return sign.sign != null;
            });

            // Si no se firmado
            if (!$this.signed) {
                toastr.error(`${document.getElementById('messages').dataset.cannotFinish} !!`);
                return;
            }

            // Obtiene la url de la solicitud para guardar las firmas del documento
            const request = $this.document.save;

            // La data que se envía
            const data =
                {
                    signs       : $this.signed,     // Se envían sólo las firmas realizadas (firmadas)
                    position    : $this.position,   // La posición datum WGS84
                    visit       : $this.visit,      // La información de la visita realizada a la página
                    captures    : $this.captures,   // Las capturas de la pantalla si se han proporcionado
                };

            // Inicia animación
            HoldOn.open({theme: 'sk-circle'});

            axios.post(request, data)
            .then(() => {
                HoldOn.close();
                // Muestra una modal indicando que el documento ha sido firmado con éxito
                try {
                    $this.$bvModal.show('document-signed');
                } catch (error) {
                    // Por ejemplo cuando estoy offline
                    location.href = this.document.redirectToWorkspaceHome;
                }
                
            })
            .catch(error => {
                HoldOn.close();
                console.error(error);
            });
        },
        /**
         * Finaliza y sale de la pantalla
         * Redirigiendo a la vista principal del espacio de trabajo (workspace)
         *
         */
        exit: function() {
            location.href = this.document.redirectToWorkspaceHome;
        },
        /**
         * Inicia o detiene la grabación de video
         *
         */
        recordCapture: function () {
            // Inicia la grabación o la detiene
            this.recording = !this.recording;

            // Al iniciar la grabación
            if (this.recording) {
                toastr.success(this.messages.recording);
                this.startRecording();
            } else {
                this.stopTimer();
            }
        },
        /**
         * Inicia la grabación de captura de pantalla
         *
         */
        startRecording: function () {

            const $this = this;

            const options = { audio: true, video: true };
            const recordedChunks = [];

            navigator.mediaDevices.
                getDisplayMedia(options)
                .catch(error => {
                    // Si el firmante ha cancelado compartir su pantalla
                    if (error.name === 'NotAllowedError') {
                        // Inicia la grabación o la detiene
                        this.recording = false;
                    }
                }).then(stream => {
                    // Si el usuario no ha cancelado la compartición de pantalla en el navegador
                    if (stream !== undefined)  {
                        // Se pone a cero el tiempo de grabación
                        $this.time  = 0;

                        // Se inicia el cronómetro que se actualiza cada segundo
                        $this.timer = setInterval(this.updateTimer, 1000);

                        $this.mediaRecorder = new MediaRecorder(stream, options); 

                        // Muestra la captura de video en tiempo real
                        const videoElement = document.getElementById('video-screen');
                        videoElement.srcObject = stream;

                        $this.mediaRecorder.addEventListener('dataavailable', function (event) {
                            if (event.data.size > 0) {
                                recordedChunks.push(event.data);        
                            }
                        });

                        $this.mediaRecorder.addEventListener('stop', async function () {
                            
                            // Obtiene un id único para el archivo de video creado
                            let id        = Math.random().toString(36).substr(2, 9);
                            let filename  = `${id}.mp4`;
                            let filetype  = 'video/mp4';
                        
                            // Convierte los datos de video en base 64 en un objeto blob
                            let blob       = new Blob(recordedChunks)
                            let filesize   = Math.round(blob.size / 1024);
                            const duration = moment.utc($this.time * 1000).format('mm:ss')

                            const file = await $this.convertBlobToBase64(blob);

                            $this.capture =
                                {
                                    id       : id,
                                    filename : filename,
                                    type     : filetype,
                                    size     : filesize,
                                    file     : file,
                                    video    : URL.createObjectURL(blob),
                                    playing  : false,
                                    duration : duration,
                                };

                            $this.captures.push( $this.capture );
                            $this.capture = null;

                            // Oculto el preview de la captura de pantalla
                            $this.$refs.videoScreen.style.display = 'none';
                            
                            // No se puede firmar si no se está grabando
                            $this.canSign = false;
                            $this.drawSignPlaceholdersInPage($this.page);

                            toastr.success($this.messages.captureRecordSuccess);

                        });
                    
                        // Muestro el preview de la captura de pantalla
                        $this.$refs.videoScreen.style.display = 'block';

                        // Habilito poder firmar sobre las cajas de firma
                        $this.canSign = true;
                        $this.drawSignPlaceholdersInPage($this.page);

                        $this.mediaRecorder.start();
                    }
                    
                });
        },
        /**
         * Convierte un objeto Blob en base 64
         * 
         * @param {Blob} blob       Un objeto Blob 
         * 
         * @return {Promise}        Una promesa con el objeto blob convertido a base 64
         */
        convertBlobToBase64: function (blob) {
            
            const reader = new FileReader();
            reader.readAsDataURL(blob);

            return new Promise((resolve, reject) => {
                
                reader.onloadend = () => {
                    resolve(reader.result);
                };

                reader.onerror = error => {
                    reject(error);
                }
            });
        },
        /**
         * Obtiene la posición actual
         * 
         * @returns {Promise}       Una promesa con la posición calculada o un error
         */
        getCurrentPosition: function() {
            
            const $this = this;

            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        $this.position.latitude  = pos.coords.latitude;
                        $this.position.longitude = pos.coords.longitude;
                        resolve($this.position);
                    },
                    error => {
                        reject(error);
                    }, 
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            });
        },
        /**
         * Actualiza el cronómetro
         *
         */
        updateTimer: function () {
            ++this.time;

            // Si se supera el tiempo máximo de grabación de video se detiene el cronómetro
            if (this.time >= this.maxTime) {
                this.stopTimer();
            }
        },
        /**
         * Detiene el cronómetro
         * 
         */
        stopTimer: async function () {
            // Detiene el cronómetro
            clearInterval(this.timer);

            // Detiene la grabación
            await this.mediaRecorder.stop();
            
            // Detiene el proceso de captura de pantalla
            const videoElement = document.getElementById('video-screen');
            const track        = videoElement.srcObject.getVideoTracks().pop();

            track.stop();
        },
    },
    computed : {
        /**
         * Muestra la representación del cronómetro en el formato mm:ss
         *
         * @return {String}
         * 
         */
        showTimer: function () {
            return moment.utc(this.time * 1000).format('mm:ss');
        },
        /**
         * Comprueba si todas las firmas han sido realizadas o no
         * 
         * @return {Boolean}
         * 
         */
        allSignaturesHaveBeenMade: function() {
            // Si ya se han cargado las firmas
            if (this.signs) {
                // Obtiene las firmas realizadas
                const signed = this.signs.filter(sign => sign.sign != null);
                // Comprueba las firmas realizadas con las que deben ser realizadas
                return signed.length == this.signs.length;
            }
            else
            return false;
        },
    },
    filters: {
        /**
         * Redondea un valor al entero más próximo
         * 
         * @param {Number} value  
         * 
         * @return {Number}
         * 
         */
        int: function (value) {
            return Math.round(value);
        }
    }
});