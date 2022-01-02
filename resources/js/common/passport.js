/**
 * Gestiona los documentos de identificación personal
 * para el proceso de validación mediante documentos identificativos
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

const { default: Axios } = require("axios");

const { createWorker, createScheduler } = Tesseract;
const scheduler = createScheduler();

new Vue({
    el: '#app',
    components: {
        vuejsDatepicker,
    },

    data: {

        modelUrl : `${document.getElementById('data').dataset.url}/assets/js/common/weights`,

        webcam:    null,    // Webcam para foto facial
        webcam1:   null,    // Webcam para foto anverso de documento
        webcam2:   null,    // Webcam para foto reverso de documento
        image:     null,

        // Face api use
        useFaceRecognition: false,  // Si el firmante debe hacerse reconocimiento de su cara en el proceso
        video:      null,
        canvas:     null,
        
        detectedFace: false,

        playInterval: null,         // Intervalo para deteccion de rostros
        ocrInterval: null,          // Intervalo para ocr

        // Los mensajes
        message     : document.getElementById('messages').dataset,

        passports   : [],           // La lista de documentos identificativos
                                    // como pasaportes, carnés

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

        // Las direcciones de las solicitudes
        request     : null,

        passport    :               // Cada uno de los documentos identificativos utilizados
            {
                type            : 0,
                number          : null,
                expedition_date : null,
                expiration_date : null,
                front           : null,
                back            : null,
            },

        position    :               // La posición del usuario firmante datum WGS84
            {
                latitude : null,
                longitude: null
            },
        
        visit : null,               // La visita del usuario firmante

        canUploadDocuments:false,   // Controla si se pueden subir los documentos o no, en 
                                    // dependencia si se ha encontrado una webcam

        faceRecognitionModelsLoaded: false, // Controla si se han cargado los modelos de la Red N para el reconocimiento facial
    },    

    /**
     * Cuando tenemos la instancia montada
     */
    mounted: function () {
        const $this = this;

        // Intentamos obtener la geolocalización del firmante
        $this.getCurrentPosition()
            .then(() => {
                console.info(`[INFO] Se ha obtenido la posición del firmante con éxito`);
            })
            .catch(() => {
                console.info(`[INFO] No se ha podido obtener la posición del firmante`);
            });

        // Obtenemos el registro de la visita del usuario a la página
        this.visit = JSON.parse(document.getElementById('visit').dataset.visit);

        // Obtenemos si se debe hacer reconocimiento de cara
        this.useFaceRecognition = JSON.parse(document.getElementById('data').dataset.useFaceRecognition);

        this.request = document.getElementById('requests').dataset;

        // Advertencia que debe introducirse el número del documento identificativo
        toastr.error($this.message.rememberIncludePassportNumber);

        // Mensaje que indica que se está buscando por un dispositio de video
        toastr.warning($this.message.findingWebcamText, $this.message.findingWebcamTitle, {
            progressBar: true,
            // Barra de progreso orientativa en proporción al número de páginas del documento
            timeOut    : 3000,
        });

        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri($this.modelUrl),
            faceapi.nets.faceLandmark68Net.loadFromUri($this.modelUrl),
            //faceapi.nets.faceRecognitionNet.loadFromUri($this.modelUrl),
            //faceapi.nets.faceExpressionNet.loadFromUri($this.modelUrl),
        ]).then(() => {
            $this.faceRecognitionModelsLoaded = true;
            $this.setupWebcam();
        });

        (async () => {
            console.log('Initializing Tesseract.js');
            for (let i = 0; i < 4; i++) {
                const worker = createWorker({
                    //logger: m => console.log("[OCR] : ", m["progress"] * 100 + "%")
                });

                await worker.load();
                await worker.loadLanguage('spa+eng');
                await worker.initialize('spa+eng');
                scheduler.addWorker(worker);
            }
            console.log('Initialized Tesseract.js');
        })()
    },

    methods: {

        /*
         * Inicia el dispositivo de video para realizar las fotos en tiempo real
         * 0 | null Facial photo
         * 1 Document Anverse
         * 2 Document Reverse
         *
         * @param int webcamNumber                        La cámara que inicializaré según el apartado
         */
        setupWebcam: async function (webcamNumber) {
            const $this = this;
            
            $this.stopWebCam();             // Primero cerramos alguna cámara abierta
            $this.detectedFace = false;     // Establecemos en false la detección de rostro

            // Inicializo la webcam
            const getWebCam = () => {

                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(function (stream) {

                            $this.canUploadDocuments = true;

                            toastr.clear();
                            toastr.info($this.message.findingWebcamOk);

                            $this.video = document.getElementById('webcam');            // para foto de rostro de firmante
                            $this.canvas = document.getElementById('overlay');

                            if (webcamNumber) {
                                if (parseInt(webcamNumber) == 1) {
                                    $this.video = document.getElementById('webcam1');   // para foto de anverso de documento
                                    $this.canvas = document.getElementById('overlay1');

                                    $this.video.srcObject = stream;
                                    $this.video.play();
                                    $this.webcam1 = true;

                                } else if (parseInt(webcamNumber) == 2) {
                                    $this.video = document.getElementById('webcam2');   // para foto de reverso de documento
                                    $this.canvas = document.getElementById('overlay2');

                                    $this.video.srcObject = stream;
                                    $this.webcam2 = true;

                                    // Cuando se va a hacer la foto del reverso del documento se debe aplicar OCR
                                    // para detectar los textos en la imagen de la webcam
                                    //$this.playingOcrVideo();
                                    $this.video.play();
                                }
                            } else {
                                $this.video.srcObject = stream;
                                $this.video.play();
                                $this.webcam = true;
                            }

                            $this.video.scrollIntoView(true);   // TODO, no funciona como espero

                            // Se aplica el reconocimiento facial solo cuando el usuario lo tiene configurado
                            if ($this.useFaceRecognition) {
                                if (webcamNumber && parseInt(webcamNumber) == 2) {
                                    // reverso no lleva face recognition
                                } else {
                                    $this.video.onplay = $this.playing;
                                }
                            }

                        }).catch(error => {
                            console.error(error);
                            if (error.message == 'Permission denied') {
                                toastr.error($this.message.permissionDenied);
                            } else {
                                toastr.error($this.message.findingWebcamKo);
                            }
                            $this.webcam = null;
                        });
                }
            }

            getWebCam();
        },

        /*
         * Detiene el uso de la webcam
         */
        stopWebCam: function () {
            const $this = this;

            clearInterval($this.playInterval);
            clearInterval($this.ocrInterval);

            if ($this.video) {
                var stream = $this.video.srcObject;
                var tracks = stream? stream.getTracks() : [];
                tracks.forEach(function(track) {
                   track.stop();
                });
                $this.video.srcObject = null;
                $this.video.onplay = null;
                
            }

            $this.clearCanvas();
            $this.canvas = null;
            $this.webcam = null;
            $this.webcam1 = null;
            $this.webcam2 = null;
        },

        /*
         * Limia el canvas que dibuja las caras reconocidas
         */
        clearCanvas: function () {
            const $this = this;

            if ($this.canvas) {
                $this.canvas.getContext('2d')
                    .clearRect(
                        0, 0,
                        $this.canvas.width, $this.canvas.height
                    );
            }
        },

        /*
         * Reconoce y dibuja la cara del firmante sobre la webcam
         * Face-api JS
         */
        playing: function () {
            const $this = this;

            $this.playInterval = setInterval(async () => {
                const options = new faceapi.TinyFaceDetectorOptions();
                const result = await faceapi.detectSingleFace($this.video, options)     //  .detectAllFaces
                    .withFaceLandmarks();                                               //  .detectSingleFace
                if (result) {
                    const canvas = $this.canvas;
                    const dims = faceapi.matchDimensions(canvas, $this.video, true)
                    const resizedResult = faceapi.resizeResults(result, dims);

                    faceapi.draw.drawDetections   (canvas, resizedResult);
                    faceapi.draw.drawFaceLandmarks(canvas, resizedResult);

                    $this.clearCanvas();

                    faceapi.draw.drawDetections   ($this.canvas, resizedResult);
                    faceapi.draw.drawFaceLandmarks($this.canvas, resizedResult);
                    //$this.webcam = resizedResult;
                    $this.detectedFace = true;

                    // Tomar la foto automaticamente
                    // $this.takeFacialPhoto();
                } else {
                    $this.clearCanvas();
                    $this.detectedFace = false;
                }
          }, 1000);
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
        resizeImage: function (data, wantedWidth=1024, wantedHeight=768) {

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

        /*
         * Reconoce los caracteres de la imagen utilizando OCR
         * Tesseract js
         */
        playingOcrVideo: async function () {
            const $this = this;

            $this.ocrInterval = setInterval(async () => {
                $this.canvas.getContext('2d').drawImage(
                    $this.video,
                    0, 0,
                    $this.video.width,     // Ancho de la imagen para reconocimiento de los caracteres
                    $this.video.height,    // Alto  de la imagen para reconocimiento de los caracteres
                );

                const start = new Date();
                const { data: { text } } = await scheduler.addJob('recognize', $this.canvas);
                const end = new Date()
                  
                console.log(`[${start.getMinutes()}:${start.getSeconds()} - ${end.getMinutes()}:${end.getSeconds()}], ${(end - start) / 1000} s`);
                text.split('\n').forEach((line) => {
                    console.log(line);
                });
                
                console.log("ocr => ");
                console.log(text);

            }, 1000);

            $this.video.onstop = () => {
                clearInterval($this.ocrInterval);
            }
            $this.video.onpause = $this.video.onstop

            $this.video.play();
        },
       
        /*
         * Elimina la imagen facial del firmante
         */
        deleteFacialPhoto: function () {
            this.image = null;
            this.$forceUpdate();
            this.setupWebcam();
        },

        /*
         * Toma la imagen de la webcam
         *
         * @param int webcamNumber          La cámara que inicializaré según el apartado
         */
        takePhoto: function (webcamNumber) {
            const $this = this;

            let canvas = document.getElementById('canvas');

            if (webcamNumber) {
                if (parseInt(webcamNumber) == 1) {
                    canvas = document.getElementById('canvas1');
                } else if (parseInt(webcamNumber) == 2) {
                    canvas = document.getElementById('canvas2');
                }
            }

            var rect = $this.video.getBoundingClientRect();
            Object.assign(canvas, {
                width:   rect.width,
                height:  rect.height,
            });
            canvas.getContext('2d')
                .drawImage($this.video, 0, 0, canvas.width, canvas.height);

            return canvas.toDataURL("image/png");
        },

        /*
         * Toma la imagen de la webcam como la foto facial del firmante
         */
        takeFacialPhoto: function () {
            this.image = this.takePhoto();
            this.stopWebCam();
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
         * Adiciona un documento a la lista de documentos cargados
         */
        addPassportToList: function () {
            // Añade el documento identificativo a la lista
            this.passports.push(
                {
                    type            : this.passport.type,
                    number          : this.passport.number,
                    expedition_date : this.passport.expedition_date,
                    expiration_date : this.passport.expiration_date,
                    front           : this.passport.front,
                    back            : this.passport.back
                });

            // Limpia el formulario
            this.clearForm();

            // Mensaje
            toastr.info(this.message.documentLoaded);
        },

        /**
         * Limpia el formulario de datos
         * 
         */
        clearForm: function () {
            this.passport.type               = 0;
            this.passport.number             = null;
            this.passport.expedition_date    = null;
            this.passport.expiration_date    = null;
            this.passport.front              = null;
            this.passport.back               = null;

            //this.image                       = null;
        },

        /**
         * Elimina un documento de identificación del listado
         * 
         * @param {Number} index    El índice del documento identificativo a eliminar en la tabla 
         */
        removePassport: function (index) {
            this.passports.splice(index, 1);
        },

        /**
         * Toma la foto del anverso del documento de la cámara del dispositivo, si la tiene
         */
        takeAnversoRealTimePhoto: function () {
            this.passport.front = this.takePhoto(1);
            this.stopWebCam();
        },

        /**
         * Toma la foto del reverso del documento de la cámara del dispositivo, si la tiene
         */
        takeReversoRealTimePhoto: function () {
            this.passport.back = this.takePhoto(2);
            this.stopWebCam();

            /* AQUI HACER EL OCR DE ESTA IMAGEN Y NO EN TIEMPO REAL */
            (async () => {
                const start = new Date();
                const { data: { text } } = await scheduler.addJob('recognize', this.passport.back);
                const end = new Date()
                  
                console.log(`[${start.getMinutes()}:${start.getSeconds()} - ${end.getMinutes()}:${end.getSeconds()}], ${(end - start) / 1000} s`);
                document.getElementById('ocr').innerHTML = text;

                text.split('\n').forEach((line) => {
                    console.log(line);
                });
                
            })()
        },

        /**
         * Elimina la foto que se ha tomado como anverso del documento
         */
        deleteAnverso: function () {
            this.passport.front = null;
            this.$forceUpdate();
        },

        /**
         * Elimina la foto que se ha tomado como reverso del documento
         */
        deleteReverso: function () {
            this.passport.back = null;
            this.$forceUpdate();
        },

        /**
         * Obtiene el nombre del tipo de documento
         * 
         * @param {Number} type   El número de tipo de documento
         * 
         * @return {String}       El nombbre del tipo de documento
         */
        getDocumentTypeName: function (type) {
            return document.querySelector(`option[value="${type}"]`).innerHTML;
        },

        /**
         * Guarda los documentos para la validación
         * 
         * @param {Event} event 
         */
        save: function (event) {
            const $this = this;

            HoldOn.open({theme: 'sk-circle'});

            const data = {
                image       : $this.image,
                passports   : $this.passports,
                position    : $this.position,
                visit       : $this.visit,
            };

            console.log(data);

            console.log($this.request);

            axios.post($this.request.save, data)
                .then(resp => {
                    console.log(resp);

                    HoldOn.close();
                    location.href = $this.request.redirectAfterSave;
                })
                .catch(error => {
                    HoldOn.close();
                    console.error(error);
                });
        },

    },

    computed: {

        /*
         * Si se puede finalizar
         * Cuando tengo documentos en mi lista, y además
         * Si debo hacer reconocimiento facial que se haya tomdo la imagen para ello
         */
        canFinish: function () {
            if (!this.useFaceRecognition && this.passports.length) {
                return true;
            }
            if (this.useFaceRecognition && this.image && this.passports.length) {
                return true;
            }
            return false;
        },

        /*
         * Si se puede tomar foto facial
         * Cuando debo hacer reconocimiento facial y se ha reconocido una cara
         * y cuando no debo hacer reconocimiento
         */
        canTakeFacialPhoto: function () {
            if (!this.useFaceRecognition) {
                return true;
            }
            if (this.useFaceRecognition && this.detectedFace) {
                return true;
            }
            
            return false;
        },

        /*
         * Si se puede iniciar la webcam en apartado para foto facial
         * Cuando debo hacer reconocimiento facial y se han cargado los modelos de la RNN
         * y cuando no debo hacer reconocimiento
         */
        canInitWebCWithFR: function () {
            if (!this.useFaceRecognition) {
                return true;
            }
            if (this.useFaceRecognition && this.faceRecognitionModelsLoaded) {
                return true;
            }
            
            return false;
        },

        

    }
});
