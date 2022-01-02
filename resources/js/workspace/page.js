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
        
        page        : null,         // La página actual
        pages       : null,         // El número de páginas del documento
        scale       : 1,            // La escala actual del documento
        maxScale    : 2,            // La máxima escala del documento (al hacer zoom)
        minScale    : 1,            // La mínima escala del documento (al hacer zoom)
        url         : null,         // La dirección URL del documento PDF cargado
        pdf         : null,         // Referencia al documento PDF cargado
        canvas      : null,         // El elemento canvas
        context     : null,         // El contexto del canvas
        viewportI   : [],           // Array para guardar el viewport inicial de la página 
        viewport    : null,         // El viewport

        recordedChunks : [],        // La data que se está grabando

        // Las dimensiones de la caja de firma
        // 6.5 cm x 5.5 cm
        // 5.5 cm = 156         px
        // 6.5 CM = 184.2       px
        placeholder :               // Las dimensiones de un contenedor de firma
             {                      // cuando se muestra a escala unidad
                 width  : 185,
                 height : 155,
                 canvas :
                    {
                        width   : 175,      // La anchura del elemento canvas
                        height  :  85,      // La altura  del elemento canvas
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

        rendering : false,
        fit : false,

        // log canvas moves over canvas for monitoring mouse position, only for tests
        mouse : {
            x:0,
            y:0,
            clientX:0,
            clientY:0,
            canvasLeft:0,
            canvasTop:0,
        },
        positions : {
            clientX : 0,
            clientY : 0,
            movementX : 0,
            movementY : 0,
        },
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

        $this.captureResizeWindowEvent();
    },
    methods: {

        /**
         * Para tracear posicion del mouse respecto al canvas y en documento
         */
        mouseMoveOverCanvasEvent: function (event) {
            const $this = this;

            // Obtiene la posición en el canvas
            var rect = $this.canvas.getBoundingClientRect();
            
            $this.mouse.clientX = event.clientX;
            $this.mouse.clientY = event.clientY;

            $this.mouse.canvasLeft = parseInt(rect.left);
            $this.mouse.canvasTop  = parseInt(rect.top);

            $this.mouse.x = parseInt((event.clientX - rect.left) / $this.scale);        //+1
            $this.mouse.y = parseInt((event.clientY - rect.top)  / $this.scale);

            $this.mouse.x = $this.mouse.x < 1 ? 1 : $this.mouse.x > $this.canvas.width ? $this.canvas.width : $this.mouse.x;
            $this.mouse.y = $this.mouse.y < 1 ? 1 : $this.mouse.y > $this.canvas.height ? $this.canvas.height : $this.mouse.y;
        },

        // Repinta el canvas al finalizar el evento de redimensión del
        // navegador y no constantemente
        captureResizeWindowEvent: function () {
            const $this = this;

            $(window).resize(function () {
                
                if ($this.fit) {
                    $this.rendering = true;
                    // Limpia el canvas
                    //$this.clearCanvas();
                    // Elimina los contenedor de firma que pueda haber
                    $this.clearSignPlaceholders();

                    if (this.resizeTo) {
                        clearTimeout(this.resizeTo);
                    }

                    this.resizeTo = setTimeout(function () {
                        $(this).trigger('resizeEnd');
                    }, 500);
                }
                

            });
            
            $(window).bind('resizeEnd', function () {
                $this.resizeCanvas();
            });
        },

        resizeCanvas: function () {
            this.render();
        },

        /**
         * Va a la página indicada
         * 
         * @param {Number} page     El número de página del documento
         * 
         */
        goPage: function (page) {

            if (this.page === page) {
                this.rendering = false;
                return;
            }
            
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
         */
        zoomPlus: function () {
            if (this.scale == this.maxScale) {
                return;
            }
            if (this.scale + 0.25 > 2) {
                this.scale = 2;
            } else {
                this.scale += 0.25;
            }
            this.render();
        },

        /**
         * Reducir la página
         */
        zoomMinus: function () {
            if (this.scale == this.minScale) {
                return;
            }
            if (this.scale - 0.25 < 1) {
                this.scale = 1;
            } else {
                this.scale -= 0.25;
            }
            this.render();
        },

        /**
         * Ajusta la escala para que se acople la imagen en la pantalla disponible
         */
        zoomFit: function () {
            this.fit = true;

            this.rendering = true;
            //this.clearCanvas();
            this.render();
        },

        /**
         * Ajusta la escala a 1 para que se vel en su tamaño original
         */
        zoomReset: function () {
            this.scale = 1;
            this.fit = false;
            
            this.rendering = true;
            //this.clearCanvas();
            this.render();
        },

        /*
         * Limpia el canvas y lo dimensiona a tamaño 100x100
         */
        clearCanvas: function () {
            this.context.clearRect(0,0, this.canvas.width, this.canvas.height);
            this.canvas.width  = 100;
            this.canvas.height = 100;
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

            /*
             *  largo/horizontal de 6,5 cm      = 184        px
             *  ancho/alto/vertical de 5,5cm    = 156        px
             */

            console.log("Posicion de la firma X(left) x Y(top)")
            console.log(`X-Left => ${sign.x}  Y - Top => ${sign.y}`);

            // Toma la plantilla del marcador, la clona
            const signPlaceholder = document.querySelector('.sign-placeholder.template');
            let placeholder = signPlaceholder.cloneNode(true);
            
            // Elimina la clase plantilla del contenedor de firma clonado
            placeholder.classList.remove('template');

            // Define el color del contenedor de firma
            // A cada firmante se le asigna un color  definidos por la sucesión de clases: [color-0 .. color-9]
            placeholder.classList.add(`color-${sign.signer.id % 10}`);

            const placeholderWidth  = this.placeholder.width  * this.scale;
            const placeholderHeight = this.placeholder.height * this.scale;

            console.log("Ancho x Alto de contenedor de firma");
            console.log(`${placeholderWidth} x ${placeholderHeight}`);

            // Define la anchura y altura del contenedor de firma en función del grado de zoom (escala)
            placeholder.style.width  = `${placeholderWidth}px`;
            placeholder.style.height = `${placeholderHeight}px`;

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

            // Solo muestro el canvas en la caja cuando la escala es mayor o igual a 1
            if (this.scale < 1) {
                placeholder.querySelector('canvas').style.display = 'none';
            } else {
                const canvasWidth  = this.placeholder.canvas.width  * this.scale;
                //const canvasHeight = this.placeholder.canvas.height * this.scale;

                // canvas sizes
                placeholder.querySelector('canvas').style.width  = `${canvasWidth}px`;
                placeholder.querySelector('canvas').style.height = `${this.scale * 150 - 70}px`;

                placeholder.querySelector('canvas').width  = canvasWidth;
                placeholder.querySelector('canvas').height = this.scale * 150 - 70;
            }

            // Completa el contenedor de firma
            placeholder.dataset.signId = sign.id;

            placeholder.querySelector('.sign-placeholder-id').innerHTML = sign.signer.name;
            placeholder.querySelector('.clear-sign-placeholder').dataset.signId = sign.id;

            // Añade el nombre del contenedor de firma (código o id de la firma)
            // Pero en vez del signer metemos el id de la firma
            placeholder.querySelector('.signer-name').innerHTML = sign.id;

            // Añade el evento para limpiar el contenedor de firma al presionar el botón de "papelera"
            placeholder.querySelector('.clear-sign-placeholder').addEventListener('click', this.clearSignPlaceHolder);

            // Define el contenedor de firma (canvas)
            let signCanvas = placeholder.querySelector('.sign');
       
            signCanvas.dataset.id = sign.id;
          
            // Si exite ya una firma la muestra
            if (sign.sign) {

                const myfn = async () => {
                    // Devuelvo la imagen escalada en su tamanno original 100 %.
                    return await this.resizeImage(
                        sign.sign,
                        this.placeholder.canvas.width  * this.scale,
                        this.scale * 150 - 70,
                    );
                }

                // pinto esta imagen en todo el canvas de firma escalada segun este canvas
                myfn().then(scaleImage => {
                    var image = new Image();
                    image.src = scaleImage;

                    image.onload = () => {
                        signCanvas.getContext('2d').drawImage( image, 0, 0);
                    };
                    
                });

                /*

                // Carga la imagen de la firma
                let image = new Image();
                image.src = sign.sign;

                // Cuando la imagen ha sido cargada, se dibuja sobre el canvas
                image.onload = () => {
                    // genero un canvas temporal para ajustar ese imagen a las dimensiones de la caja
                    var canvasTmp = document.createElement('canvas');
                    var ctxTmp    = canvasTmp.getContext('2d');

                    //canvasTmp.width  = this.placeholder.canvas.width  * this.scale;
                    //canvasTmp.height = this.scale * 150 - 70;

                    // dimensiones a escala 1
                    canvasTmp.width  = this.placeholder.canvas.width;
                    canvasTmp.height = this.placeholder.canvas.height;

                    // la pinto en el canvas temporal ajustado a las dimensiones de la caja
                    ctxTmp.drawImage(
                        image,
                        0,
                        0,
                        this.placeholder.canvas.width  * this.scale,
                        this.scale * 150 - 70,
                    );

                    // ahora tomo esa imagen del canvas temporal y la copio en el canvas minuatura

                    // Tomo el imageData del canvas temporal
                    var imageDataTmp = ctxTmp.getImageData(0, 0, canvasTmp.width, canvasTmp.height);

                    signCanvas
                        .getContext('2d')
                        .putImageData(imageDataTmp, 0, 0);
                }
                */
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

            // Posiciono la caja de firma según en las coordenadas correctas no importa la escala
            const placeholderLeft = $this.canvas.offsetLeft + (sign.x * this.scale);

            // TODO: REVISAR PORQUE HAY QUE RESTAR ESE NUMERO PARA QUE LA FIRMA COINCIDA CON EL LUGAR ESPECIFICADO EN LA CONFIGURACION
            const placeholderTop  = $this.canvas.offsetTop  + (sign.y * this.scale) - 24;

            console.log("OFFSETS DEL CANVAS");
            console.log(
                `Left => ${$this.canvas.offsetLeft} - Top  => ${$this.canvas.offsetTop}`
            );

            console.log("Posicion del marcador de firma");
            console.log(
                `Left => ${placeholderLeft} - Top  => ${placeholderTop}`
            );

            // Posiciona el marcador y lo hace visible
            placeholder.style.left    = `${placeholderLeft}px`;
            placeholder.style.top     = `${placeholderTop}px`;
            placeholder.style.display = 'block';
            
            // Añade la firma a la lista de firmas
            document.getElementById('signs').appendChild(placeholder);
        },

        /**
         * Redimensiona una imagen
         * 
         * @param {String} data             Los datos de la imagen en formato Base64
         * @param {Number} wantedWidth      El ancho de la imagen requerido en píxeles
         * @param {Number} wantedHeight     El alto de la imagen requerido en píxeles
         *                                  Si no se especifica, se calcula un alto proporcional
         *                                  al ancho de la imagne original
         * 
         * @return {Promise}                Una promesa con la imagen redimensionada en Base64
         *
         */
         resizeImage: function (data, wantedWidth, wantedHeight) {

            return new Promise(resolve => {

                // Creamos una nueva Imagen
                let image = new Image();

                // y cargamos a la imagen a redimensionar
                image.src = data;

                // Cuando la imagen está cargada
                image.onload = () =>
                    {        
                        // Si no se ha proporcionado una altura, la obtiene en relación a la anchura
                        if (typeof wantedHeight === 'undefined') {
                            wantedHeight = wantedWidth * image.height / image.width
                        }

                        // Creamos un nuevo elemento canvas para efectuar la operación
                        let canvas = document.createElement('canvas');
                        let ctx    = canvas.getContext('2d');

                        // y lo dimensionamos con las dimensiones requeridas
                        canvas.width  = wantedWidth;
                        canvas.height = wantedHeight;

                        // Dibujamos la imagen
                        ctx.drawImage(image, 0, 0, wantedWidth, wantedHeight);

                        console.info(`[OK] La imagen de firma ha sido redimensionada a ${wantedWidth}x${wantedHeight}`);

                        // Devolvemos la imagen redimensionada
                        resolve(canvas.toDataURL());
                    };

            });
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
                 */
                onEnd: function () {
                    // Se guarda la firma en base 64n ya debidamente escalada (a escala 1:1)
                    sign.sign = signCanvas.toDataURL();

                    const myfn = async () => {
                        // Devuelvo la imagen escalada en su tamanno original 100 %.
                        return await $this.resizeImage(
                            sign.sign,
                            $this.placeholder.canvas.width,
                            150 - 70,
                        );
                    }
    
                    // pinto esta imagen en todo el canvas de firma escalada segun este canvas
                    myfn().then(scaleImage => {
                        sign.sign = scaleImage;
                    });
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
                $this.viewport = page.getViewport({ scale: 1 });

                if (!$this.viewportI[$this.page]) {
                    $this.viewportI[$this.page] = $this.viewport;
                }

                const docWidth = parseInt($this.$refs.document.clientWidth);

                if ($this.fit) {
                    console.log("Recalculando escala ...");
                    $this.scale = Math.round( (docWidth / $this.viewportI[$this.page].width) * 100, 2) / 100; 
                    console.log('Escala calculada');
                    console.log(`Math.round( (${docWidth} / ${$this.viewportI[$this.page].width}) * 100, 2) / 100`);
                } else {
                    //console.log("Escala es 1");
                    //$this.scale = 1;
                }

                //console.log($this.scale)
                //console.log('% Escalado');
                console.log(Math.round( $this.scale * 100) + ' %')

                $this.viewport = page.getViewport({ scale: $this.scale, });

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

                var renderTask = page.render(renderContext);
                renderTask.promise.then(function () {
                    $this.rendering = false;
                });

                $this.drawSignPlaceholdersInPage($this.page);
            });
        },

        /*
         * Guardo las firmas y capturas realizadas en el proceso
         */
        saveSignsAndCaptures: function () {
            const $this = this;

            // Obtiene las firmas realizadas
            $this.signed = $this.signs.filter(sign => {
                return sign.sign != null;
            });

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
         * Guarda el documento firmado
         */
        saveSignedDocument: function () {
            const $this = this;

            // Verificar si se está grabando, en caso de que se esté haciendo captura de pantalla
            if ($this.recording) {

                let stop = async function() {
                    // Detiene el cronómetro
                    clearInterval($this.timer);

                    // Detiene la grabación
                    await $this.mediaRecorder.stop();
                    
                    // Detiene el proceso de captura de pantalla
                    const videoElement = document.getElementById('video-screen');
                    const track        = videoElement.srcObject.getVideoTracks().pop();
                    track.stop();

                    // Oculto el preview de la captura de pantalla
                    $this.$refs.videoScreen.style.display = 'none';
                    // No se puede firmar si no se está grabando
                    $this.canSign = false;
                    $this.recording = false;

                    // Espero 1 segundo para salvar los dato para esperar que se guarde la captura de pantalla
                    // que he finalizado
                    setTimeout( ()=>{
                        $this.saveSignsAndCaptures();
                    }, 1000);

                };

                stop();
            } else {
                $this.saveSignsAndCaptures();
            }
        },

        /**
         * Finaliza y sale de la pantalla
         * Redirigiendo a la vista principal del espacio de trabajo (workspace)
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
            $this.recordedChunks = [];

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
                                $this.recordedChunks.push(event.data);        
                            }
                        });

                        $this.mediaRecorder.addEventListener('stop', async function () {
                            
                            // Obtiene un id único para el archivo de video creado
                            let id        = Math.random().toString(36).substr(2, 9);
                            let filename  = `${id}.mp4`;
                            let filetype  = 'video/mp4';
                        
                            // Convierte los datos de video en base 64 en un objeto blob
                            let blob       = new Blob($this.recordedChunks)
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
                        
                        // Elimino todos los mensajes toast que puedan estar mostrándose
                        toastr.clear();

                        // Comienzo a grabar
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
            return this.time > 0 ? moment.utc(this.time * 1000).format('mm:ss'):'';
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