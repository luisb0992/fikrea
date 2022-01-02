/**
 * Webpack mix
 *
 * @link https://laravel.com/docs/8.x/mix
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 *
 */

const mix = require('laravel-mix');

/**
 * La url home del proyecto
 */
const homeUrl = 'http://localhost:8000/landing';

/**
 * Copia las fuentes de la landing page y de la dashboard page a la carpeta pública
 */
mix.copyDirectory('resources/fonts/landing', 'public/assets/fonts/landing')
    .copyDirectory('resources/fonts/dashboard', 'public/assets/fonts/dashboard');

/**
 * Copia el favicon en la carpeta pública
 */
mix.copy('resources/images/favicon.ico', 'public/assets/images/');

/**
 * Copia el icono de la flecha de fikrea en la carpeta pública
 */
mix.copy('resources/images/up.ico', 'public/assets/images/');

/**
 * Copia la imagen del cursor personalizado para la carpeta pública
 */
mix.copy('resources/sass/workspace/cursor-sign.png', 'public/assets/css/workspace/');

/**
 * Copia las imágenes a la carpeta pública
 */
mix.copyDirectory('resources/images', 'public/assets/images');

/**
 * Copia los archivos multimedia a la carpeta pública
 */
mix.copyDirectory('resources/media', 'public/assets/media');

/**
 * Copia los archivos javascript de vendor de la landing page a la carpeta pública
 */
mix.copyDirectory('resources/js/landing/vendor', 'public/assets/js/landing/vendor');

/**
 * Copia los archivos de javascript de vendor de la dashboard page a la carpeta pública
 */
mix.copyDirectory('resources/js/dashboard/vendor', 'public/assets/js/dashboard/vendor');

/**
 * Copia los archivos css de vendor a la carpeta pública
 */
mix.copyDirectory('resources/css/vendor', 'public/assets/css/vendor');

/**
 * Copia los archivos css de vendor de la landing page a la carpeta pública
 */
mix.copyDirectory('resources/css/landing/vendor', 'public/assets/css/landing/vendor');

/**
 * Copia los archivos css de la dashboard page a la carpeta pública
 */
mix.copyDirectory('resources/css/dashboard', 'public/assets/css/dashboard/vendor');

/**
 * Compila las hojas de estilos de la página de landing
 */
mix.sass('resources/sass/landing/style.scss', 'public/assets/css/landing')
    .sass('resources/sass/landing/share.scss', 'public/assets/css/landing')
    .sass('resources/sass/landing/fixed-bottom-button.scss', 'public/assets/css/landing');

/**
 * Compila las hojas de estilos de la página de error
 */
mix.sass('resources/sass/error/style.scss', 'public/assets/css/error');

/**
 * Compila la hoja de estilo de bootstrap
 */
mix.copy('resources/sass/bootstrap.min.css', 'public/assets/css/');

/**
 * Publica el archivo principal app.js
 */
mix.js('resources/js/app.js', 'public/assets/js');

/**
 * Publica el archivo de configuración del token CSRF para trabajar con librerías
 * Javascript como Axios, para que sea incorporado en las llamadas Ajax
 */
mix.copyDirectory('resources/js/csrf/', 'public/assets/js/csrf');

/**
 * Publica los archivos de bootstrap-vue y vue
 */
mix.copyDirectory('resources/js/vue/', 'public/assets/js/vue');

/**
 * Publica los archivos de vuelidate js
 */
mix.copyDirectory('resources/js/vuelidate/', 'public/assets/js/libs');

/**
 * Publica los archivos de filesize js  plugins en libs
 */
mix.copyDirectory('resources/js/filesize/', 'public/assets/js/libs');

/**
 * Publica los archivos de libs para libs
 */
mix.copyDirectory('resources/js/libs/', 'public/assets/js/libs');

/**
 * Publica los archivos de pfd para pdf
 */
mix.copyDirectory('resources/js/pdf/', 'public/assets/js/libs');

/**
 * Publica el archivo de configuración de las librería de javascript externas
 */
mix.js('resources/js/config/config.js', 'public/assets/js/config');

/**
 * Publica los archivos javascript de la página de landing
 */
mix.js('resources/js/landing/cookies.js', 'public/assets/js/landing')
    .js('resources/js/landing/file-share.js', 'public/assets/js/landing');

/**
 * Compila las hojas de estilos personalizada y común de la página dashboard
 */
mix.sass('resources/sass/dashboard/style.scss', 'public/assets/css/dashboard');

/**
 * Publica los archivos javascript de la página de dashboard
 */
