/**
 * Configuración de firma del documento
 * 
 * El creador establece unas zonas del documento que deben ser firmadas
 * por los usuarios que el seleccione, y unas zonas donde debe proceder a firmar el mismo
 * 
 * @author javieru <javi@gestoy.com>
 * @author rosellpp <rpupopolanco@gmail.com>
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
const { default: Axios } = require('axios');
import interact from "interactjs";

new Vue({
    el: '#app',
    data: {

                                    // Herramientas que puede utilizar el usuario
        Tool :
            {
                Sign : 0,           // Herramienta de firma
                Stamp: 1,           // Herramienta de sello
            },
                                    // El documento que se procesa
        document    : 
            {
                guid: undefined,
            },

                                    // La herramienta que está siendo utilizada en este momento por el usuario
        tool        : null,

        page        : 1,            // La paǵina actual
        pages       : null,         // El número de páginas del documento
        scale       : 1,            // La escala actual del documento
        fit         : false,        // Si se ajusta el documento a la pantalla
        maxScale    : 2,            // La máxima escala del documento (al hacer zoom)
        minScale    : 1,            // La mínima escala del documento (al hacer zoom)
        url         : null,         // La dirección URL del documento PDF cargado
        pdf         : null,         // Referencia al documento PDF cargado
        canvas      : null,         // El elemento canvas
        context     : null,         // El contexto del canvas
        viewportI   : [],           // Array para guardar el viewport inicial x c/p 
        viewport    : null,         // El viewport

        // Las dimensiones de la caja de firma
        // 6.5 cm x 5.5 cm
        // 5.5 cm = 156         px
        // 6.5 CM = 184.2       px
        placeholder :              // Las dimensiones de un contenedor de firma
            {                      // cuando se muestra a escala unidad
                width  : 185,
                height : 155,
                canvas :
                {
                    width   : 175,      // La anchura del elemento canvas
                    height  :  85,      // La altura  del elemento canvas
                }
            },

        signers     : [],           // La lista de firmantes del documento
       
        signs       : [],           // Las posiciones de las firmas
                                    // Es una lista de firmas
      
        sign        :               // Definición de una firma
            {
                id      : null,     // El id del contenedor de firma
                signer  :           // El firmante que debe realizar la firma en ese lugar
                    {
                        id      : null,   // El id del firmante
                        name    : null,   // El nombre, email o teléfono del firmante
                                          // según los datos que se proporcionaron
                        creator : false,  // Si el firmante es el creador del documento o no
                    },
                page    : null,     // La paǵina
                x       : null,     // La coordenada "x" dentro de la página
                y       : null,     // La coordenada "y" dentro de la página
                sign    : null,     // La firma,
            },

        stamps      : [],           // Los sellos estampados sobre el documento

        stamp       :
            {
                page    : null,     // La paǵina
                x       : null,     // La coordenada "x" dentro de la página
                y       : null,     // La coordenada "y" dentro de la página
                stamp   : null,     // El sello
            },

        // Los datos de configuración del usuario
        config  : 
            {
                // La firma que tiene configurada por defecto
                sign    : null,
                // Los sellos que tiene disponibles para estampar sobre el documento
                stamps  : [],
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
                saveStamp       : document.getElementById('request').dataset.saveStamp,
                removeStamp     : document.getElementById('request').dataset.removeStamp,
                saveDocument    : document.getElementById('pdf').dataset.request,
            },
        
        // Controla si se está cargando el pdf
        rendering : true,

        // Función que se ejecuta cada 1 segundo para controlar el resize de la ventana
        resizeTo : null,

        // Para guardar los valores del offset del canvas, útil cuando se repinta y el canvas es nulo
        // mientras se posicionan las cajas de firmas
        offsets  : {
            left: 0,        // Offset a la izquierda
            top : 0,        // Offset hacia arriba
        },

        // array para guardar info relacionada con cada página del documento
        pagesData: [],

        // log canvas moves over canvas for monitoring mouse position, only for tests
        mouse : {
            x : 0,
            y : 0,
            clientX : 0,
            clientY : 0,
            canvasLeft : 0,
            canvasTop  : 0,
        },

        positionsSigns : [],     // Lista de par x,y para controlar el movimiento de cada caja
        positionsStamps: [],     // Lista de par x,y para controlar el movimiento de cada sello
        indexSign      : -1,     // Indice de la firma seleccionada para mover en el array de firmas
        indexStamp     : -1,     // Indice de el sello seleccionado para mover en el array de sellos
        usedColors     : [],     // Clases de colors que se han utilizado

    },

    /**
     * Cuando tenemos la instancia montada
     *
     */
    mounted: function () {

        const $this = this;

        $this.fit = false;

        // Fija la herramienta a usar por defecto
        $this.tool = $this.Tool.Sign;

        // Carga el o los firmantes del documento
        $this.signers = JSON.parse(document.getElementById('signers').dataset.signers);
    
        // Añadimos la propiedad "selected" que indica si un firmante ya ha sido seleccionado
        // Es decir, si se ha definido ya al menos una posición o cuadro de firma para el mismo
        //
        // Antes de finalizar el proceso habrá que verificar que todos los firmantes han sido seleccionados
        $this.signers.forEach(signer => signer.selected = false);
          
        // Carga la firma que tiene configurada el usuario por defecto
        $this.config.sign = document.getElementById('user-sign').getAttribute('src') || null;

        // Carga los sellos de los que dispone el usuario
        $this.config.stamps = JSON.parse(document.getElementById('stamps').dataset.stamps);

        // La url del documento PDF
        $this.url = document.getElementById('pdf').dataset.pdf;

        // Obtiene las miniaturas del documento
        $this.getThumbs();

        /**
         * Carga asíncrona del documento PDF
         */
        const loadingTask = pdfjsLib.getDocument($this.url);

        loadingTask.promise.then(function(pdf) {

            // Fija el número de páginas del documento
            $this.page  = 1;
            $this.pages = pdf.numPages;
            $this.pdf   = pdf;

            /**
             * Espera a la obtención de las firmas existentes
             * ante de cargar la primera página del documento
             */
            $this.loadSigns()
                .then(() => {
                    
                    // Carga la paǵina del documento
                    $this.render();

                    // Abre la modal con la ayuda
                    $this.$bvModal.show('help-config-document');
                })
                .catch(error => {
                    console.error("Error al cargar documento PDF");
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

        /**
         * Obtiene las miniaturas del documento PDF
         * 
         */
        getThumbs: function () {

            const $this = this;
            
            // la url del pdf que se previsualiza
            console.log($this.url);
            
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

                        // Si la página falta aún de ser firmada, resalta el número de página
                        /* TODO
                        if ($this.remainingPages.includes(num)) {
                            page.querySelector('.page-number').classList.add('remaining');
                        }
                        */

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

                                // Obtengo todas las firmas dentro de la página actual
                                this.signs.filter( sign => sign.page === num)
                                    .forEach( sign => {
                                        // Pinto algo en la posición x, y de la firma
                                        let color = $this.pagesData[num].randomColor;
                                        if (sign.signer,creator == 0) {
                                            color = $this.pagesData[num].signerRColor;
                                        }
                                        ctx.fillStyle = `#${color}`;
                                        ctx.fillRect(sign.x, sign.y, 250, 150);
                                        ctx.fillStyle = `#000000`;
                                        // TODO, agregar el código de la firma en el canvas
                                    });

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

        /**
         * Actualiza la miniatura de la página dada posicionando las cajas de firma
         * que se han configurado sobre ella
         *
         * @param {Number} page     Un número de página
         */
        refreshPreview: async function (page) {
            console.log("Actualizando minuatura de página => " + page);
            const $this = this;

            // busco el canvas correspondiente a la miniatura que debo actualizar
            const pageDiv = document.getElementById('pagesPreview').querySelector(
                `[data-page="${page}"]`
            );

            // Canvas que debe ser actualizado
            const canvasDestination = pageDiv.querySelector('canvas');
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

                                // controlar si el creador ha firmado todas sus firmas sobre esta página
                                let creatorPendingOnPage = false;

                                // Obtengo todos los sellos dentro de la página actual
                                this.stamps.filter(stamp => stamp.page === num )
                                    .forEach( stamp => {
                                        console.log("pintando miniatura de sello ", stamp.code);

                                        // stamp.thumb  es la imagen del sello, pintarlo en miniatura

                                        var image = new Image();
                                        image.src = stamp.stamp.thumb;

                                        var canvasTmp = document.createElement('canvas');
                                        var ctxTmp    = canvasTmp.getContext('2d');

                                        image.onload = () => {
                                            ctxTmp.drawImage( image, 0, 0, 180, 180);

                                            ctx.putImageData(
                                                ctxTmp.getImageData(0, 0, 180, 180),
                                                stamp.x,
                                                stamp.y,
                                            );
                                        };
                                    });

                                // Obtengo todas las firmas dentro de la página actual
                                this.signs.filter( sign => sign.page === num)
                                    .forEach( sign => {
                                        console.log("pintando miniatura de firma");

                                        // Tomo el color de la caja de firma para la miniatura
                                        let rgb = $(`[data-sign-id="${sign.code}"]`).css("background-color");
                                        let color = '#' + rgb.substr(4, rgb.indexOf(')') - 4).split(',').map((color) => parseInt(color).toString(16)).join('');
                                        
                                        // En caso de que no se pueda obtener el color del div
                                        if (!color) {
                                            color = $this.pagesData[num].randomColor;
                                            if (sign.signer.creator == 0) {
                                                color = $this.pagesData[num].signerRColor;
                                            }
                                        }

                                        // Si no se ha encontrado una firma pendiente
                                        // y es del creador
                                        if (!creatorPendingOnPage && sign.signer.creator == 1) {
                                            creatorPendingOnPage = sign.sign? false:true;
                                        }

                                        // Si la firma se ha realizado, pinto la miniatura de la misma
                                        if (sign.sign) {
                                            console.log("pintar miniatura de firma => " + sign.code);

                                            const myfn = async () => {
                                                // Devuelvo la imagen escalada en su tamanno original 100 %.
                                                return await this.resizeImage(
                                                    sign.sign,
                                                    $this.placeholder.canvas.width,
                                                    $this.placeholder.canvas.height,
                                                );
                                            }

                                            // pinto esta imagen en todo el canvas de firma escalada segun este canvas
                                            myfn().then(scaleImage => {
                                                var image = new Image();
                                                image.src = scaleImage;

                                                var canvasTmp = document.createElement('canvas');
                                                var ctxTmp    = canvasTmp.getContext('2d');

                                                image.onload = () => {
                                                    ctxTmp.drawImage( image, 0, 0, image.width, image.height);

                                                    ctx.putImageData(
                                                        ctxTmp.getImageData(0, 0, $this.placeholder.canvas.width, $this.placeholder.canvas.height),
                                                        sign.x,
                                                        sign.y,
                                                    );
                                                };
                                                
                                            });

                                        } else {
                                            console.log("pintar caja para simular firma => " + sign.code);

                                            // Pinto algo en la posición x, y de la firma
                                            //ctx.fillStyle = `#${color}`;
                                            ctx.fillStyle = `${color}`;
                                            ctx.fillRect(sign.x, sign.y, $this.placeholder.width, $this.placeholder.height-30);
                                            // TODO, agregar el código de la firma en el canvas
                                        }
                                        console.log('miniatura de firma actualizada [ok]');

                                    });

                                // Si el creador tiene firmas pendientes en esta página la marcamos como pendiente
                                if (creatorPendingOnPage) {
                                    pageDiv.querySelector('.page-number').classList.add('remaining');
                                } else {
                                    pageDiv.querySelector('.page-number').classList.remove('remaining');
                                }
                            });
                }));
            
            }).catch(error => {
                console.error(error);
            });

        },

        // Repinta el canvas al finalizar el evento de redimensión del
        // navegador y no constantemente
        captureResizeWindowEvent: function () {
            const $this = this;

            $(window).resize(function () {
                
                if ($this.fit) {
                    $this.rendering = true;
                    // Limpia el canvas
                    $this.clearCanvas();
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
         * Cierra la modal con la ayuda
         *
         */
        closeHelp: function () {
            this.$bvModal.hide('help-config-document');
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

        /**
         * Selecciona la herramienta a utilizar
         * 
         * @param {Tool} Tool
         * 
         */
        selectTool: function(Tool) {
            this.tool = Tool;
        },

        /**
         * Obtiene la posición del ratón sobre el documento
         * 
         * @param {Event} event 
         * 
         * @return {Mouse}          La posición del ratón sobre el documento
         */
        getMousePositionOverDocument: function (event) {
            const $this = this;

            //console.log("=======================")
            //console.log("Escala : " + this.scale)

            // Obtiene la posición en el canvas
            var rect = $this.canvas.getBoundingClientRect();

            //console.log("Posicion del canvas")
            //console.log(rect)
            //console.log(`Canvas Left => ${rect.left} - Top => ${rect.top}`)

            // Obtiene la posición actual del ratón
            let mouse =
                {
                    x : parseInt((event.clientX - rect.left) / this.scale),
                    y : parseInt((event.clientY - rect.top ) / this.scale),
                };

            // Obtiene el ancho y alto de la página
            let page =
                {
                    width  : document.querySelector('#pdf').clientWidth  / this.scale,
                    height : document.querySelector('#pdf').clientHeight / this.scale,
                };

            //console.log("Dimensiones del canvas");
            //console.log(page.width + ' x ' + page.height);

            // El ancho y alto del cuadro de firma
            let signPlaceholder =
                {
                    width  : this.placeholder.width,// * this.scale,
                    height : this.placeholder.height,// * this.scale,
                };

            //console.log("Dimensiones del cuadro de firma");
            //console.log(signPlaceholder.width + ' x ' + signPlaceholder.height);

            // Si la posición del cuadro de firma que se va a dibujar excede los límites de la pantalla
            // se corrige la posición para que el cuadro de firma se ajuste a esos límites
            if (mouse.x + signPlaceholder.width > page.width) {
                mouse.x = (page.width - signPlaceholder.width);// / this.scale;
            }

            if (mouse.y + signPlaceholder.height > page.height) {
                mouse.y = (page.height - signPlaceholder.height);// / this.scale;
            }

            //console.log("Info de posiciones fimales");
            //console.log("Mouse X => " + mouse.x + ' - Mouse Y => ' + mouse.y);

            /*
            this.context.fillRect(
                mouse.x,
                mouse.y,
                5,
                5
            );
            */

            //console.log("==========================")

            return mouse;
        },

        /**
         * Inserta un elemento en la posición actual del ratón
         * 
         * @param {Event} event 
         * 
         */
        select: function (event) {
            /*
                // Para configurar las firmas y/o los sellos el usuario debe estar trabajando
                // en 100 % de escalado
                if (this.scale != 1) {
                    toastr.error(document.getElementById('messages').dataset.scaleNotAllowed);
                    return;
                }
            */

            switch (this.tool) {
                //
                // Herramienta de firma manuscrita
                //
                case this.Tool.Sign:
                    this.selectSign(event);
                    break;
                //
                // Herramienta de estampación de sello
                //
                case this.Tool.Stamp:
                    this.selectStamp(event);
                    break;
            }
        },

        /**
         * Selecciona una firma
         * 
         * @param {Event} event 
         */
        selectSign: function (event) {

            // Obtiene la posición en el canvas
            this.canvas.getBoundingClientRect();

            // Genera un código/id único para cada firma
            const code = Math.random().toString(16).substring(2);

            // Obtiene la posición actual del ratón
            let mouse = this.getMousePositionOverDocument(event);
             
            this.sign = 
                {
                    // Se genera un id único para cada contenedor de firma
                    id      : code,
                    code    : code,
                    signer  : 
                        {
                            id      : null,
                            name    : null,
                            creator : false
                        },
                    page    : this.page,
                    x       : mouse.x,
                    y       : mouse.y
                };

            // Si hay varios firmantes a elegir, abrir la modal para seleccionar el firmante
            if (this.signers.length > 1) {
                this.$bvModal.show('select-signer');
            } else {
                // Inserta la posición de firma directamente
                this.insertSign();
            }
        },

        /**
         * Selecciona un sello
         * 
         * @param {Event} event 
         */
        selectStamp: function (event) {
            
            // Obtiene la posición actual del ratón
            let mouse = this.getMousePositionOverDocument(event);
             
            this.stamp = 
                {
                    page    : this.page,
                    x       : mouse.x,
                    y       : mouse.y,
                    stamp   : null,
                };

            // Abre la modal para seleccionar un sello
            this.$bvModal.show('select-stamp');

        },

        /**
         * Carga las firmas previamente guardadas del documento
         * 
         * @return {Promise}    Una promesa
         */
        loadSigns: function () {

            const $this = this;

            // La url de la petición para obtener las firmas
            const requestSigns = document.getElementById('pdf').dataset.signs;

            return new Promise((resolve, reject) => {
                axios.post(requestSigns)
                    .then(response => {
                        // Obtiene las firmas
                        let signs = response.data;
                        //console.log("Firmas cargadas previamente guardadas")
                        //console.log(signs);
                        
                        signs.forEach(sign => {
                            // Establece los datos del firmante
                            sign.signer =
                                {
                                    id      : sign.signer_id,       // El id del firmante
                                    name    : sign.signer,          // El nombre, email o teléfono del firmante
                                                                    // según los datos que se proporcionaron
                                    creator : sign.creator == '1'   // true si la firma es del creador/autor del documento
                                                                    // false en caso contrario
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
         * Inserta la firma en el lugar del documento indicado,
         * es decir, en una posición determinada dentro de una paǵina
         * 
         */
        insertSign: async function () {

            // Obtiene el firmante seleccionado
            const selected = document.getElementById('signer');

            // Si se ha seleccionado un firmante entre la lista de firmantes
            if (selected) {
                // Se obtiene el firmante seleccionado
                this.sign.signer = this.signers.find(signer => signer.id ==  selected.value);  
            } else {
                // Si no hay firmante seleccionado se selecciona el primero de ellos
                this.sign.signer = this.signers.find(Boolean);
            }

            //
            // La firma estará inicialmente vacía salvo que sea del autor/creador del documento y 
            // haya definido una firma por defecto para usar en los procesos de firma en la configuración del usuario
            //
            if (this.sign.signer.creator ) {
                // Al ser del creador marcamos la página mini como que debe ser firmada
                document.getElementById('pagesPreview').querySelector(
                    `[data-page='${this.sign.page}'] .page-number`
                ).classList.add('remaining');

                this.sign.sign = null;         // Firma inicialmente vacía

                if (this.config.sign) {
                    //
                    // La imagen original debe ser redimensionada a 240 x 80
                    //

                    this.sign.sign = await this.resizeImage(
                        this.config.sign,
                        this.placeholder.canvas.width,
                        this.scale * 150 - 70,
                    );
                }
    
            } else {
                this.sign.sign = null;         // Firma inicialmente vacía
            }

            // Añade la firma a la lista de firmas
            this.signs.push(this.sign);

            // Oculta la modal
            this.$bvModal.hide('select-signer');

            // Dibuja el contenedor de firma
            this.drawSignPlaceholder(this.sign);

            // Actualizo las minuaturas de las firmas sobre la pagina actual
            this.refreshPreview(this.page);
        },
        /**
         * 
         * Inserta un sello en el documento
         * 
         * @param {Stamp} stamp     El sello a insertar
         *  
         */
        insertStamp: function (stamp) {
            
            // Fija un código único para cada sello
            this.stamp.code = Math.random().toString(16).substring(2);

            // Selecciona el sello elegido entre la lista de sellos disponibles
            this.stamp.stamp = stamp;

            // Añade el sello estampado a la lista de sellos estampados
            this.stamps.push(this.stamp);

            // Oculta la modal
            this.$bvModal.hide('select-stamp');

            // Dibuja el sello estampado
            this.drawStamp(this.stamp);
        },
        /**
         * Selecciona un sello predeterminado de la biblioteca de sellos que se proporciona
         * 
         * @param {Number} id   El id del sello de la biblioteca
         *
         */
        selectStampFromLibrary: async function (id) {

            // Obtenemos el sello seleccionado de la biblioteca
            let libraryStamp = document.querySelector(`.stamp-library[data-stamp-id="${id}"`);
           
            // El ancho del sello
            let stampWidth = stamps.dataset.maxWidth; 

            // Inserta el sello en el documento
            this.insertStamp(
                {
                    page    : this.stamp.page,
                    x       : this.stamp.x,
                    y       : this.stamp.y,
                    thumb   : await this.resizeImage(libraryStamp.src, stampWidth),
                }
            );
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

        getColorForSigner: function (signer) {
            const indexSigner = this.usedColors.findIndex(color => color.signerId == signer.id);
            
            if (indexSigner != -1) {
                return this.usedColors[indexSigner].class;
            } else {
                return null;
            }
        },

        /**
         * Define el color que debe tomar la caja de firma
         * 
         * Se tiene en cuenta el color de fondo de la imagen para no superponer colores similares
         */
        definePlaceholderColor: function (sign) {
            // Tomo el pixel sobre el que he dado click para posicionar la caja de firma
            // Se toma el pixel ubicado en la intersección de las diagonales del rectángulo
            // de la caja de firma
            // x = sign.x + sign.width / 2
            // y = sign.y + sign.height / 2

            let color = this.getColorForSigner(sign.signer);

            if (color) {
                console.log("Color guardado");
                return `color-${color}`;
            } else {
                console.log("Buscando nuevo color");
                const pixelData = this.context.getImageData(
                    sign.x + (this.placeholder.width * this.scale / 2),  // + (sign.width / 2),
                    sign.y + (this.placeholder.height * this.scale / 2), // + (sign.height / 2),
                    1,
                    1,
                ).data;

                const pixel = {
                    r : pixelData[0],
                    g : pixelData[1],
                    b : pixelData[2],
                    a : pixelData[3],
                };

                /*
                    .color-0 { background-color: var(--pink);}          #f66d9b     246,109,155,1
                    .color-1 { background-color: var(--red);}           #e3342f     227,52 ,47 ,1 
                    .color-2 { background-color: var(--orange);}        #f6993f     246,153,63 ,1 
                    .color-3 { background-color: var(--green);}         #38c172     56 ,193,114,1
                    .color-4 { background-color: var(--teal);}          #4dc0b5     77 ,192,181,1
                    .color-5 { background-color: var(--cyan);}          #6cb2eb     108,178,235,1
                    .color-6 { background-color: var(--blue);}          #3490dc     52 ,114,220,1
                    .color-7 { background-color: var(--indigo);}        #6574cd     101,116,205,1
                    .color-8 { background-color: var(--purple);}        #9561e2     149,97 ,226,1
                    .color-9 { background-color: var(--gray);}          #808080     128,128,128,1
                */
                const colors = [
                    {
                        class : 0,
                        r     : 246,
                        g     : 109,
                        b     : 155,
                    },{
                        class : 1,
                        r     : 227,
                        g     : 52,
                        b     : 47,
                    },{
                        class : 2,
                        r     : 246,
                        g     : 153,
                        b     : 63,
                    },{
                        class : 3,
                        r     : 56,
                        g     : 193,
                        b     : 114,
                    },{
                        class : 4,
                        r     : 77,
                        g     : 192,
                        b     : 181,
                    },{
                        class : 5,
                        r     : 108,
                        g     : 178,
                        b     : 235,
                    },{
                        class : 6,
                        r     : 52,
                        g     : 114,
                        b     : 220,
                    },{
                        class : 7,
                        r     : 101,
                        g     : 116,
                        b     : 205,
                    },{
                        class : 8,
                        r     : 149,
                        g     : 97,
                        b     : 226,
                    },{
                        class : 9,
                        r     : 128,
                        g     : 128,
                        b     : 128,
                    },
                ];

                // Para buscar el color menos parecido
                let max = 0;
                let bestColor = 0;

                // Debo buscar el color que más contraste o que menos se parezca al color de fondo en la parte de la imagen
                // donde coloque la caja de firma, y que no esté dentro de 'usedColors', que son los que se han utilizado
                colors.filter(
                    color => this.usedColors.map(color => color.class).indexOf(color.class) == -1
                ).forEach( color => {
                    let deltae = this.deltae94(pixel, color);
                    // console.log(color, deltae);
                    // Tomo el menor valor, el más parecido
                    if (deltae > max) {
                        max = deltae;
                        bestColor = color.class;
                    }
                });

                this.usedColors.push({
                    class   : bestColor,
                    signerId: sign.signer.id,                    
                });

                return `color-${bestColor}`;
            }
            //return `color-${sign.signer.id % 10}`;
        },

        // a converter for converting rgb model to xyz model
        // @see https://www.it-swarm-es.com/es/algorithm/como-comparar-dos-colores-por-similituddiferencia/942740044/
        rgbToXYZ: function(rgb) { 
            let red = parseFloat(rgb.r / 255);
            let green = parseFloat(rgb.g / 255);
            let blue = parseFloat(rgb.b / 255);

            if (red > 0.04045) {
                red = (red + 0.055) / 1.055;
                red = Math.pow(red, 2.4);
            } else {
                red = red / 12.92;
            }

            if (green > 0.04045) {
                green = (green + 0.055) / 1.055;
                green = Math.pow(green, 2.4);    
            } else {
                green = green / 12.92;
            }

            if (blue > 0.04045) {
                blue = (blue + 0.055) / 1.055;
                blue = Math.pow(blue, 2.4);    
            } else {
                blue = blue / 12.92;
            }

            red = (red * 100);
            green = (green * 100);
            blue = (blue * 100);

            var x = (red * 0.4124) + (green * 0.3576) + (blue * 0.1805);
            var y = (red * 0.2126) + (green * 0.7152) + (blue * 0.0722);
            var z = (red * 0.0193) + (green * 0.1192) + (blue * 0.9505);

            return [x, y, z];
        },

        //a convertor from xyz to lab model
        // @see https://www.it-swarm-es.com/es/algorithm/como-comparar-dos-colores-por-similituddiferencia/942740044/
        xyzToLab: function (xyz) { 
            var x = xyz[0];
            var y = xyz[1];
            var z = xyz[2];

            var x2 = x / 95.047;
            var y2 = y / 100;
            var z2 = z / 108.883;

            if (x2 > 0.008856) {
                x2 = Math.pow(x2, 1/3);
            } else {
                x2 = (7.787 * x2) + (16 / 116);
            }

            if (y2 > 0.008856) {
                y2 = Math.pow(y2, 1 / 3);
            } else {
                y2 = (7.787 * y2) + (16 / 116);
            }

            if (z2 > 0.008856) {
                z2 = Math.pow(z2, 1 / 3);
            } else {
                z2 = (7.787 * z2) + (16 / 116);
            }

            var l= 116 * y2 - 16;
            var a= 500 * (x2 - y2);
            var b= 200 * (y2 - z2);
            
            var labresult = new Array();
            labresult[0] = l;
            labresult[1] = a;
            labresult[2] = b;
            
            return (labresult);
        },

        //calculating Delta E 1994
        // @see https://www.it-swarm-es.com/es/algorithm/como-comparar-dos-colores-por-similituddiferencia/942740044/
        deltae94: function (rgb1, rgb2) {
            const lab1 = this.xyzToLab(this.rgbToXYZ(rgb1));
            const lab2 = this.xyzToLab(this.rgbToXYZ(rgb2));

            var c1 = Math.sqrt( (lab1[1] * lab1[1]) + (lab1[2] * lab1[2]) );
            var c2 = Math.sqrt( (lab2[1] * lab2[1]) + (lab2[2] * lab2[2]) );
            var dc = c1 - c2;

            var dl = lab1[0]-lab2[0];
            var da = lab1[1]-lab2[1];
            var db = lab1[2]-lab2[2];
            
            var dh = Math.sqrt( (da * da) + (db * db) - (dc * dc));

            var first = dl;
            var second = dc / (1 + (0.045 * c1));
            var third = dh / (1 + (0.015 * c1));

            return (Math.sqrt( (first * first) + (second * second) + (third * third)));
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

            // console.log("Insertando firma")
            // console.log("Posicion de la firma X(left) x Y(top)")
            // console.log(`X-Left => ${sign.x}  Y - Top => ${sign.y}`);

            // Toma la plantilla del contenedor de firma y la clona
            let placeholder = document.querySelector('.sign-placeholder.template').cloneNode(true);

            // Elimina la clase plantilla del contenedor de firma clonado
            placeholder.classList.remove('template');

            // Define el color del contenedor de firma
            // A cada firmante se le asigna un color definidos por la sucesión de clases: [color-0 .. color-9]
            placeholder.classList.add($this.definePlaceholderColor(sign));

            const placeholderWidth  = parseInt(this.placeholder.width  * this.scale);
            const placeholderHeight = parseInt(this.placeholder.height * this.scale);

            console.log("Ancho x Alto de contenedor de firma");
            console.log(`${placeholderWidth} x ${placeholderHeight}`);

            // Define la anchura y altura del contenedor de firma en función del grado de zoom (escala)
            placeholder.style.width  = `${placeholderWidth}px`;
            placeholder.style.height = `${placeholderHeight}px`;

            //
            // El elemento canvas también debe ser redimensionado
            //
            // Ecuaciones de transformación paramétricas:
            // 
            // Wσ = W1  · σ
            // Hσ = 150 · σ - 70
            //
            // Siendo:
            //  σ  la escala actual del documento, σ = 1,2,...
            //  W1 es el ancho del elemento de firma a escala σ = 1
            //

            // Solo muestro el canvas en la caja cuando la escala es mayor o igual a 1
            if (this.scale < 1) {
                placeholder.querySelector('canvas').style.display = 'none';
            } else {
                // canvas sizes
                const canvasWidth  = parseInt(this.placeholder.canvas.width  * this.scale);
                //const canvasHeight = parseInt(this.placeholder.canvas.height  * this.scale);

                placeholder.querySelector('canvas').style.width  = `${canvasWidth}px`;
                placeholder.querySelector('canvas').style.height = `${this.scale * 150 - 70}px`;

                placeholder.querySelector('canvas').width  = canvasWidth;
                placeholder.querySelector('canvas').height = this.scale * 150 - 70;
            }
        
            // Completa el contenedor de firma
            placeholder.dataset.signId = sign.id;
            placeholder.querySelector('.sign-placeholder-header').dataset.signId = sign.id;

            // Metemos el id de la firma en el span en el header de la caja de firma
            // Pero en vez del id metemos el nombre del firmante
            placeholder.querySelector('.sign-placeholder-id').innerHTML = sign.signer.name;
          
            // Añade el evento para eliminar el contenedor de firma al presionar la "x"
            placeholder.querySelector('.remove-sign-placeholder').dataset.signId = sign.id;
            placeholder.querySelector('.remove-sign-placeholder').addEventListener('click', this.removeSignPlaceHolder);

            /*
            // Evento para mover la caja por sobre el canvas
            placeholder.querySelector('.move-sign-placeholder').dataset.signId = sign.id
            placeholder.querySelector('.move-sign-placeholder').addEventListener('click', this.dragMouseDown);
            */

            // Si es una zona de firma del creador/autor del documento
            // Añade el evento para limpiar el contenedor de firma al presionar la "papelera"
            if (sign.signer.creator) {
                placeholder.querySelector('.clear-sign-placeholder').dataset.signId = sign.id;
                placeholder.querySelector('.clear-sign-placeholder').addEventListener('click', this.clearSignPlaceHolder);
            } else {
                // Si no se es el creador del documento, el botón de "papelera"
                // para limpiar la zona de firma no aparece
                placeholder.querySelector('.clear-sign-placeholder').remove();
            }

            // Posiciono la caja de firma según en las coordenadas correctas no importa la escala
            const placeholderLeft = $this.canvas.offsetLeft + (sign.x * this.scale);
            const placeholderTop  = $this.canvas.offsetTop  + (sign.y * this.scale);

            //console.log("offsets")
            //console.log(document.querySelector('#pdf').offsetLeft+ ' x ' + document.querySelector('#pdf').offsetTop)

            if (!$this.offsets.left && !$this.offsets.top) {
                // Guardo los offsets del canvas
                $this.offsets.left = $this.canvas.offsetLeft;
                $this.offsets.top  = $this.canvas.offsetTop;
            }

            //console.log("Posicion del marcador de firma");
            console.log(
                `Left => ${placeholderLeft} - Top  => ${placeholderTop}`
            );

            //console.log("Offsets del canvas");
            //console.log(
            //    `Left => ${$this.offsets.left} - Top  => ${$this.offsets.top}`
            //);

            // Añade el nombre del contenedor de firma (código o id de la firma)
            // Pero en vez del signer metemos el id de la firma
            placeholder.querySelector('.signer-name').innerHTML = sign.id;

            // Posiciona el marcador y lo hace visible
            placeholder.style.left    = `${placeholderLeft}px`;
            placeholder.style.top     = `${placeholderTop}px`;
            placeholder.style.display = 'block';

            // Solo muestro los textos relacionados a la firma cuando la
            // escala es mayor o igual a 1
            if (this.scale < 1) {
                placeholder.querySelector('.sign-placeholder-id').style.display = 'none';
                placeholder.querySelector('.signer-name').style.display = 'none';
            }

            // Fija el código/id de la firma en el canvas que contiene el contenedor de firma
            let signCanvas = placeholder.querySelector('.sign');
            signCanvas.dataset.signId = sign.id;

            // Si existe ya una firma la muestra
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

                /**

                    // Carga la imagen de la firma
                    let image = new Image();
                    image.src = sign.sign;

                    // Cuando la imagen ha sido cargada, se dibuja sobre el canvas
                    image.onload = () => {
                        // genero un canvas temporal para ajustar ese imagen a las dimensiones de la caja
                        var canvasTmp = document.createElement('canvas');
                        var ctxTmp    = canvasTmp.getContext('2d');

                        canvasTmp.width  = this.placeholder.canvas.width  * this.scale;
                        canvasTmp.height = this.scale * 150 - 70;

                        // la pinto en el canvas temporal ajustado a las dimensiones de la caja
                        ctxTmp.drawImage(
                            image,
                            0,
                            0,
                            canvasTmp.width,
                            canvasTmp.height,
                        );

                        // ahora tomo esa imagen del canvas temporal y la copio en el canvas minuatura

                        // Tomo el imageData del canvas temporal
                        var imageDataTmp = ctxTmp.getImageData(0, 0, canvasTmp.width, canvasTmp.height);

                        signCanvas
                            .getContext('2d')
                            .putImageData(
                                imageDataTmp,
                                0,
                                0,
                            );
                        
                    }

                */
            }
    
            // Si el contenedor de firma es del propio creador/autor del documento
            // Inicia el componente "SignaturePad" en el mismo para que el propio creador pueda firmar ya sobre el mismo
            if (sign.signer.creator) {
                
                // Activa SignaturePad sobre el contenedor (canvas) de firma
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
                        
                        $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                        
                        /* Actualizo el color del page-number div */
                        if ($this.signs.filter(
                                _sign => _sign.page == sign.page && _sign.signer.creator && !_sign.sign
                            ).length == 0
                        ) {
                            document.querySelector(`#pagesPreview [data-page='${sign.page}'] .page-number`).classList.remove('remaining');
                        }

                        // Actualizo las minuaturas
                        $this.refreshPreview(sign.page);
                    }
                });

                if (!sign.sign) {
                    let message = `${placeholder.dataset.messageForCreator} ${sign.id}`;
                    if (this.scale < 1) {
                        message += `<br>${document.getElementById('messages').dataset.scaleForSign}`;
                    }
                    toastr.warning(message);
                }
            }

            // Añade la firma a la lista de firmas
            document.getElementById('signs').appendChild(placeholder);

            const _draggable = document.querySelector(`#signs > .sign-placeholder[data-sign-id="${sign.id}"] .sign-placeholder-header`);
            // lo hago draggable y resizable sobre el canvas con interact.js
            if (_draggable.parentElement) {
                interact(_draggable)
                    // Hago que el div pueda moverse dentro del documento
                    .draggable({
                        // restricciones de movimiento sobre el documento
                        modifiers: [
                            interact.modifiers.restrictRect({
                                restriction: $this.canvas       // Solo dentro del canvas
                            })
                        ],

                        // eventos
                        listeners: {
                            start (event) {
                                // selecciono la caja
                                $this.indexSign = $this.signs.findIndex(sign => sign.id === event.target.dataset.signId);

                                if (!$this.positionsSigns[$this.signs[$this.indexSign].id]) {
                                    $this.positionsSigns[$this.signs[$this.indexSign].id] = {x:0, y:0};
                                }
                            },

                            move (event) {
                                // Como solo el header de la caja es lo que controla interact.js
                                // debemos restringir el movimiento hacia abajo

                                $this.positionsSigns[$this.signs[$this.indexSign].id].x += event.dx;
                                $this.positionsSigns[$this.signs[$this.indexSign].id].y += event.dy;

                                // Tomo la posición del mouse relativa a la caja que estoy arrastrando
                                let div = document.querySelector(`#signs>.sign-placeholder[data-sign-id='${event.target.dataset.signId}']`);
                                const divRect = div.getBoundingClientRect();
                                const canvasRect = $this.canvas.getBoundingClientRect();
                                let y = (Math.abs(divRect.top - canvasRect.top)) / $this.scale;
                                y = y < 1 ? 1 : parseInt(y);
                                if (y + $this.placeholder.height > $this.canvas.height) {
                                    //div.style.top = `${$this.canvas.height-$this.placeholder.height}px`;

                                    div.style.top =
                                        (parseInt((div.offsetTop - 5) / $this.scale)) + 'px';

                                    return false;
                                } else {
                                    event.target.parentElement.style.transform =
                                    `translate(${$this.positionsSigns[$this.signs[$this.indexSign].id].x}px, ${$this.positionsSigns[$this.signs[$this.indexSign].id].y}px)`;
                                }
                            },

                            end (event) {
                                console.log("ending ...");

                                // $this.signs[$this.indexSign].x = parseInt(x);
                                // $this.signs[$this.indexSign].y = parseInt(y);

                                // Tomo la posición del mouse relativa a la caja que estoy arrastrando
                                let div = document.querySelector(`#signs>.sign-placeholder[data-sign-id='${event.target.dataset.signId}']`);
                                const divRect = div.getBoundingClientRect();
                                const canvasRect = $this.canvas.getBoundingClientRect();

                                const x = (divRect.left - canvasRect.left) / $this.scale;
                                const y = (Math.abs(divRect.top - canvasRect.top)) / $this.scale;

                                $this.signs[$this.indexSign].x = parseInt(x);
                                $this.signs[$this.indexSign].y = parseInt(y);

                                // Actualizo las minuaturas de las firmas sobre la pagina actual
                                $this.refreshPreview($this.page);
                            },
                        }
                    });
            }

            // actualico miniaturas
            this.refreshPreview(this.page);
        },

        /**
         * Dibuja un sello sobre el documento
         * 
         * @param {Stamp} stamp     El sello
         *  
         */
        drawStamp: function (stamp) {

            const $this = this;

            // Carga la imagen en miniatura del sello
            let image = new Image();
            image.src = stamp.stamp.thumb;

            // Cuando la imagen ha sido cargada, se dibuja sobre el canvas
            image.onload = () => {

                // Dibuja un contenedor de sello
                let stampPlaceholder = document.querySelector('.stamp-placeholder.template').cloneNode(true);
                            
                // Elimina la clase plantilla del contenedor de sello
                stampPlaceholder.classList.remove('template');

                // Fija el id del sello estampado
                stampPlaceholder.dataset.stampId = stamp.code;

                // Añade el evento para eliminar el contenedor de sello al presionar la "x"
                stampPlaceholder.querySelector('.remove-stamp-placeholder').dataset.stampId = stamp.code;
                stampPlaceholder.querySelector('.remove-stamp-placeholder').addEventListener('click', this.removeStampPlaceHolder);
                stampPlaceholder.querySelector('.remove-stamp-placeholder').classList.add(`scale-${this.scale}`);

                // Coloca la imagen del sello
                let stampImage = stampPlaceholder.querySelector('.stamp');
                stampImage.setAttribute('src', image.src);
                // y la dimensiona según la escala actual del documento
                stampImage.width  = image.width  * $this.scale;
                stampImage.height = image.height * $this.scale;
                
                // Posiciona el contenedor del sello y lo hace visible
                stampPlaceholder.style.top     = `${stamp.y * $this.scale + $this.canvas.offsetTop}px`;
                stampPlaceholder.style.left    = `${stamp.x * $this.scale + $this.canvas.offsetLeft}px`;

                stampPlaceholder.style.display = 'block';

                // Añade la firma a la lista de sellos estampadps
                document.getElementById('stamps').appendChild(stampPlaceholder);

                // Hago el sello que se pueda mover en el lienzo
                const _draggable = document.querySelector(`#stamps > .stamp-placeholder[data-stamp-id="${stamp.code}"]`);

                // lo hago draggable y resizable sobre el canvas con interact.js
                if (_draggable) {
                    interact(_draggable)
                        // Hago que el div pueda moverse dentro del documento
                        .draggable({
                            // restricciones de movimiento sobre el documento
                            modifiers: [
                                interact.modifiers.restrictRect({
                                    restriction: $this.canvas       // Solo dentro del canvas
                                })
                            ],

                            // eventos
                            listeners: {
                                start (event) {
                                    // selecciono la caja
                                    $this.indexStamp = $this.stamps.findIndex(stamp => stamp.code === event.target.dataset.stampId);
                                    if (!$this.positionsStamps[$this.stamps[$this.indexStamp].code]) {
                                        $this.positionsStamps[$this.stamps[$this.indexStamp].code] = {x:0, y:0};
                                    }
                                    console.log("Stamp ", $this.indexStamp);
                                },

                                move (event) {
                                    $this.positionsStamps[$this.stamps[$this.indexStamp].code].x += event.dx;
                                    $this.positionsStamps[$this.stamps[$this.indexStamp].code].y += event.dy;

                                    //event.target.parentElement.style.transform =
                                    event.target.style.transform =
                                    `translate(${$this.positionsStamps[$this.stamps[$this.indexStamp].code].x}px, ${$this.positionsStamps[$this.stamps[$this.indexStamp].code].y}px)`;
                                },

                                end (event) {
                                    // Actualizo las minuaturas de las firmas sobre la pagina actual
                                    console.log('finalizando movimiento de caja')

                                    // Tomo la posición del mouse relativa a la caja que estoy arrastrando
                                    let div = document.querySelector(`#stamps > .stamp-placeholder[data-stamp-id="${$this.stamps[$this.indexStamp].code}"]`);
                                    const divRect = div.getBoundingClientRect();
                                    const canvasRect = $this.canvas.getBoundingClientRect();

                                    const x = (divRect.left - canvasRect.left) / $this.scale;
                                    const y = (Math.abs(divRect.top - canvasRect.top)) / $this.scale;

                                    $this.stamp.x = parseInt(x);
                                    $this.stamp.y = parseInt(y);

                                    $this.refreshPreview($this.page);
                                },
                            }
                        });
                }

                $this.refreshPreview($this.page);
            };
        },

        /**
         * Abre el explorador de archivos para seleccionar una imagen
         * que pueda servir como sello para estampar sobre el documento
         *
         * @param {Event} event
         * 
         */
        selectFileStamp: function (event) {
            stamp.click();
        },

        /**
         * Sube un nuevo sello
         * 
         * Selecciona un archivo de imagen y lo sube
         */
        uploadStamp: function(event) {
            
            const $this = this;

            // Obtiene el archivo
            const file = event.target.files[0];

            // Comprueba si es un archivo de imagen
            if (!this.isValidFileImage(file)) {
                toastr.error(this.message.fileIsNotImage);
                return false;
            }

            // Muestra la animación
            HoldOn.open({theme: 'sk-circle'});

            // Carga la vista previa de la imagen
            var reader = new FileReader();

            reader.readAsDataURL(file);
            reader.onload = async function (event) {

                // El ancho del sello
                let stampWidth = stamps.dataset.maxWidth; 

                let stamp = 
                    {
                        // Define el nombre del sello, suprimiendo la extensión del archivo
                        name    : file.name.split('.').slice(0, -1).join('.'),
                        stamp   : event.target.result,
                        thumb   : await $this.resizeImage(event.target.result, stampWidth)
                    };
  
                // Envíamos y guardamos el sello en el servidor
                Axios.post(
                    $this.request.saveStamp, 
                        {
                            stamp   : stamp
                        },
                )
                .then(response => {

                    // Oculta la animación
                    HoldOn.close();

                    // Añade el sello a la lista de sellos disponibles
                    let stamp = response.data;      
                    $this.config.stamps.push(stamp);
                })
                .catch(error => {
                    console.error(error);
                });
            };
        },

        /**
         * Elimina un sello disponible para ser estampado en un documento
         * 
         * @param {Stamp} stamp     El sello a eliminar
         *  
         */
        removeStamp: function (stamp) {

            const $this = this;

            // Elimina el sello
            Axios.post(
                $this.request.removeStamp.replace(':id', stamp.id),
            )
            .then(() => {
                // Elimina el sello de la lista de sellos disponibles
                this.config.stamps = this.config.stamps.filter(_stamp => stamp.id != _stamp.id);
            })
            .catch(error => {
                console.error(error);
            });
        },

        /**
         * Comprueba si un archivo es una imagen válida
         *       
         * Para los sellos estampados se recomienda utilizar PNG o GIF que
         * adminiten transparencia al disponer de canal alpha
         *
         * @param {String} file     Un archivo
         *
         * @return {Boolean}        true si es una imagen admitida 
         */
        isValidFileImage: function (file) {
            // Obtiene los tipos mime de imágenes admitidas 
            const acceptedImageTypes = JSON.parse(stamps.dataset.mimes);

            return file && acceptedImageTypes.includes(file['type'])
        },

        /**
         * Limpia un contenedor de firma
         * 
         * Ocurre cuando se presiona el botón "papelera" del contenedor
         * 
         * @param {Event} event
         */
        clearSignPlaceHolder: function (event) {
            const $this = this;
            
            // Obtiene el id del contenedor de firma a limpiar
            let signId = event.currentTarget.dataset.signId;

            // Encuentra el contedor de firma y lo limpia
            const found = this.signs.find(sign => sign.id === signId);

            found.sign = null;
            found.pad.clear();

            $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                            
            /* Actualizo el color del page-number div */
            if ($this.signs.filter(
                    _sign => _sign.page == found.page && _sign.signer.creator && !_sign.sign
                ).length == 0
            ) {
                document.querySelector(`#pagesPreview [data-page='${found.page}'] .page-number`).classList.remove('remaining');
            }

            // Actualizo las minuaturas
            $this.refreshPreview(found.page);
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
         * Dibuja todos los sellos estampados en una página del documento
         * 
         * @param {Number} page     El número de página
         */
        drawStampsInPage: function (page) {
            
            // Elimina los contenedor de sellos estampados que pueda haber
            this.clearStampPlaceholders();

            // Coloca los sellos estampados en la página
            this.stamps.forEach(stamp => {
                if (stamp.page == page) {
                    this.drawStamp(stamp);
                }
            });
        },

        /**
         * Elimina un contenedor de firma
         * 
         * Ocurre cuando se presiona la "x" del contenedor de firma
         * 
         * @param {Event} event
         */
        removeSignPlaceHolder: function (event) {
            // Obtiene el id del contenedor de firma a eliminar
            let signId = event.currentTarget.dataset.signId;

            // Elimina el contenedor de firma de id dado
            document.querySelector(`[data-sign-id="${signId}"]`).remove();

            // Obtengo el índice en el arreglo de firmas
            const index = this.signs.findIndex(sign => sign.id === signId);

            // Actualizo la miniatura de la página a la que pertenece la firma
            this.refreshPreview(this.signs[index].page);

            // y de la lista de firmas
            this.signs.splice(index, 1);
        },

        /**
         * Elimina un contenedor del sello estampado
         * 
         * Ocurre cuando se presiona la "x" del contenedor del sello
         * 
         * @param {Event} event
         */
        removeStampPlaceHolder: function (event) {
            
            // Obtiene el id del contenedor de sello a eliminar
            let stampId = event.currentTarget.dataset.stampId;

            // Elimina el contenedor de firma de id dado
            document.querySelector(`[data-stamp-id="${stampId}"]`).remove();

            // y de la lista de sellos estampados
            this.stamps.splice(this.stamps.findIndex(stamp => stamp.id === stampId), 1);

            this.refreshPreview(this.page);
        },

        /**
         * Elimina todos los contenedores de firma del documento
         *
         */
        clearSignPlaceholders: function () {
            document.getElementById('signs').innerHTML  = '';
        },

        /**
         * Elimina todos los contenedores de sello del documento
         *
         */
        clearStampPlaceholders: function () {
            document.getElementById('stamps').innerHTML  = '';
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
         * Muestra la paǵina del documento actual
         */
        render: function () {
            const $this = this;

            // clear the canvas
            if ($this.context) {
                //$this.context.clearRect(0, 0, $this.canvas.width, $this.canvas.height);
                $this.clearCanvas();
            }

            $this.pdf.getPage($this.page).then(function(page) {

                /**
                 * Determina el viewport del documento
                 */
                $this.viewport = page.getViewport({ scale: 1, });
                 
                if (!$this.viewportI[$this.page]) {
                    $this.viewportI[$this.page] = $this.viewport;
                }

                //console.log('Tamanno de imagen de pagina de PDF')
                //console.log('Ancho x Alto')
                //console.log($this.viewportI[$this.page].width + ' x ' + $this.viewportI[$this.page].height)

                // La escala del canvas es el ancho del visor del documento entre el ancho
                // del la imagen de la página del pdf
                const docWidth = parseInt($this.$refs.document.clientWidth);
                //console.log('Ancho del documento DIV : ' + docWidth);

                // Si se debe ajustar el doc a la pantalla calcular la escala
                //console.log("Se debe ajustar a la pantalla ?");

                if ($this.fit) {
                    //console.log("Recalculando escala ...");
                    $this.scale = Math.round( (docWidth / $this.viewportI[$this.page].width) * 100, 2) / 100; 
                    //console.log('Escala calculada');
                    //console.log(`Math.round( (${docWidth} / ${$this.viewportI[$this.page].width}) * 100, 2) / 100`);
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
                $this.canvas  = document.getElementById('pdf');
                $this.context = $this.canvas.getContext('2d');

                /**
                 * Dimensiona el elemento canvas en función del viewport
                 */
                $this.canvas.width  = $this.viewport.width;
                $this.canvas.height = $this.viewport.height;

                // Obtiene la posición en el canvas
                // var rect = $this.canvas.getBoundingClientRect();
               
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

                // Dibuja los contenedores de firma
                $this.drawSignPlaceholdersInPage($this.page);
                
                // Dibuja los sellos estampados
                $this.drawStampsInPage($this.page);
            });
        },

        /**
         * Guarda la configuración de firma del documento
         *
         * Se guardan todas las firmas consignadas sobre el documento
         * 
         */
        saveDocument: function () {
           
            const $this = this;

            // Comprueba que firmantes han sido seleccionados y cuales no
            // es decir, para que firmantes se ha establecido una posición de firma y para cuales no
            //
            // El usuario creador del documento puede optar como alternativa a la firma manuscrita,
            // a sellar el documento, estampando uno sobre el mismo
            // 
            $this.signers.forEach(signer => 
                {
                    signer.selected = $this.signs.filter(sign => sign.signer.id == signer.id).length > 0
                                                            ||
                                    (signer.creator && $this.stamps.length > 0);
                }
            );

            //
            // Para todos los firmantes que se han definido en el paso anterior
            // debe establecerse al menos un cuadro o posición de firma
            // En caso contrario no se permite guardar el documento y se muestra el error
            //
            // Por tanto, para finalizar el proceso todos los firmantes han tenido que ser seleccionados
            //
            if ($this.signers.length != $this.signers.filter(signer => signer.selected).length) {
                // Abre la modal que muestra el error
                // que indica que falta de especificar firma/firmas para algun/algunos de los firmantes
                $this.$bvModal.show('number-of-signers-not-mismatch');

                return;
            }

            // Compruebo que todas las firmas del creador se hayan realizado
            if ($this.creatorSigns.filter(sign => sign.sign !== null).length !== $this.creatorSigns.length) {
                // Obtengo la pagina de la primera que debe firmar
                toastr.options.onclick = () => {
                    $this.goPage($this.creatorSigns.filter(sign => sign.sign === null)[0].page);
                }
                toastr.error(document.getElementById('messages').dataset.creatorMustFinished);
                return;
            }
            
            // Inicia animación
            HoldOn.open({theme: 'sk-circle'});
    
            axios.post($this.request.saveDocument, {
                signs   : $this.signs,      // Las firmas
                stamps  : $this.stamps,     // Los sellos estampados
            })
            .then(response => {
                // Detiene la animación
                HoldOn.close();

                if (response.data.code == 1) {
                    // Obtiene el documento guardado
                    $this.document = response.data.document;

                    console.info(`[OK] Se ha guardado con éxito el documento ${$this.document.guid}`);

                    // Abre la modal con la ayuda
                    $this.$bvModal.show('document-sign-config-success');
                } else {
                    toastr.error("Error !!");
                }
            })
            .catch (error => {
                // Detiene la animación
                HoldOn.close();
                console.error(error);
            });
        },
        
        /**
         * Redirige después de guardar el documento 
         * 
         * @param {Event} event
         *  
         */
        redirectAfterSave: function (event) {

            // Guardar el documento preparado
            const request = document.getElementById('pdf').dataset.redirectTo;
            location.href = request;
        },

        
    },

    /* Variables computadas */
    computed: {

        /*
         * Devuelve las firmas que debe realizar el creador del documento 
         */
        creatorSigns: function () {
            return this.signs.filter(sign => sign.signer.creator === true);
        },

        /*
         * Devuelve las paginas que contienen firmas en el doc
         */
        pagesWithSigns: function () {
            return [...new Set(this.signs.map(sign => sign.page))];
        },

    },

    /* Filtros */
    filters: {
        /**
         * Redondea un valor al entero más próximo
         * 
         * @param {Number} value  Un valor numérico
         * 
         * @return {Number}       El valor redondeado al entero máx próximo
         * 
         */
        int: function (value) {
            return Math.round(value);
        },
        
        /**
         *Expresa una fecha
         * 
         * @param {Date} value    Una fecha
         * 
         * @return {String}       Una representación de la fecha
         * 
         */
        date: function (value) {
            return moment(value).format('DD-MM-YYYY');
        },
    }
});
