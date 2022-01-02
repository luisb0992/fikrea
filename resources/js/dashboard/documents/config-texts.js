/**
 * Configuración de las cajas de textos del documento
 * 
 * El creador establece unas zonas del documento con cajas de textos que deben ser completadas
 * por los usuarios que el seleccione, o por el mismo en algunos casos
 * 
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

// regla para restricciones de caja
const RULEBOX = {
        minLength: 1,
        maxLength: 0,
        numbers:   true,
        letters:   true,
        specials:  true,
    };

new Vue({
    el: '#app',
    data: {
        document    :               // El documento que se procesa
            {
                guid: undefined,
            },
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

        signers     : [],           // La lista de firmantes del documento
       
        textboxs    : [],           // Las cajas de texto que deben completarse por todos
                                    // Es una lista de cajas de textos
      
        indexBox    : -1,            // Indice de la caja de texto 'box' en el array de cajas 'textboxs'

        
        // Definición de una caja de texto
        box         : {
                id      : null,     // El id del contenedor de la caja
                signer  :           // El firmante que debe completar la info en ese lugar
                    {
                        id      : null,   // El id del firmante
                        name    : null,   // El nombre, email o teléfono del firmante
                                          // según los datos que se proporcionaron
                        creator : false,  // Si el firmante es el creador del documento o no
                    },
                page    : null,     // La página
                x       : null,     // La coordenada "x" dentro de la página
                y       : null,     // La coordenada "y" dentro de la página
                text    : '',       // El texto,
                type    : 4,        // El tipo de input que se debe mostrar en la caja
                                    // 1 - iniciales
                                    // 2 - nombre completo
                                    // 3 - número de identificación
                                    // 4 - texto libre
                                    // 5 - casilla de verificación
                                    // 6 - lista de opciones
                title  : null,      // Título de la caja de texto para el firmante externo
                width  : 185,       // Ancho de la caja, se puede modificar
                height : 100,       // Alto de la caja, NO SE PUEDE MODIFICAR
                rules  : {},        // Reglas de restricciones o limitaciones de los textos
                options: '',        // Listado de opciones para cajas tipo select, separadas por ';'
                shiftX : 0,         // Posición relativa de la caja de texto con el div que lo contiene según left
                shiftY : 0,         // Posición relativa de la caja de texto con el div que lo contiene según top
                fitMaxLength: true,// Ajustar la cantidad máxima de caracteres al ancho de la caja de texto
                initMaxLength: null,// Tamaño máximo de texto inicial, para recuperar este valor en algún momento
                moreDetails: false, // Muestra vista ampliada de la caja de texto, con info de firmante e id
            },
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
        
        // Controla si se está cargando el pdf
        rendering : true,

        // Función que se ejecuta cada 1 segundo para controlar el resize de la ventana
        resizeTo : null,

        // Función que se ejecuta cada 1 segundo para controlar el resize de la caja de texto
        resizeBox : null,

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
            x:0,
            y:0,
            clientX:0,
            clientY:0,
            canvasLeft:0,
            canvasTop:0,
        },

        movementPlaceholder: null,  // Div que se está moviendo sobre el canvas
        moving:false,               // Para controlar el movimiento de la caja de firma
        positions : {
            clientX : 0,
            clientY : 0,
            movementX : 0,
            movementY : 0,
        },

        items: null,        // Elementos div que representan una caja de texto
        dragging: {         // Elemento que se esta arrastrando sobre el documento
            shiftX : 0,
            shiftY : 0,
        },

        langs      : null,  // Textos traducidos 

        positionsBoxs : [],     // Lista de par x,y para controlar el movimiento de cada caja

        newOptionText: '',      // Opción para adicionar en caja de texto tipo select

        desktop: true,          // Si estoy en desktop o móvil

    },

    /**
     * Cuando tenemos la instancia montada
     *
     */
    mounted: function () {

        const $this = this;

        $this.fit = false;

        // Carga el o los firmantes del documento
        $this.signers = JSON.parse(document.getElementById('signers').dataset.signers);

        // Si estoy en desktop o móvil
        $this.desktop = JSON.parse(document.getElementById('data').dataset.desktop);

        // Carga los textos traducidos
        $this.langs = JSON.parse(document.getElementById('langs').dataset.langs);
    
        // Añadimos la propiedad "selected" que indica si un firmante ya ha sido seleccionado
        // Es decir, si se ha definido ya al menos una posición o cuadro de firma para el mismo
        //
        // Antes de finalizar el proceso habrá que verificar que todos los firmantes han sido seleccionados
        $this.signers.forEach(signer => signer.selected = false);
          
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
            $this.loadBoxs()
                .then(() => {
                    
                    // Carga la paǵina del documento
                    $this.render();

                    // Abre la modal con la ayuda
                    $this.$bvModal.show('help-textboxs-document');
                })
                .catch(error => {
                    console.error("Error al cargar documento PDF");
                    console.error(error);
                });
        });

        $this.captureResizeWindowEvent();

        // Cajas de textos
        $this.items = document.querySelectorAll('.box');

        $this.items.forEach(box => {
            box.addEventListener('dragstart',  $this.handleDragStart,   false);
            box.addEventListener('dragend',    $this.handleDragEnd,     false);
            box.addEventListener('dragenter',  $this.handleDragEnter,   false);
            box.addEventListener('dragleave',  $this.handleDragLeave,   false);
            box.addEventListener('dragend',    $this.handleDragEnd,     false);

            box.addEventListener('touchstart',  $this.handleDragStart,   false);
            box.addEventListener('touchend',    $this.handleDragEnd,     false);
            box.addEventListener('touchenter',  $this.handleDragEnter,   false);
            box.addEventListener('touchleave',  $this.handleDragLeave,   false);
            box.addEventListener('touchend',    $this.handleDragEnd,     false);
        });

        // Al hacer click sobre el cuerpo de la página elimino la selección de la caja
        document.body.onclick = () => {
            //$this.selectedBox = false;        // Des-selcciono la caja de texto
        };
    },

    methods: {
        /*
         * Configuración inicial de una caja de texto
         */
        initBox: function () {
            // Genera un código/id único para cada firma
            const code = Math.random().toString(16).substring(2);

            this.box = {
                id      : code,     // El id del contenedor de la caja
                signer  :           // El firmante que debe completar la info en ese lugar
                    {
                        id      : null,   // El id del firmante
                        name    : null,   // El nombre, email o teléfono del firmante
                                          // según los datos que se proporcionaron
                        creator : false,  // Si el firmante es el creador del documento o no
                    },
                page    : null,     // La página
                x       : null,     // La coordenada "x" dentro de la página
                y       : null,     // La coordenada "y" dentro de la página
                text    : '',       // El texto,
                options: '',        // Listado de opciones para cajas tipo select, separadas por ';'
                type    : 4,        // El tipo de input que se debe mostrar en la caja
                                    // 1 - iniciales
                                    // 2 - nombre completo
                                    // 3 - numero de identificacion
                                    // 4 - texto libre
                                    // 5 - casilla de verificacion
                                    // 6 - lista de opciones
                title  : null,      // Título de la caja de texto para el firmante externo
                width  : 185,       // Ancho de la caja, se puede modificar
                height : 100,       // Alto de la caja, NO SE PUEDE MODIFICAR
                shiftX : 0,         // Posición relativa de la caja de texto con el div que lo contiene según left
                shiftY : 0,         // Posición relativa de la caja de texto con el div que lo contiene según top
                fitMaxLength: true,// Ajustar la cantidad máxima de caracteres al ancho de la caja de texto
                initMaxLength: null,// Tamaño máximo de texto inicial, para recuperar este valor en algún momento
                moreDetails: false, // Muestra vista ampliada de la caja de texto, con info de firmante e id
            };
        },

        /*
         * Drag and drop functions
         */

        // Inicio del drag
        handleDragStart: function(e) {
            const $this = this;
            
            console.log(e);

            e.target.style.opacity = '0.4';

            // Tomo la posición del mouse relativa a la caja que estoy arrastrando
            let div = document.getElementById(e.target.id);
            const bondings = div.getBoundingClientRect();

            $this.dragging.shiftX = event.clientX - bondings.left;
            $this.dragging.shiftY = event.clientY - bondings.top;

            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('data', e.target.id);
        },

        // Final del drag
        handleDragEnd: function(e) {
            e.target.style.opacity = '1';

            this.items.forEach(item => {
                item.classList.remove('over');
            });
        },

        handleDragOver: function (e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            
            return false;
        },

        handleDragEnter: function (e) {
            e.target.classList.add('over');
        },

        handleDragLeave: function (e) {
            e.target.classList.remove('over');
        },

        handleDrop: function (e) {
            const $this = this;

            e.stopPropagation();            // stops the browser from redirecting.

            let type = parseInt(document.getElementById(e.dataTransfer.getData("data")).dataset.type) || 3;

            // Obtiene la posición en el canvas
            var rect = $this.canvas.getBoundingClientRect();
            
            $this.mouse.clientX = e.clientX;
            $this.mouse.clientY = e.clientY;

            $this.mouse.canvasLeft = parseInt(rect.left);
            $this.mouse.canvasTop  = parseInt(rect.top);

            // La posición de la caja es la posición del mouse - posición del mouse
            // relativa a la caja que se está arrastrando
            // Todo escalado sino no se posiciona correctamente...
            $this.mouse.x = parseInt(((e.clientX - rect.left) / $this.scale) - ($this.dragging.shiftX / $this.scale));        //+1
            $this.mouse.y = parseInt(((e.clientY - rect.top)  / $this.scale) - ($this.dragging.shiftY / $this.scale));

            $this.mouse.x = $this.mouse.x < 1 ? 1 : $this.mouse.x > $this.canvas.width ? $this.canvas.width : $this.mouse.x;
            $this.mouse.y = $this.mouse.y < 1 ? 1 : $this.mouse.y > $this.canvas.height ? $this.canvas.height : $this.mouse.y;

            // Genera un código/id único para cada firma
            const code = Math.random().toString(16).substring(2);

            $this.box = 
                {
                    // Se genera un id único para cada caja de texto de firma
                    id      : code,
                    code    : code,
                    signer  : 
                        {
                            id      : null,
                            name    : null,
                            creator : false
                        },
                    page    : $this.page,
                    x       : $this.mouse.x,
                    y       : $this.mouse.y,
                    text    : '',       // El texto de la caja
                    options: '',        // Listado de opciones para cajas tipo select, separadas por ';'
                    type    : type,
                    title   : null,     // Título de la caja de texto para el firmante externo
                    width  : 185,       // Ancho de la caja, se puede modificar
                    height : 100,       // Alto de la caja, NO SE PUEDE MODIFICAR
                    rules  : {...$this.rules[type - 1]},    // Reglas de restricciones
                    shiftX : 0,         // Posición relativa de la caja de texto con el div que lo contiene según left
                    shiftY : 0,         // Posición relativa de la caja de texto con el div que lo contiene según top
                    fitMaxLength: true,// Ajustar la cantidad máxima de caracteres al ancho de la caja de texto
                    initMaxLength: null,// Tamaño máximo de texto inicial, para recuperar este valor en algún momento
                    moreDetails: false, // Muestra vista ampliada de la caja de texto, con info de firmante e id
                };

            // Si hay varios firmantes a elegir, abrir la modal para seleccionar el firmante
            if (this.signers.length > 1) {
                this.$bvModal.show('select-signer');
            } else {
                // Inserta la posición de firma directamente
                this.insertBox();
            }

            return false;
        },

        /**
         * Oculta o muestra los detalles de la caja de firma
         */
        showHideDetailsBox: function (box) {
            let placeholder = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${this.box.id}"] input`)
            placeholder = !placeholder? document.querySelector(`#textboxs > .box-placeholder[data-box-id="${this.box.id}"] select`) : placeholder;

            if (box.moreDetails) {
                // Muestro los divs ocultos del id y el firmante de la caja de firma
                placeholder
                    .parentElement
                        .parentElement
                            .querySelector('.box-placeholder-header').classList.remove("d-none");

                placeholder
                    .parentElement
                        .parentElement
                            .querySelector('.signer-name').classList.remove("d-none");
            } else {
                // Oculto los divs mostrados del id y el firmante de la caja de firma
                placeholder
                    .parentElement
                        .parentElement
                            .querySelector('.box-placeholder-header').classList.add("d-none");

                placeholder
                    .parentElement
                        .parentElement
                            .querySelector('.signer-name').classList.add("d-none");
            }
        },

        /**
         * Limpia el texto de una caja
         */
        clearBox: function (box) {
            let placeholder = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${this.box.id}"] input`)
            if (placeholder) {
                if (placeholder.type == 'text') {
                    placeholder.value = '';
                } else {
                    // es un checkbox
                    placeholder.checked = 0;
                }                    
            } else {
                placeholder = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${this.box.id}"] select`);
                placeholder.selectedIndex = 0;
            }
            this.$forceUpdate();
            this.textboxs.filter(_box => box.id == _box.id)[0].text = '';

        },

        /**
         * Elimina una caja
         */
        deleteBox: function (box) {
            try {
               const $this = this;

                if ($this.box && box.id == $this.box.id) {
                    $this.initBox();
                    $this.selectedBox = false;
                }

                // Obtengo el índice en el arreglo de cajas
                const index = $this.textboxs.findIndex(_box => _box.id === box.id);

                if (index != -1) {
                    const pageBox = $this.textboxs[index].page;

                    // Elimina el contenedor de firma de id dado
                    document.querySelector(`#textboxs [data-box-id="${$this.textboxs[index].id}"]`).remove();

                    // Elimino la caja del array
                    $this.textboxs.splice(index, 1);

                    // Actualizo la miniatura de la página a la que pertenece la firma
                    $this.refreshPreview(pageBox);

                    // Elimino el li de la lista de las cajas de creador
                    document.querySelector(`#creatorBoxs li[data-box-id="${boxId}"]`).remove();
                }
            }
            catch (error) {}
            finally {}
        },

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

        /**
         * Actualiza la miniatura de la página dada posicionando las cajas de firma
         * que se han configurado sobre ella
         *
         * Actualizo cuando estoy en desktop
         *
         * @param {Number} page     Un número de página
         */
        refreshPreview: async function (page) {
            const $this = this;

            if (!$this.desktop) {
                return;
            }

            console.log("Actualizando minuatura de página => " + page);

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

                                        // box.text == 'false' para cuando es un checkbox
                                        if (box.signer.creator) {
                                            if (!box.text || box.text == 'false') {
                                                creatorPendingOnPage = true;
                                            }
                                        }

                                        if (box.text) {
                                            const fontSize = parseInt($this.canvas.width / $this.getFontSize()) - 4;
                                            ctx.font = `${fontSize}px Arial`;

                                            // Pinto rectángulo de color
                                            ctx.fillStyle = `${color}`;
                                            ctx.fillRect(box.x + box.shiftX, box.y + box.shiftY, box.width - box.shiftX, 23);
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
                    // Elimina los contenedor de textos que pueda haber
                    $this.clearBoxsPlaceholders();

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
            this.$bvModal.hide('help-textboxs-document');
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

            // Obtiene la posición en el canvas
            var rect = $this.canvas.getBoundingClientRect();

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

            // El ancho y alto del cuadro de firma
            let signPlaceholder =
                {
                    width  : this.textbox.width,
                    height : this.textbox.height,
                };

            // Si la posición del cuadro de firma que se va a dibujar excede los límites de la pantalla
            // se corrige la posición para que el cuadro de firma se ajuste a esos límites
            if (mouse.x + signPlaceholder.width > page.width) {
                mouse.x = (page.width - signPlaceholder.width);// / this.scale;
            }

            if (mouse.y + signPlaceholder.height > page.height) {
                mouse.y = (page.height - signPlaceholder.height);// / this.scale;
            }
            
            return mouse;
        },

        /**
         * Inserta un elemento en la posición actual del ratón
         * 
         * @param {Event} event 
         * 
         */
        select: function (event) {
            const $this = this;
            $this.selectedBox = false;        // Des-selcciono la caja de texto

            // Obtiene la posición en el canvas
            $this.canvas.getBoundingClientRect();

            // Genera un código/id único para cada firma
            const code = Math.random().toString(16).substring(2);

            // Obtiene la posición actual del ratón
            let mouse = $this.getMousePositionOverDocument(event);

            const defaultType = 4;      // Texto libre

            $this.box = 
                {
                    // Se genera un id único para cada caja de texto de firma
                    id      : code,
                    code    : code,
                    signer  : 
                        {
                            id      : null,
                            name    : null,
                            creator : false
                        },
                    page    : $this.page,
                    x       : mouse.x,
                    y       : mouse.y,
                    text    : '',       // Texto de la caja
                    options:'',        // Listado de opciones para cajas tipo select, separadas por ';'
                    title   : null,     // Título de la caja de texto para el firmante externo
                    type    : defaultType,// El tipo de input que se debe mostrar en la caja
                    width   : 185,       // Ancho de la caja, se puede modificar
                    height  : 100,       // Alto de la caja, NO SE PUEDE MODIFICAR
                    rules   : {...$this.rules[defaultType-1]},// Reglas de restricciones
                    shiftX  : 0,         // Posición relativa de la caja de texto con el div que lo contiene según left
                    shiftY  : 0,         // Posición relativa de la caja de texto con el div que lo contiene según top
                    fitMaxLength: true,// Ajustar la cantidad máxima de caracteres al ancho de la caja de texto
                    initMaxLength: null,// Tamaño máximo de texto inicial, para recuperar este valor en algún momento
                    moreDetails: false, // Muestra vista ampliada de la caja de texto, con info de firmante e id
                };

            // Si hay varios firmantes a elegir, abrir la modal para seleccionar el firmante
            if (this.signers.length > 1) {
                this.$bvModal.show('select-signer');
            } else {
                // Inserta la posición de firma directamente
                this.insertBox();
            }
        },

        /**
         * Carga las firmas previamente guardadas del documento
         * 
         * @return {Promise}    Una promesa
         */
        loadBoxs: function () {
            const $this = this;

            // La url de la petición para obtener las cajas de texto
            const requestBoxs = document.getElementById('pdf').dataset.boxs;

            return new Promise((resolve, reject) => {
                axios.post(requestBoxs)
                    .then(response => {
                        // Obtiene las cajas de texto
                        let boxs = response.data;
                        
                        boxs.forEach(box => {
                            // Establece los datos del firmante
                            box.signer =
                                {
                                    id      : box.signer_id,       // El id del firmante
                                    name    : box.signer,          // El nombre, email o teléfono del firmante
                                                                   // según los datos que se proporcionaron
                                    creator : box.creator == '1'   // true si la firma es del creador/autor del documento
                                                                   // false en caso contrario
                                };
                            // Estable el id de la caja
                            box.id     = box.code;
                        });

                        $this.boxs = boxs;

                        resolve(boxs);
                    })
                    .catch (error => {
                        reject(error);
                    });
            });
        },

        /**
         * Reutilizo funcionalidad de modal selec-signer que llama al método 
         * insertSign al aceptar
         */
        insertSign: function () {
            // Chequeo que se haya seleccionado el tipo de caja a insertar
            // Para cuando se está en móvil
            console.log("verificando tipo")
            console.log(this.box.type);

            // Oculta la modal de selección de firmante
            this.$bvModal.hide('select-signer');

            // Si estoy en movil muestro modal para seleccion de tipo de caja a insertar
            if (!this.desktop && this.box.type == 4) {
                this.$bvModal.show('select-box-type');
            } else {
                this.insertBox();
            }
        },

        /**
         * Inserta la caja de texto en el lugar del documento indicado,
         * es decir, en una posición determinada dentro de una paǵina
         */
        insertBox: function () {
            // Obtiene el firmante seleccionado
            const selected = document.getElementById('signer');

            // Si se ha seleccionado un firmante entre la lista de firmantes
            if (selected) {
                // Se obtiene el firmante seleccionado
                this.box.signer = this.signers.find(signer => signer.id ==  selected.value);  
            } else {
                // Si no hay firmante seleccionado se selecciona el primero de ellos
                this.box.signer = this.signers.find(Boolean);
            }

            // La caja estará inicialmente vacía 
            if (this.box.signer.creator ) {
                // Al ser del creador marcamos la página mini como que debe ser cumplimentada
                document.getElementById('pagesPreview').querySelector(
                    `[data-page='${this.box.page}'] .page-number`
                ).classList.add('remaining');
            } else {
                // Cuando la caja es de un firmante externo le asignamos como título su tipo
                this.box.title = this.langs[this.box.type-1];
            }

            this.box.text = '';         // Texto inicialmente vacía

            // Añade la caja a la lista de cajas de texto
            this.textboxs.push(this.box);

            // Oculta la modal
            this.$bvModal.hide('select-signer');
            this.$bvModal.hide('select-box-type');

            // Si es un checkbox se pone en false
            if (this.box.type == 5) {
                this.box.text = 'false';
            }

            // Dibuja el contenedor de la caja de texto
            this.drawBoxPlaceholder(this.box);

            // Actualizo las minuaturas de las firmas sobre la página actual
            //this.refreshPreview(this.page);
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

        changeMaxLength: function (box) {
            const $this = this;

            let inputField = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"] input`)
            if (inputField) {
                /*
                    171 - 20
                    ?   - Y=box.rules.maxLength
                    ? = Y * 171 / 20
                */

                let width = box.rules.maxLength * 171 / 20;

                Object.assign(inputField.style, {
                    width:  `${width}px`,
                });

                let placeholder = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"]`);
                if (placeholder) {
                    Object.assign(placeholder.style, {
                        width:  `${width + 11}px`,
                    });
                }
            }
        },

        /**
         * Dibuja un contenedor de caja de texto
         * 
         * @param {Sign} sign   Una caja de texto 
         */
        drawBoxPlaceholder: function (box) {
            console.log("pintando caja de texto para " + box.id)
            const $this = this;

            // Clono la caja de muestra
            // Toma la plantilla del contenedor de firma y la clona
            let placeholder = document.querySelector('.box-placeholder.template').cloneNode(true);

            // Elimina la clase plantilla del contenedor de firma clonado
            placeholder.classList.remove('template');

            // Define el color del contenedor de firma
            // A cada firmante se le asigna un color definidos por la sucesión de clases: [color-0 .. color-9]
            placeholder.classList.add(`color-${box.signer.id % 10}`);

            const placeholderWidth  = parseInt(box.width  * this.scale);
            const placeholderHeight = parseInt(box.height * this.scale);

            // Define la anchura y altura del contenedor de firma en función del grado de zoom (escala)
            //placeholder.style.width  = `${placeholderWidth}px`;             // Ancho por defecto
            //placeholder.style.height = `${placeholderHeight}px`;

            // Añade el evento para eliminar el contenedor de la caja al presionar la "x"
            placeholder.querySelector('.remove-box-placeholder').dataset.boxId = box.id;
            placeholder.querySelector('.remove-box-placeholder').addEventListener('click', this.removeBoxPlaceHolder);

            // Añade el evento para eliminar el texto introducido de la caja al presionar la "trash"
            placeholder.querySelector('.clear-box-placeholder').dataset.boxId = box.id;
            placeholder.querySelector('.clear-box-placeholder').addEventListener('click', (e) => {
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
                this.textboxs.filter(_box => e.currentTarget.dataset.boxId == _box.id)[0].text = '';
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
            // deshabilito cuando no es del creador o cuando no es un select
            let disabled = box.signer.creator || box.type == 6 ? '':'disabled';
            let value = box.text ? `value="${box.text}"`:'';
            
            const charsInBox = $this.charsInBox(box);           // Restricción de caracteres en la caja

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
                input = `<input data-box-id="${box.id}" type="checkbox" ${disabled} ${checked} /> <label>${$this.langs[4]}</label>`;
            } else if (boxType == 6) {
                input = `<select data-box-id="${box.id}" ${disabled}>
                    <option value='-1'>${$this.langs[5]}</option>
                </select>`;
            } else {
                input = `<input data-box-id="${box.id}" type="text" minlength="1" maxlength="${charsInBox}" ${disabled} placeholder="${$this.langs[3]}" />`;
            }

            // Recalculo el ancho según el tipo de caja de texto
            if ([1,2,3,4].indexOf(boxType) != -1) {
                /*
                171 - 20
                    ?   - Y=box.rules.maxLength
                    ? = Y * 171 / 20
                */
                var width = 171 * box.rules.maxLength/20;
                placeholder.style.width = `${width + 11}px`;    
            }

            placeholder.querySelector('.box-placeholder-body .box').outerHTML = input;

            // Añade la firma a la lista de firmas
            document.getElementById('textboxs').appendChild(placeholder);

            // Calculo los offsets respecto a la caja contenedora del input donde va el texto
            const parentRect = placeholder.getBoundingClientRect();
            let inputField = placeholder.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"] input`);
            inputField = inputField ? inputField : placeholder.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"] select`);
            
            const inputRect = inputField.getBoundingClientRect();

            // La diferencia del hijo respecto a su elemento padre
            box.shiftX = parseInt(inputRect.x - parentRect.x);
            box.shiftY = parseInt(inputRect.y - parentRect.y);

            // el font-size de los inputs texts se debe calcular según la escala para que siempre
            // permita la entrada de los mismos caracteres
            // font-size(px) = parseInt(input.width / 40.4)
            inputField.style.fontSize = `${$this.getFontSize($this.scale)}px`;
            
            // lo hago draggable y resizable sobre el canvas con interact.js
            interact(`#textboxs > .box-placeholder[data-box-id="${box.id}"]`)
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
                            //console.log(event.type, event.target)
                            // selecciono la caja de texto
                            // Obtengo el índice en el arreglo de cajas
                            $this.indexBox = $this.textboxs.findIndex(box => box.id === event.target.dataset.boxId);
                            $this.box = $this.textboxs[$this.indexBox];

                            if (!$this.positionsBoxs[$this.box.id]) {
                                 $this.positionsBoxs[$this.box.id] = {x:0, y:0};
                            }
                        },

                        move (event) {
                            $this.positionsBoxs[$this.box.id].x += event.dx;
                            $this.positionsBoxs[$this.box.id].y += event.dy;

                            event.target.style.transform =
                                `translate(${$this.positionsBoxs[$this.box.id].x}px, ${$this.positionsBoxs[$this.box.id].y}px)`;
                        },

                        end (event) {
                            //console.log("ending ...");

                            // Tomo la posición del mouse relativa a la caja que estoy arrastrando
                            let div = document.querySelector(`#textboxs>.box-placeholder[data-box-id='${$this.box.id}']`);
                            const divRect = div.getBoundingClientRect();
                            const canvasRect = $this.canvas.getBoundingClientRect();

                            const x = (divRect.left - canvasRect.left) / $this.scale;
                            const y = (Math.abs(divRect.top - canvasRect.top)) / $this.scale;

                            $this.box.x = parseInt(x);
                            $this.box.y = parseInt(y);

                            // Actualizo las minuaturas de las firmas sobre la pagina actual
                            $this.refreshPreview($this.page);
                        },
                    }
                // Hago que el div pueda cambiarse de tamaño con el mouse solo por la derecha
                }).resizable({
                    // restricciones de movimiento sobre el documento
                    modifiers: [
                        interact.modifiers.restrictSize({
                            min: { width: (width + 11) * this.scale, height: 100 * this.scale },
                            //max: { width: maxWidth * this.scale, height: 100 * this.scale }
                        }),
                        interact.modifiers.restrictRect({
                            restriction: $this.canvas       // Solo dentro del canvas
                        }),
                    ],
                    // esquinas
                    edges: { top: false, left: false, bottom: false, right: true },
                    // eventos
                    listeners: {
                        start (event) {
                            // selecciono la caja de texto
                            // Obtengo el índice en el arreglo de cajas
                            $this.indexBox = $this.textboxs.findIndex(box => box.id === event.target.dataset.boxId);
                            $this.box = $this.textboxs[$this.indexBox];
                        },

                        move: function (event) {

                            let x = (parseFloat(x) || 0) + event.deltaRect.left
                            let y = (parseFloat(y) || 0) + event.deltaRect.top

                            Object.assign(event.target.style, {
                                width:  `${event.rect.width}px`,
                                height: `${event.rect.height}px`,
                            })

                            // tomo las nuevas dimensiones de la caja
                            $this.textboxs[$this.indexBox].width = parseInt(event.rect.width / $this.scale);

                            $this.fitMaxLengthAdjust($this.textboxs[$this.indexBox]);      // Ajusto al ancho de la caja
                             
                            // Actualizo las minuaturas de las firmas sobre la página actual
                            if ($this.resizeBox) {
                                clearTimeout($this.resizeBox);
                            }
                            $this.resizeBox = setTimeout(function () {
                                $this.refreshPreview($this.page);
                            }, 1000);

                        },
                    }
                });

            // Si he adicionado un select debo completarlo con las opciones del mismo
            if (boxType == 6) {
                toastr.warning(document.getElementById('messages').dataset.completeSelectBox);
            }

            // Asigno función dinámica para cuando se escribe en el input
            // tomar el texto en la caja seleccionada
            if (box.signer.creator) {
                if ([6].indexOf(boxType) != -1) {
                    //console.log('asignando funcionalidad para select');
                    inputField.onchange = (e) => {
                        const $this = this;

                        $this.textboxs[$this.indexBox].text = String(e.target.value);

                        $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                    };
                } else if ([5].indexOf(boxType) != -1) {
                    //console.log('asignando funcionalidad para checkboxs');
                    inputField.onchange = (e) => {
                        const $this = this;
                        $this.indexBox = $this.textboxs.findIndex(box => box.id === e.target.dataset.boxId);
                        $this.box = $this.textboxs[$this.indexBox];

                        this.textboxs[this.indexBox].text = String(e.target.checked);
                        $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                    };
                } else {
                    //console.log('asignando funcionalidad para inputs texs')
                    inputField.onkeyup = (e) => {
                        const $this = this;

                        /* avoid special chars example
                        var bad = /[^\sa-z\d]/i;
                        var key = String.fromCharCode(e.keyCode || e.which);
                        console.log(bad.test(key))
                        if (e.which !== 0 && e.charCode !== 0 && bad.test(key)) {
                            return false;
                        } 
                        */ 

                        $this.indexBox = $this.textboxs.findIndex(box => box.id === e.target.dataset.boxId);
                        $this.box = $this.textboxs[$this.indexBox];

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
                        if (removeKeys.indexOf(e.keyCode) === -1 && $this.box.rules.maxLength != 0 && writing) {
                            if ($this.box.rules.maxLength == $this.box.text.length) {
                                toastr.warning(document.querySelector('#messages').dataset.maxLengthAchieved);
                            }
                        }

                        //this.textboxs[this.indexBox].text = e.target.value;
                        $this.box.text = e.target.value;

                        $this.$forceUpdate();       /* Actualizo el dom para el estado de las firmas del creador */
                    };
                }

                if (!box.text) {
                    toastr.warning(placeholder.dataset.messageForCreator);
                }
            }

            // Asigno funcionalidad a las cajas de texto no importa quien sea el signer
            if ([1, 2, 3, 4].indexOf(boxType) != -1){
                inputField.onkeydown = (e) => {
                    const $this = this;
                    //console.log(e.keyCode)

                    const keysToExclude = [32,37,38,39,40,12,27,8,46,35,36];   // Teclas que se omiten: espacio, teclas dirección,
                                                                         // espacio, numpadenter, remove keys, end, home

                    $this.indexBox = $this.textboxs.findIndex(box => box.id === e.target.dataset.boxId);
                    $this.box = $this.textboxs[$this.indexBox];
                    
                    if (keysToExclude.indexOf(e.keyCode) == -1) {
                        
                        let acceptChar = false;     // Se rechaza el caracter hasta que no se verifique
                        // En dependencia de lo que he escrito reviso la regla de la caja
                        switch($this.whatITyped(e)) {
                            case 1:
                                //console.log('check for Number');
                                acceptChar = $this.box.rules.numbers;
                                break; 
                            case 2:
                                //console.log('check for Letters');
                                acceptChar = $this.box.rules.letters;
                                break; 
                            case 3:
                                //console.log('check for Specials');
                                acceptChar = $this.box.rules.specials;
                                break; 
                            default:
                                //console.log('refuse it!, do nothing');
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
                //console.log('pintando opciones de select ')
                box.options.split(';').forEach(option => {
                    //console.log(option)
                    if (option != '') {
                        inputField.add(new Option(option));
                    }
                });

            }

            // Funcionalidad del input cuando pierde el foco
            inputField.onblur = (e) => {
                    const $this = this;
                    $this.refreshPreview($this.page);
                };

            // Actualizo las minuaturas de las firmas sobre la página actual
            this.refreshPreview(this.page);
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

        /**
         * Devuelve el tamaño de fuente para los inputs textos según la escala
         */
        getFontSize: function (scale) {
            /**
             * fuente ideal
             *   en input a lo ancho deben caber 72 caracteres
             *   100%  585px 72 chars  14.4					40.62
             *         171px ?
             *   125%  724px 72 chars  18.0 px fontSize		40.22
             *   150%  884px 72 chars  21.9 px				40.36
             *   175% 1034px 72 chars  25.5					40.54
             *   200% 1184px 72 chars  29.4					40.27
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
        },

        /**
         * Elimina un contenedor de caja de texto
         * 
         * Ocurre cuando se presiona la "x" del contenedor de caja
         * 
         * @param {Event} event
         */
        removeBoxPlaceHolder: function (event) {
            try {
               const $this = this;

                // Obtiene el id del contenedor de caja de texto a eliminar
                let boxId = event.currentTarget.dataset.boxId;

                if ($this.box && boxId == $this.box.id) {
                    $this.initBox();
                    $this.selectedBox = false;
                }

                // Obtengo el índice en el arreglo de cajas
                const index = $this.textboxs.findIndex(box => box.id === boxId);

                if (index != -1) {
                    const pageBox = $this.textboxs[index].page;

                    // Elimina el contenedor de firma de id dado
                    document.querySelector(`#textboxs [data-box-id="${$this.textboxs[index].id}"]`).remove();

                    // Elimino la caja del array
                    $this.textboxs.splice(index, 1);

                    // Actualizo la miniatura de la página a la que pertenece la firma
                    $this.refreshPreview(pageBox);

                    // Elimino el li de la lista de las cajas de creador
                    document.querySelector(`#creatorBoxs li[data-box-id="${boxId}"]`).remove();
                }
            }
            catch (error) {}
            finally {}
        },

        /**
         * Elimina todos los contenedores de textboxs del documento
         */
        clearBoxsPlaceholders: function () {
            document.getElementById('textboxs').innerHTML  = '';
        },

        /*
         * Limpia el canvas y lo dimensiona a tamaño 100x100
         */
        clearCanvas: function () {
            this.context.clearRect(0,0, this.canvas.width, this.canvas.height);
            this.canvas.width  = 250;
            this.canvas.height = 100;
        },

        /**
         * Muestra la paǵina del documento actual
         */
        render: function () {
            const $this = this;
            console.log('Muestra la paǵina del documento actual => ' + $this.page);

            // clear the canvas
            if ($this.context) {
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

                // Si debemos ajustarnos a la pantalla
                if ($this.fit) {
                    // La escala del canvas es el ancho del visor del documento entre el ancho
                    // del la imagen de la página del pdf
                    const docWidth = parseInt($this.$refs.document.clientWidth);
                    $this.scale = Math.round( (docWidth / $this.viewportI[$this.page].width) * 100, 2) / 100; 
                }

                //console.log('Por ciento de escalado')
                //console.log(Math.round( $this.scale * 100) + ' %')

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

                // Dibuja los contenedores de cajas de firma
                $this.drawBoxsPlaceholdersInPage($this.page);
            });
        },

        /**
         * Guarda la configuración de cajas de texto del documento
         *
         * Se guardan todas las cajas consignadas sobre el documento
         */
        saveDocument: function () {
           
            const $this = this;

            // Verifico que si hay cajas de texto tipo select tengan las opciones configuradas
            if ($this.textboxs.filter( box => box.type == 6 && box.options == "").length > 0) {
                toastr.warning(document.getElementById('messages').dataset.incompleteSelectBoxes);
                return;
            }

            // Comprueba que firmantes han sido seleccionados y cuales no
            // es decir, para que firmantes se ha establecido una caja de texto y para cuales no
            $this.signers.forEach(signer => 
                {
                    //console.log(signer.name);
                    const selected = $this.textboxs.filter(box => box.signer.id == signer.id).length > 0;
                    //console.log(selected);
                    signer.selected = selected;
                }
            );

            $this.$forceUpdate(); // Si esto no se visualiza correctamente el moda de abajo

            //
            // Para todos los firmantes que se han definido en el paso anterior
            // debe establecerse al menos una caja de texto
            // En caso contrario no se permite guardar el documento y se muestra el error
            //
            // Por tanto, para finalizar el proceso todos los firmantes han tenido que ser seleccionados
            //
            if ($this.signers.length != $this.signers.filter(signer => signer.selected).length) {
                // Abre la modal que muestra el error
                // que indica que falta de especificar cajas de texto para algún firmante
                $this.$bvModal.show('number-of-signers-not-mismatch');
                return;
            }

            let completedBoxes = $this.creatorBoxs.filter(box => ["", -1].indexOf(box.text) == -1).length;
            
            //console.log('completedBoxes')
            //console.log(completedBoxes)

            // Compruebo que todas las cajas del creador se hayan completado
            if (completedBoxes !== $this.creatorBoxs.length ) {
                // Obtengo la página de la primera que debe completar
                toastr.options.onclick = () => {
                    $this.goPage($this.creatorBoxs.filter(box => box.text == "")[0].page);
                }
                toastr.error(document.getElementById('messages').dataset.creatorMustFinished);
                return;
            }

            // Inicia animación
            HoldOn.open({theme: 'sk-circle'});
    
            axios.post($this.request.saveDocument, {
                boxs   : $this.textboxs,      // Las cajas de texto
            })
            .then(response => {
                // Detiene la animación
                HoldOn.close();

                if (response.data.code == 1) {
                    // Obtiene el documento guardado
                    $this.document = response.data.document;

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

        /*
         * Devuelve true cuando solo esta seleccionado un check de los 3 que regulan las restricciones
         * de una caja de texto, con el objetivo de que mínimo una regla esté activa siempre
         */
        onlyMe: function (num) {
            const $this = this;
            if (!$this.box.id || !$this.selectedBox)  {
                return true;
            }

            if (num == 1) {
                if ($this.box.rules.numbers) {
                    return !$this.box.rules.letters && !$this.box.rules.specials;
                } else {
                    return false;
                }
            }

            if (num == 2) {
                if ($this.box.rules.letters) {
                    return !$this.box.rules.numbers && !$this.box.rules.specials;
                } else {
                    return false;
                }
            }

            if (num == 3) {
                if ($this.box.rules.specials) {
                    return !$this.box.rules.numbers && !$this.box.rules.letters;
                } else {
                    return false;
                }
            }
             
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

                maxLength = parseInt(box.width / 8.6) - 1;
                box.rules.maxLength = maxLength;
            }

            // Guardo el valor de la longitud máxima inicial
            if (!box.initMaxLength) {
                box.initMaxLength = box.rules.maxLength;
            }
            
            // si 'text' es verdadero se devuelve la info completa para mostrar en opciones de la caja como propiedad
            // Cantidad máxima : xx
            if (text) {
                return `${this.langs[6]} : ${maxLength}`;
            }
            return maxLength;
        },

        /*
         * Ajusta el tamaño máximo permitido al ancho de la caja de texto
         */
        fitMaxLengthAdjust: function (box) {
            if (box.fitMaxLength) {
                /*
                 * ancho de las cajas según cantidad de caracteres
                 * 171px / 20 caracteres 8.6px por caracter apróximadamente
                 */

                // busco el ancho de la caja de texto
                let inputField = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"] input`);
                inputField = inputField ? inputField : document.querySelector(`#textboxs > .box-placeholder[data-box-id="${box.id}"] select`);

                box.rules.maxLength = Math.round((parseInt(inputField.getBoundingClientRect().width) / 8.6));
            } else {
                // Si no quiero ajustar, restablezco al valor inicial
                if (box.initMaxLength) {
                    box.rules.maxLength = box.initMaxLength;
                }
            }
        },

        /*
         * Adiciona un texto como una nueva opción para el input select de una caja de texto tipo select
         */
        addOptionToBox: function (box) {
            const $this = this;

            // los items se guardan en 'options' de la caja separados por ';'
            if ($this.newOptionText != '') {
                // lo adiciono si el texto no está insertado ya
                if ($this.box.options.split(';')
                        .findIndex(option => option == $this.newOptionText) == -1
                ) {
                    $this.box.options += `${$this.newOptionText};`;

                    // Adiciono el texto o actualizo las opciones del select
                    let inputField = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${$this.box.id}"] select`);
                    if (inputField) {
                        inputField.add(new Option($this.newOptionText));
                    }
                }

                $this.newOptionText = '';
            }
        },

        /*
         * Elimina un texto de una opción en el input select de una caja de texto tipo select
         *
         * @param Box box La caja de texto
         * @param integer index La posición en el array de opciones que se desea eliminar
         */
        removeOptionToBox: function (box, index) {
            // los items se guardan en 'options' de la caja separados por ';'

            //Verifico que no este eliminando a la opción 0
            let inputField = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${this.box.id}"] select`);
            if (inputField) {
                if (inputField.options[index+1].value != -1) {
                    let options = box.options.split(';');
                    options = options.filter(option => option != options[index]);
                    box.options = `${options.join(';')}`

                    // Elimino el texto o actualizo las opciones del select
                    // siempre el option está en la pos index+1 porque en 0 mantenemos el texto inicial
                    inputField.remove(index+1);
                }
            }
        },

    },

    /* Variables computadas */
    computed: {

        /*
         * Devuelve las cajas de textos que debe realizar el creador del documento 
         */
        creatorBoxs: function () {
            return this.textboxs.filter(box => box.signer.creator === true);
        },

        /*
         * Devuelve las paginas que contienen cajas de texto en el doc
         */
        pagesWithBoxs: function () {
            return [...new Set(this.textboxs.map(box => box.page))];
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
    },

    watch: {

        // Observador de cuando cambia la longitud máxima de la caja seleccionada
        'box.rules.maxLength': {
            handler(maxLength, oldmaxLength){
                const $this = this;
                /*
                 * Actualiza el atributo maxLength del input html corrspondiente a la caja seleccionada
                 */
                // Si hay una caja seleccionada
                if ($this.box.id && $this.selectedBox) {
                    let inputField = document.querySelector(`#textboxs > .box-placeholder[data-box-id="${$this.box.id}"] input`);
                    inputField?.setAttribute('maxlength', $this.box.rules.maxLength);
                }
            },
        },
    },

    /*
    // vigilante de modificación sobre la caja seleccionada
    watch: {

        box: {
            handler(box, oldBox){
                const $this = this;
                if (oldBox.id) {
                    // salvo la caja anterior en el listado
                    let indexOld = $this.textboxs.findIndex( b => b.id == oldBox.id);
                    if (indexOld > -1) {
                        $this.textboxs[indexOld] = oldBox;
                    }
                }
                //console.log('ahora => ' + box.id);
            },
            //deep: true,           // No es necesario por el momento
        },
    },

    */
});