mix
    .js('resources/js/dashboard/contact.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/home.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/config.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/search.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/login.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/register.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/password-change.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/profile.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/config-audio.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/config-video.js', 'public/assets/js/dashboard')
    .js('resources/js/dashboard/session.js', 'public/assets/js/dashboard');

/**
 * Compila las hojas de estilo de la página de dashboard
 */
mix.sass('resources/sass/dashboard/login.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/register.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/password-change.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/customer-contact.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/home.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/profile.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/config.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/subscription/mustberenew.scss', 'public/assets/css/dashboard/subscription')
    .sass('resources/sass/dashboard/subscription/select.scss', 'public/assets/css/dashboard/subscription')
    .sass('resources/sass/dashboard/session.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/slider.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/select-validations.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/page.scss', 'public/assets/css/dashboard')
    .sass('resources/sass/dashboard/document/config.scss', 'public/assets/css/dashboard/document')
    .sass('resources/sass/dashboard/document/config-textboxs.scss', 'public/assets/css/dashboard/document')
    .sass('resources/sass/dashboard/document/status.scss', 'public/assets/css/dashboard/document')
    .sass('resources/sass/dashboard/document/history.scss', 'public/assets/css/dashboard/document')
    .sass('resources/sass/dashboard/file/upload.scss', 'public/assets/css/dashboard/file')
    .sass('resources/sass/dashboard/file/list.scss', 'public/assets/css/dashboard/file');

/**
 * Publica los archivos javascript de manipulación de la subscripción
 */
mix.js('resources/js/dashboard/subscriptions/select.js', 'public/assets/js/dashboard/subscriptions');

/**
 * Publica los archivos javascript de manipulación de firmantes, documentos, páginas
 */
mix.js('resources/js/dashboard/documents/list.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/signers.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/validations.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/document.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/sign-multiple.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/form-data.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/edit.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/config.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/config-texts.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/config-signers-request.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/status.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/documents/share.js', 'public/assets/js/dashboard/documents')
    .js('resources/js/dashboard/requests/edit.js', 'public/assets/js/dashboard/requests')
    .js('resources/js/dashboard/requests/status.js', 'public/assets/js/dashboard/requests')
    .js('resources/js/dashboard/requests/signers.js', 'public/assets/js/dashboard/requests');

/**
 * Publica los archivos javascript necesarios para la certificación y verificación de datos
 */
mix.js('resources/js/dashboard/verificationform/edit.js', 'public/assets/js/dashboard/verificationform')
    .js('resources/js/dashboard/verificationform/signers.js', 'public/assets/js/dashboard/verificationform')
    .js('resources/js/dashboard/verificationform/status.js', 'public/assets/js/dashboard/verificationform');

/**
 * Publica los archivos javascript necesarios para la gestion de eventos del usuario
 */
mix.js('resources/js/dashboard/events/edit.js', 'public/assets/js/dashboard/events')
    .js('resources/js/dashboard/events/templates-drafts.js', 'public/assets/js/dashboard/events')
    .js('resources/js/dashboard/events/census.js', 'public/assets/js/dashboard/events');

/**
 * Publica el archivo screen.js y screen.scss para la grabación de pantalla como herramienta individual
 */

mix.sass('resources/sass/dashboard/screen.scss', 'public/assets/css/dashboard');

mix.js('resources/js/dashboard/screen/screen.js', 'public/assets/js/dashboard/screen');
mix.js('resources/js/dashboard/screen/screen-list.js', 'public/assets/js/dashboard/screen');

/**
 * Publica los archivos javascript de manipulación y compartición de archivos
 */
mix.js('resources/js/dashboard/files/file.js', 'public/assets/js/dashboard/files')
    .js('resources/js/dashboard/files/file-list.js', 'public/assets/js/dashboard/files')
    .js('resources/js/dashboard/files/file-share.js', 'public/assets/js/dashboard/files')
    .js('resources/js/dashboard/files/file-share-history.js', 'public/assets/js/dashboard/files')
    .js('resources/js/dashboard/files/file-share-list.js', 'public/assets/js/dashboard/files')
    .js('resources/js/dashboard/files/selected.js', 'public/assets/js/dashboard/files')
    .js('resources/js/dashboard/files/shared-files.js', 'public/assets/js/dashboard/files')
    .scripts(
        ['resources/js/dashboard/files/clipboard.js'],
        'public/assets/js/dashboard/files/clipboard.js'
    );

/**
 * Compila las hojas de estilo de la página de espacio de usuario firmante
 */
mix.sass('resources/sass/workspace/home.scss', 'public/assets/css/workspace')
    .sass('resources/sass/workspace/document.scss', 'public/assets/css/workspace')
    .sass('resources/sass/workspace/page.scss', 'public/assets/css/workspace')
    .sass('resources/sass/workspace/document-request.scss', 'public/assets/css/workspace')
    .sass('resources/sass/workspace/form-data.scss', 'public/assets/css/workspace');

