/**
 * Muestra el documento a firmar 
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
        document      : document.getElementById('document').dataset,
        // La lista de páginas del documento que faltan de ser firmadas
        remainingPages: [],
        // Los mensajes
        message     : document.getElementById('messages').dataset,
        // Las firmas sobre el documento
        signs : [],
        // Escala
        scale: 1,

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
    },
    /**
     * Cuando tenemos la instancia montada
     *
     */
    mounted: function () {
 
        // Las páginas que faltan de ser firmadas en el documento
        this.remainingPages = this.document.remainingPages.split(',').map(Number);

        console.log("this.remainingPages");
        console.log(this.remainingPages);

        // Obtiene las miniaturas del documento
        this.getThumbs();

        // Obtengo las firmas
        this.signs = JSON.parse(document.getElementById('document').dataset.signs);
    },
    methods: {

        /**
         * Obtiene las miniaturas del documento PDF
         * 
         */
        getThumbs: function () {

            const $this = this;
            
            // la url del pdf que se previsualiza
            console.log($this.document.pdf);

            pdfjsLib.getDocument($this.document.pdf)
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
                        
                        // Crea un elemento para cada una de las páginas
                        // clonando la plantilla establecida
                        let page = document.querySelector('.page.template').cloneNode(true);

                        // Coloca el número de página
                        page.classList.remove('template');
                        page.dataset.page = num;
                        page.querySelector('.page-number').innerHTML = num;

                        // Si la página falta aún de ser firmada, resalta el número de página
                        if ($this.remainingPages.includes(num)) {
                            page.querySelector('.page-number').classList.add('remaining');
                        }

                        // Añade el elemento de página creado al conjunto de páginas
                        document.getElementById('pages').appendChild(page);

                        // Añade el enlace a la paǵina para ser firmada
                        page.addEventListener('click', () => {
                            // La url de acceso para cada página del documento y ejecutar la firma si procede
                            location.href = $this.document.page.replace('#', num);
                        });

                        // Genero un color diferente para las firmas en cada página
                        const randomColor = Math.floor(Math.random()*16777215).toString(16);
                  
                        // Construye un canvas en el elemento de página
                        return pdf.getPage(num)
                            .then(page => $this.makeThumb(page))
                            .then(canvas => {
                                const ctx = canvas.getContext("2d");
                                // Obtengo todas las firmas dentro de la página actual
                                this.signs.filter( sign => sign.page === num)
                                    .forEach( sign => {
                                        // Pinto algo en la posición x, y de la firma
                                        ctx.fillStyle = `#${randomColor}`;
                                        ctx.fillRect(sign.x, sign.y, this.placeholder.canvas.width, this.placeholder.canvas.height);
                                        ctx.fillStyle = `#000000`;
                                        // TODO, agregar el código de la firma en el canvas
                                    });
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
    }
});
