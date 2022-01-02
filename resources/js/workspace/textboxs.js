/**
 * Completamiento de las cajas de textos configuradas en el docuento
 * para el firmante
 * 
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

// ir al inicio de la página siempre al cargar
if (history.scrollRestoration) {
    history.scrollRestoration = 'manual';
} else {
    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }
}

// regla para restricciones de caja
const RULEBOX = {
    minLength: 1,
    maxLength: 0,
    numbers:   true,
    letters:   true,
    specials:  true,
};

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
        data: {
            // La información del documento
            //document    : document.getElementById('document').dataset,

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



            textboxs    : null,           // La lista de cajas de textos del documento
            signed      : null,           // La lista de cajas completadas

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

            // Los mensajes de la aplicación
            message :
                {
                    fileIsNotImage : document.getElementById('messages').dataset.fileIsNotImage,
                    processDocumentText: document.getElementById('messages').dataset.processDocumentText,
                    processDocumentTitle: document.getElementById('messages').dataset.processDocumentTitle,
                },
            // Las solicitudes
            request :
                {
                    saveDocument    : document.getElementById('pdf').dataset.request,
                },
            // array para guardar info relacionada con cada página del documento
            pagesData: [],

            indexBox    : -1,            // Indice de la caja de texto 'box' en el array de cajas 'textboxs'

            // Definición de una caja de texto o caja seleccionada
            box         : null,
            // Definición de las reglas para los diferentes tipos de cajas de textos
            rules       : [
                // Posición 0 : Tipo de caja 1 - iniciales
                {
                    minLength: 2,
                    maxLength: 4,
                    numbers:   false,
                    letters:   true,
                    specials:  false,
                },
                // Posición 1 : Tipo de caja 2 - nombre completo
                {
                    minLength: 5,
                    maxLength: 30,
                    numbers:   false,
                    letters:   true,
                    specials:  false,
                },
                // Posición 2 : Tipo de caja 3 - número de identificación : sin restricciones
                {...RULEBOX},
                // Posición 3 : Tipo de caja 4 - texto libre : sin restricciones
                {...RULEBOX},
                // Posición 4 : Tipo de caja 5 - casilla de verificación : sin restricciones (porque es un check)
                {...RULEBOX},
                // Posición 5 : Tipo de caja 6 - lista de opciones : sin restricciones (porque es un select)
                {...RULEBOX},
            ],
                
            selectedBox : false,        // Para indicar cuándo se tiene seleccionada una caja
            langs      : null,          // Textos traducidos 

            // Las dimensiones de la caja de texto
            // 6.5 cm x 3.5 cm
            // 3.5 cm = 100         px
            // 6.5 cm = 184.2       px
            textbox :              // Las dimensiones de un contenedor de firma
                {                  // cuando se muestra a escala unidad
                    width  : 185,
                    height : 100,
                    box :
                    {
                        width   : 175,      // La anchura del elemento canvas
                        height  :  30,      // La altura  del elemento canvas
                    }
                },


    },
     
        /**
         * Cuando tenemos la instancia montada
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

            // La url del documento PDF
            $this.url = document.getElementById('pdf').dataset.pdf;

            // Carga los textos traducidos
            $this.langs = JSON.parse(document.getElementById('langs').dataset.langs);

            // Obtiene las miniaturas del documento
            $this.getThumbs();

            /**
             * Carga asíncrona del documento PDF
             */
            const loadingTask = pdfjsLib.getDocument($this.url);

            loadingTask.promise.then(function(pdf) {

                // Fija el número de páginas del documento

                // Fija el número de páginas del documento
                $this.page  = 1;
                $this.pages = pdf.numPages;
                $this.pdf   = pdf;
                
                /**
                 * Espera a la obtención de las cajas existentes
                 * ante de cargar la primera página del documento
                 */
                $this.loadBoxs()
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

        /**
         * Obtiene las miniaturas del documento PDF
         * 
         */
        getThumbs: function () {

            const $this = this;
            
            // la url del pdf que se previsualiza
            // console.log($this.url);
            
            pdfjsLib.getDocument($this.url)
                .promise.then(pdf => {

                    // Mensaje que indica que el documento se está procesando
                    toastr.warning($this.message.processDocumentText, $this.message.processDocumentTitle, {
                        progressBar: true,
                        // Barra de progreso orientativa en proporción al número de páginas del documento
                        timeOut    : Math.max(3000, pdf.numPages * 200),
                    });

                    // La lista de páginas que posee el documento a firmar en un array
                    var pages = [];

                    while (pages.length < pdf.numPages) {
                        pages.push(pages.length + 1);
                    }

                    return Promise.all(pages.map(num => {

                        // establezco la config de cada página
                        $this.pagesData[num] = {
                            randomColor :   null,       // Color de las cajas de firma del creador
                            signerRColor:   null,       // Color de las cajas de firma de los firmantes
                            imageData   :   null,       // ImageData del canvas para mejorar rendimiento
                        }
                        
                        // Crea un elemento para cada una de las páginas
                        // clonando la plantilla establecida
                        let page = document.querySelector('.page.template').cloneNode(true);

                        // Coloca el número de página
                        page.classList.remove('template');
                        page.dataset.page = num;
                        page.querySelector('.page-number').innerHTML = num;

                        // Añade el elemento de página creado al conjunto de páginas
                        document.getElementById('pagesPreview').appendChild(page);

                        // Añade el enlace al preview de la paǵina a configurar
                        page.addEventListener('click', () => {
                            $this.rendering = true;
                            $this.goPage(num);
                        });

                        // Genero un color diferente para las firmas en cada página para el firmante
                        // y para los firmantes
                        if (!$this.pagesData[num].randomColor) {
                            $this.pagesData[num].randomColor  = Math.floor(Math.random()*16777215).toString(16);
                            $this.pagesData[num].signerRColor = Math.floor(Math.random()*16777215).toString(16);
                        }
                  
                        // Construye un canvas en el elemento de página
                        return pdf.getPage(num)
                            .then(page => $this.makeThumb(page))
                            .then(canvas => {
                                const ctx = canvas.getContext("2d");

                                // Guardo la imagen en minuatura para que a la hora de refrescar no volver a 
                                // obtener toda esta info del canvas
                                if (!$this.pagesData[num].imageData) {
                                    $this.pagesData[num].imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                }

                                // Oculto la imagen pre-loading
                                page.querySelector('.pre-loading-img').style.display = 'none';
                                page.prepend(canvas);
                            });
                }));
            
            }).catch(error => {
                console.error(error);
            });
        },

        /**
         * Muestra la miniatura de la página dada
         * 
         * @param {Number} page     Un número de página
         */
        makeThumb: function(page) {
            const viewport = page.getViewport({ scale: 1 });
        
            const canvas  = document.createElement('canvas');
            const context = canvas.getContext('2d');

            canvas.width  = viewport.width;
            canvas.height = viewport.height;

            return page.render({
                canvasContext: context, 
                viewport     : viewport
            }).promise.then(() => {
                return canvas;
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
         * Carga las cajas previamente guardadas del documento para el firmante
         * 
         * @return {Promise}    Una promesa
         */
        loadBoxs: function () {

            const $this = this;

            // La url de la petición para obtener las firmas
            const requestBoxs = document.getElementById('pdf').dataset.boxs;

            return new Promise((resolve, reject) => {
                axios.post(requestBoxs)
                    .then(response => {
                        // Obtiene las firmas
                        let boxs = response.data;
                        
                        boxs.forEach(box => {
                            // Establece los datos del firmante
                            box.signer =
                                {
                                    id      : box.signer_id,   // El id del firmante
                                    name    : box.signer       // El nombre, email o teléfono del firmante
                                                               // según los datos que se propocionaron
                                };
                            box.rules = JSON.parse(box.rules); // viene en texto plano codificado en json
                            box.text = !box.text? '' : box.text; // Si viene nulo se inicializa con ''
                            // Estable el id de la firma
                            box.id     = box.code;
                        });

                        $this.textboxs = boxs;

                        resolve(boxs);
                    })
                    .catch (error => {
                        reject(error);
                    });
            });
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
                }

                //console.log($this.scale)
                //console.log('% Escalado');
                console.log(Math.round( $this.scale * 100) + ' %')

                $this.viewport = page.getViewport({ scale: $this.scale, });

                /**
                 * Prepara el canvas utilizando las dimensiones del viewport obtenido
                 */
                $this.canvas  = document.getElementById('pdf');
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
                    // Dibuja los contenedores de cajas de firma
                    $this.drawBoxsPlaceholdersInPage($this.page);
                });
            });
        },

        /**
         * Dibuja todos los contenedores de cajas de firma registrados en una página del documento
         * 
         * @param {Number} page     El número de página
         */
        drawBoxsPlaceholdersInPage: function (page) {
            // Elimina los contenedor de cajas de texto que pueda haber
            this.clearBoxsPlaceholders();

            // Coloca los contenedores de textos marcados en la página
            this.textboxs.forEach(box => {
                if (box.page == page) {
                    this.drawBoxPlaceholder(box);
                }
            });

            // Actualizo las minuaturas de las firmas sobre la página actual
            this.refreshPreview(this.page);
        },

        /**
         * Dibuja un contenedor de caja de texto
         * 
         * @param {Sign} sign   Una caja de texto 
         */
        drawBoxPlaceholder: function (box) {
            const $this = this;
            console.log("pintando caja de texto para " + box.id)

            // Clono la caja de muestra
            // Toma la plantilla del contenedor de firma y la clona
            let placeholder = document.querySelector('.box-placeholder.template').cloneNode(true);

            // Elimina la clase plantilla del contenedor de firma clonado
            placeholder.classList.remove('template');

            // Define el color del contenedor de firma
            placeholder.classList.add(`color-${box.signer.id % 10}`);

            const placeholderWidth  = parseInt(box.width  * this.scale);
            const placeholderHeight = parseInt(box.height * this.scale);

            // Define la anchura y altura del contenedor de firma en función del grado de zoom (escala)
            placeholder.style.width  = `${placeholderWidth}px`;

            // Añade el evento para eliminar el texto introducido de la caja al presionar la "trash"
            placeholder.querySelector('.remove-box-placeholder').dataset.boxId = box.id;
            placeholder.querySelector('.remove-box-placeholder').addEventListener('click', (e)=>{
                e.preventDefault();
                let inputField = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${e.currentTarget.dataset.boxId}"] input`);

                if (inputField) {
                    if (inputField.type == 'text') {
                        inputField.value = '';
                    } else {
                        // es un checkbox
                        inputField.checked = 0;
                    }                    
                } else {
                    inputField = placeholder.querySelector(`#textboxs > .box-placeholder[data-box-id="${e.currentTarget.dataset.boxId}"] select`);
                    inputField.selectedIndex = 0;
                }
                $this.$forceUpdate();
            });

            // Evento de selección al dar click sobre la caja de texto
            placeholder.addEventListener('click', () => {
                $this.indexBox = $this.textboxs.findIndex(b => b.id == box.id);
                $this.box = $this.textboxs[$this.indexBox];
                if ($this.indexBox > -1) {
                    $this.selectedBox = true;       // Indicamos que tenemos seleccionada una caja de texto
                }
            });
        
            // Completa el contenedor de firma
            placeholder.dataset.boxId = box.id;

            // Metemos el id de la firma en el span en el header de la caja de firma
            // Pero en vez del id metemos el nombre del firmante
            placeholder.querySelector('.box-placeholder-id').innerHTML = box.signer.name;

            // Posiciono la caja de texto según en las coordenadas correctas no importa la escala
            const placeholderLeft = $this.canvas.offsetLeft + (box.x * this.scale);
            const placeholderTop  = $this.canvas.offsetTop  + (box.y * this.scale);
          
            // Posiciona el marcador y lo hace visible
            placeholder.style.left    = `${placeholderLeft}px`;
            placeholder.style.top     = `${placeholderTop}px`;
            placeholder.style.display = 'block';

            // Añade el nombre del contenedor de firma (código o id de la firma)
            // Pero en vez del signer metemos el id de la firma
            placeholder.querySelector('.signer-name').innerHTML = box.id;

            /* adiciono el input segun el caso*/
            let input = '';

            const boxType = parseInt(box.type) || 4; // Tipo de caja de texto
            // no deshabilito el input
            let disabled = !this.canSign? 'disabled':'';
            let value = box.text ? `value="${box.text}"`:'';
            
            const charsInBox = box.rules.maxLength;           // Restricción de caracteres en la caja

            if (boxType == 1) {
                input = `<input data-box-id="${box.id}" type="text" minlength="4" maxlength="${charsInBox}" ${disabled} ${value} placeholder="${$this.langs[0]}" />`;
            } else if (boxType == 2) {
                input = `<input data-box-id="${box.id}" type="text" minlength="1" maxlength="${charsInBox}" ${disabled} ${value} placeholder="${$this.langs[1]}" />`;
            } else if (boxType == 3) {
                input = `<input data-box-id="${box.id}" type="text" minlength="1" maxlength="${charsInBox}" ${disabled} ${value} placeholder="${$this.langs[2]}" />`;
            } else if (boxType == 4) {
                input = `<input data-box-id="${box.id}" type="text" minlength="1" maxlength="${charsInBox}" ${disabled} ${value} placeholder="${$this.langs[3]}" />`;
            } else if (boxType == 5) {
                const checked = box.text == "true" ? 'checked' : '';
                input = `<input data-box-id="${box.id}" type="checkbox" ${disabled} ${checked} /><label>${$this.langs[4]}</label>`;
            } else if (boxType == 6) {
                input = `<select data-box-id="${box.id}" ${disabled}>
                    <option value='-1'>${$this.langs[5]}</option>
                </select>`;
            } else {
                input = `<input data-box-id="${box.id}" type="text" minlength="1" maxlength="${charsInBox}" ${disabled} placeholder="${$this.langs[3]}" />`;
            }

            placeholder.querySelector('.box-placeholder-body .box').outerHTML = input;

            // Añade la caja de texto a la lista de cajas
            document.getElementById('textboxs').appendChild(placeholder);

            let inputField = placeholder.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"] input`);
            inputField = inputField ? inputField : placeholder.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"] select`);

            // el font-size de los inputs texts se debe calcular según la escala para que siempre
            // permita la entrada de los mismos caracteres
            inputField.style.fontSize = `${$this.getFontSize($this.scale)}px`;

            // Asigno función dinámica para cuando se escribe en el input
            // tomar el texto en la caja seleccionada
            if ([6].indexOf(boxType) != -1) {
                console.log('asignando funcionalidad para select');
                inputField.onchange = (e) => {
                    const $this = this;

                    $this.indexBox = $this.textboxs.findIndex(box => box.id === e.target.dataset.boxId);
                    $this.textboxs[$this.indexBox].text = String(e.target.value);

                    $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                };
            } else if ([5].indexOf(boxType) != -1) {
                console.log('asignando funcionalidad para checkboxs');
                inputField.onchange = (e) => {
                    const $this = this;
                    $this.indexBox = $this.textboxs.findIndex(box => box.id === e.target.dataset.boxId);
                    $this.box = $this.textboxs[$this.indexBox];

                    this.textboxs[this.indexBox].text = String(e.target.checked);
                    $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                };
            } else {
                console.log('asignando funcionalidad para inputs texs')
                inputField.onkeyup = (e) => {
                    const $this = this;

                    // keyCodes for Backspace and delete => 8, 46
                    const removeKeys = [8, 46];
                    // códigos para tecla espacio y letras min y MAY
                    const charsCode = [32];
                    // letras 65-90
                    for (let index = 65; index <= 90; index++) {
                        charsCode.push(index);
                    }
                    // códigos para los números 48-57,  96-105
                    const numbersCode = [48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105];

                    let writing = false;
                    if (numbersCode.indexOf(e.keyCode) != -1 || charsCode.indexOf(e.keyCode) != -1) {
                        writing = true;
                    }

                    // Si no estoy eliminando texto en la caja, y estoy presionando una techa numérica o de una letra
                    // chequeo si se ha alcanzado el máximo de caracteres permitidos
                    if (removeKeys.indexOf(e.keyCode) === -1
                            && $this.box.rules.maxLength != 0
                                && writing
                    ) {
                        if ($this.box.rules.maxLength == $this.box.text.length) {
                            toastr.warning(document.querySelector('#messages').dataset.maxLengthAchieved);
                        }
                    }

                    $this.box.text = e.target.value;

                    $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                };
            }

            // Si debo hacer captura de pantalla y no se está grabando mostrar mensaje
            // al dar click encima de los inputs
            if (!$this.canSign && !$this.recording) {
                console.log('aqui onclick')
                placeholder.onclick = () => {
                    toastr.clear();
                    toastr.warning(document.getElementById('messages').dataset.mustInitScreenCaptureRecording);
                }
            }

            // Asigno funcionalidad a las cajas de texto no importa quien sea el signer
            // para validar que se escriba lo que se pidió por el creador, reglas {numbers, letters, special}
            if ([1, 2, 3, 4].indexOf(boxType) != -1){
                inputField.onkeydown = (e) => {
                    const $this = this;

                    if ($this.box.rules.maxLength == $this.box.text.length) {
                        toastr.warning(document.querySelector('#messages').dataset.maxLengthAchieved);
                        return false;
                    }

                    const keysToExclude = [32,37,38,39,40,12,27,8,46];   // Teclas que se omiten: espacio, teclas dirección,
                                                                         // espacio, numpadenter, remove keys

                    $this.indexBox = $this.textboxs.findIndex(box => box.id === e.target.dataset.boxId);
                    $this.box = $this.textboxs[$this.indexBox];
                    
                    if (keysToExclude.indexOf(e.keyCode) == -1) {
                        
                        let acceptChar = false;     // Se rechaza el caracter hasta que no se verifique
                        // En dependencia de lo que he escrito reviso la regla de la caja
                        switch($this.whatITyped(e)) {
                            case 1:
                                console.log('check for Number');
                                acceptChar = $this.box.rules.numbers;
                                break; 
                            case 2:
                                console.log('check for Letters');
                                acceptChar = $this.box.rules.letters;
                                break; 
                            case 3:
                                console.log('check for Specials');
                                acceptChar = $this.box.rules.specials;
                                break; 
                            default:
                                console.log('refuse it!, do nothing');
                                break; 
                        }

                        // Si el caracter es aceptado, el evento 'onkeyup' chekea por la longitud
                        if (!acceptChar) {
                            e.preventDefault();
                            return false;
                        }  
                    }
                };
            }

            // Si es un select y trae opciones las pinto
            if (boxType == 6 && inputField) {
                // las opciones vienen separadas por ';' en la propiedad 'options' de la caja
                console.log('pintando opciones de select ')
                box.options?.split(';').forEach(option => {
                    console.log(option)
                    if (option != '') {
                        inputField.add(new Option(option));
                    }
                });

            }

            // Funcionalidad del input cuando pierde el foco
            inputField.onblur = (e) => {
                    const $this = this;
                    //$this.refreshPreview($this.page);
                };

            
        },

        // Devuelve si se ha escrito un número o no
        isNumber: function (e) {
            // chequeo si es un número
            const numbersForSpecial = [                         // 48-57 códigos para los números y caracteres especiales + shift
                48, 49, 50, 51, 52, 53, 54, 55, 56, 57
            ];
            const numbersCode = [                               // 96-105 códigos numpad para los números
                96, 97, 98, 99, 100, 101, 102, 103, 104, 105
            ];

            if (numbersForSpecial.concat(numbersCode).indexOf(e.keyCode) != -1) {
                return true;
            } 
            return false;
        },

        // Devuelve si se ha escrito una letra o no
        isLetter: function (e) {
            // chequeo si es una letra
            const charsCode = [];                               // 65-90 códigos letras
            for (let index = 65; index <= 90; index++) {        
                charsCode.push(index);
            }
            if (charsCode.indexOf(e.keyCode) != -1) {
                return true;
            }
            return false;
        },

        // Devuelve si se ha escrito un caracter especial o no
        isSpecial: function (e) {
            // TODO: incluir simbolos -,+,_, ,, ;, : '', "", [], {}

            const numbersForSpecial = [                         // 48-57 códigos para los numeros y caracteres especiales + shift
                48, 49, 50, 51, 52, 53, 54, 55, 56, 57,
            ];
            const moreShiftSpecial = [
                186, 187, 219, 221, 222,                        // :, +, {, }, "
            ]
            const withoutShiftSpecial = [
                186, 107, 109, 111, 189, 190, 188, 219, 221, 222,// ;, +, -, /, -, ., ',', [, ], '
            ]
            if (e.shiftKey && numbersForSpecial.indexOf(e.keyCode) != -1) {
                return true;
            }
            if (e.shiftKey && moreShiftSpecial.indexOf(e.keyCode) != -1) {
                return true;
            }
            if (withoutShiftSpecial.indexOf(e.keyCode) != -1) {
                return true;
            }
            return false;
        },

        /*
         * Qué he tecleado ?, si número(1), si letra(2), si caracter especial(3), u otro(4)
         */
        whatITyped: function (e) {
            if (this.isLetter(e)) {
                return 2;
            } else {
                if (e.shiftKey) {
                    if (this.isSpecial(e)) {
                        return 3;
                    }
                } else {
                    if (this.isNumber(e)) {
                        return 1;
                    }
                    if (this.isSpecial(e)) {
                        return 3;
                    }
                }
            }
            return 4;
        },

        /*
         * Devuelve la cantidad de caracteres que pueden ocupar una caja de texto
         * según su ancho o según sus restricciones, 
         */
        charsInBox: function (box, text=null) {
            // tomo la cantidad de caracteres permitidos por la regla de restricción
            let maxLength = box.rules.maxLength;

            let fontSize = box.width / 15;
            // si es ilimitada calculo la cantidad que ocupan el ancho de la caja
            if (maxLength == 0 && [1,2,3,4].indexOf(box.type) != -1) {
                // TODO: ajustarlo al ancho de la caja
                //maxLength = parseInt(box.width / parseInt(box.width / (parseInt(box.width / 15))));
                maxLength = 72; // Cantidad de caracteres que supuestamente puede contener una linea en una hoja A4
                box.rules.maxLength = 72;
            }

            // Guardo el valor de la longitud máxima inicial
            if (!box.initMaxLength) {
                box.initMaxLength = box.rules.maxlength;
            }
            
            // si 'text' es verdadero se devuelve la info completa para mostrar en opciones de la caja como propiedad
            // Cantidad máxima : xx
            if (text) {
                return `${this.langs[6]} : ${maxLength}`;
            }
            return maxLength;
        },

        /**
         * Actualiza la miniatura de la página dada posicionando las cajas de firma
         * que se han configurado sobre ella
         *
         * @param {Number} page     Un número de página
         */
        refreshPreview: async function (page) {
            console.log("Actualizando minuatura de página => " + page);

            try {
                const $this = this;

                // busco el canvas correspondiente a la miniatura que debo actualizar
                const pageDiv = document.getElementById('pagesPreview').querySelector(
                    `[data-page="${page}"]`
                );

                // Canvas que debe ser actualizado
                const canvasDestination = pageDiv.querySelector('canvas');
                if (!canvasDestination) {
                    return;
                }
                const ctx = canvasDestination.getContext('2d');

                // repinto solo la página actual
                pdfjsLib.getDocument($this.url)
                    .promise.then(pdf => {
                        return Promise.all([page].map(num => {

                            // Construye un canvas en el elemento de página
                            return pdf.getPage(num)
                                .then(pageDiv => $this.makeThumb(pageDiv))
                                .then(canvas => {
                                    const ctxCanvas = canvas.getContext('2d');

                                    //copy canvas by ImageData from backup or from canvas by case
                                    if (this.pagesData[num].imageData) {
                                        ctx.putImageData(this.pagesData[num].imageData, 0, 0);
                                    } else {
                                        ctx.putImageData(
                                            ctxCanvas.getImageData(0, 0, canvas.width, canvas.height),
                                            0, 0
                                        );
                                    }

                                    // controla si el firmante ha completado las cajas sobre esta página
                                    let signerPendingOnPage = false;

                                    // Obtengo todas las cajas de texto dentro de la página actual
                                    this.textboxs.filter( box => box.page === num)
                                        .forEach( box => {
                                            // Tomo el color de la caja de firma para la miniatura
                                            let rgb = $(`[data-box-id="${box.code}"]`).css("background-color");
                                            let color = '#' + rgb.substr(4, rgb.indexOf(')') - 4).split(',').map((color) => parseInt(color).toString(16)).join('');
                                            
                                            // En caso de que no se pueda obtener el color del div
                                            if (!color) {
                                                color = $this.pagesData[num].randomColor;
                                                if (box.signer.creator == 0) {
                                                    color = $this.pagesData[num].signerRColor;
                                                }
                                            }

                                            if (!box.text || !box.text == '-1') {
                                                signerPendingOnPage = true;
                                            }

                                            if (box.text) {
                                                const fontSize = parseInt($this.canvas.width / $this.getFontSize()) - 4;
                                                ctx.font = `${fontSize}px Arial`;

                                                // Pinto rectángulo de color
                                                ctx.fillStyle = `${color}`;
                                                ctx.fillRect(box.x + box.shiftX, box.y + box.shiftY, box.width, 23);
                                                // Pinto texto de la caja
                                                ctx.fillStyle = `#fff`;
                                                // La posición exacta es la x de la caja + la diferencia en 'x' y 'y' del input respecto a la caja contenedora
                                                //ctx.strokeText(box.text, box.x + box.shiftX + 3, box.y + box.shiftY + 20);
                                                ctx.fillText(box.text, box.x + box.shiftX + 3, box.y + box.shiftY + 20);
                                            } else {
                                                ctx.fillStyle = `${color}`;
                                                ctx.fillRect(box.x, box.y, box.width, $this.textbox.height); 
                                            }
                                        });

                                    // Si el creador tiene cajas pendientes en esta página la marcamos como pendiente
                                    if (signerPendingOnPage) {
                                        pageDiv.querySelector('.page-number').classList.add('remaining');
                                    } else {
                                        pageDiv.querySelector('.page-number').classList.remove('remaining');
                                    }
                                });
                    }));
                
                }).catch(error => {
                    console.error(error);
                });
            }
            catch(error) {
                console.error(error)
            }
            finally {
                console.log('finally')
            } 
        },

        /**
         * Elimina todos los contenedores de textboxs del documento
         */
        clearBoxsPlaceholders: function () {
            document.getElementById('textboxs').innerHTML  = '';
        },

        /*
        * Guardo las cajas de texto y capturas realizadas en el proceso
        */
        saveBoxsAndCaptures: function () {
            const $this = this;

            // Obtiene las cajas realizadas
            $this.signed = $this.textboxs.filter(box => {
                return box => box.text != null || box.text != -1;
            });

            // Obtiene la url de la solicitud para guardar las cajas del documento
            const request = document.getElementById('pdf').dataset.request;
            console.log(request);

            // La data que se envía
            const data =
                {
                    textboxs    : $this.textboxs,   // Se envían sólo las cajas realizadas (firmadas)
                    position    : $this.position,   // La posición datum WGS84
                    visit       : $this.visit,      // La información de la visita realizada a la página
                    captures    : $this.captures,   // Las capturas de la pantalla si se han proporcionado
                };

            // Inicia animación
            HoldOn.open({theme: 'sk-circle'});

            axios.post(request, data)
            .then((response) => {
                HoldOn.close();
                if (response.data.code == 1) {
                    // Muestra una modal indicando que el documento ha sido firmado con éxito
                    try {
                        $this.$bvModal.show('document-signed');
                    } catch (error) {
                        // Por ejemplo cuando estoy offline
                        location.href = document.getElementById('data').dataset.redirectToWorkspaceHome;
                    }
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
                        $this.saveBoxsAndCaptures();
                    }, 1000);

                };

                stop();
            } else {
                $this.saveBoxsAndCaptures();
            }
        },

        /**
         * Finaliza y sale de la pantalla
         * Redirigiendo a la vista principal del espacio de trabajo (workspace)
         */
        exit: function() {
            location.href = document.getElementById('data').dataset.redirectToWorkspaceHome;
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
                        $this.recording = false;
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
                            $this.drawBoxsPlaceholdersInPage($this.page);

                            toastr.success($this.messages.captureRecordSuccess);

                        });
                    
                        // Muestro el preview de la captura de pantalla
                        $this.$refs.videoScreen.style.display = 'block';

                        $this.recording = true;

                        // Habilito poder firmar sobre las cajas de firma
                        $this.canSign = true;
                        $this.drawBoxsPlaceholdersInPage($this.page);
                        
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
         * Devuelve el tamaño de fuente para los inputs textos según la escala
         */
        getFontSize: function (scale) {
            /**
             * fuente ideal
             *   en input a lo ancho deben caber 72 caracteres
             *   100%  585px 72 chars  14.4                 40.62
             *         171px ?
             *   125%  724px 72 chars  18.0 px fontSize     40.22
             *   150%  884px 72 chars  21.9 px              40.36
             *   175% 1034px 72 chars  25.5                 40.54
             *   200% 1184px 72 chars  29.4                 40.27
             *                                           ===========
             *                                               40.4
             */
            if (scale <= 1.25) {
                return 14.4;
            } else if (scale > 1.25 && scale <= 1.5) {
                return 18;
            } else if (scale > 1.5 && scale <= 1.75) {
                return 21.9;
            } else if (scale > 1.75 && scale <= 2) {
                return 25.5;
            } else {
                return 29.4;
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
         * Comprueba si todas las cajas han sido completadas
         * 
         * @return {Boolean}
         * 
         */
        allSignaturesHaveBeenMade: function() {
            // Si ya se han completado las cajas de texto
            if (this.textboxs) {
                // Obtiene las cajas realizadas
                const signed = this.textboxs.filter(box => box.text != "" && box.text != -1);
                // Comprueba las cajas realizadas con las que deben ser realizadas
                return signed.length == this.textboxs.length;
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