/**
 * Compila las hojas de estilo que se comparten entre zonas de la aplicación
 * Dashboard del usuario y Workspace del usuario firmante
 */
mix.sass('resources/sass/common/video.scss', 'public/assets/css/common')
    .sass('resources/sass/common/passport.scss', 'public/assets/css/common')
    .sass('resources/sass/common/buttons.scss', 'public/assets/css/common')
    .sass('resources/sass/common/colors.scss', 'public/assets/css/common')
    .sass('resources/sass/common/scroll-to-top.scss', 'public/assets/css/common');

/**
 * Publica los archivos javascript de la página de espacio de usuario firmante
 */
mix.js('resources/js/workspace/menu.js', 'public/assets/js/workspace')
    .js('resources/js/workspace/home.js', 'public/assets/js/workspace')
    .js('resources/js/workspace/document.js', 'public/assets/js/workspace')
    .js('resources/js/workspace/page.js', 'public/assets/js/workspace')
    .js('resources/js/workspace/textboxs.js', 'public/assets/js/workspace')
    .js('resources/js/workspace/form-data.js', 'public/assets/js/workspace')
    .js('resources/js/workspace/renew-file.js', 'public/assets/js/workspace')
    .js('resources/js/workspace/request.js', 'public/assets/js/workspace');

/**
 * Copia el archivo webcam-easy.min para public/assets/css/workspace/
 */
mix.copy('resources/js/workspace/webcam-easy.min.js', 'public/assets/js/workspace/');

/**
 * Copia los archivos json de los modelos de face-api para js/common
 */
mix.copyDirectory('resources/js/common/weights/', 'public/assets/js/common/weights');

/**
 * Copia el archivo face-api.min para public/assets/ja/workspace/
 */
mix.copy('resources/js/workspace/face-api.min.js', 'public/assets/js/workspace/');

/**
 * Publica los archivos javascript compartidos entre zonas de la aplicación
 * Dashboard del usuario y Workspace del usuario firmante
 */
mix.js('resources/js/common/video.js', 'public/assets/js/common')
    .js('resources/js/common/audio.js', 'public/assets/js/common')
    .js('resources/js/common/passport.js', 'public/assets/js/common')
    .js('resources/js/common/app-menu.js', 'public/assets/js/common')
    .js('resources/js/common/global-comment.js', 'public/assets/js/common')
    .js('resources/js/common/social-media.js', 'public/assets/js/common')
    .js('resources/js/common/copy-document.js', 'public/assets/js/common')
    .scripts(['resources/js/common/scroll-to-top.js'], 'public/assets/js/common/scroll-to-top.js')
    .scripts([
        'resources/js/common/static-data-datatable.js'
    ], 'public/assets/js/common/static-data-datatable.js');

/**
 * Compila las hojas de estilo de la página de backend del administrador
 */
mix.sass('resources/sass/backend/home.scss', 'public/assets/css/backend');

/**
 * Publica los archivos javascript de la página del administrador
 */
mix.js('resources/js/backend/subscription.js', 'public/assets/js/backend');

mix
    .scripts([
        'node_modules/datatables.net/js/jquery.dataTables.js',
        'node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js',
        'node_modules/datatables.net-responsive/js/dataTables.responsive.js',
        'node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.js',
    ], 'public/assets/js/datatables.js')
    .scripts([
        'resources/js/dashboard/files/file-logs-datatable.js'
    ], 'public/assets/js/file-logs-datatable.js')
    .scripts([
        'resources/js/dashboard/files/sharing-datatable.js',
        'resources/js/dashboard/files/sharing-functionalities.js'
    ], 'public/assets/js/dashboard/files/sharing-datatable.js')
    .scripts([
        'resources/js/dashboard/files/history-datatable.js'
    ], 'public/assets/js/dashboard/files/history-datatable.js')
    .styles([
        'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css',
        'node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.css',
        'resources/sass/common/datatables-custom.scss'
    ], 'public/assets/css/datatable.css');

mix
    .scripts([
        'node_modules/moment/moment.js',
        'node_modules/moment/locale/es.js',
        'resources/js/common/moment.js',
    ], 'public/assets/js/moment.js')

/**
 * En producción añade un id de versión a cada archivos de recurso
 * que hayan sido cargados en el proyecto con la directiva de blade @mix()
 * en lugar de usar la directiva @asset()
 */
if (mix.inProduction()) {
    mix.version();
}

/**
 * Inicia browser sync
 */
//mix.browserSync(homeUrl);
