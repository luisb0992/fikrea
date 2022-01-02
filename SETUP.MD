
# Introducción

versión 1.3
javieru <javi@gestoy.com>
(c) 2021 Retail Servicios Externos

Como crear un entorno de desarrollo PHP 7.4/MySQL en Ubuntu 20.04 para Fikrea.

Requirimientos:

+ Conocimientos básicos de GNU/Linux.
+ Una instalación limpia de Ubuntu 20.10 o 20.04 en una máquina física o en una máquina virtual, por ejemplo, en virtual box, disponible para su descarga para todos los sistemas operativos.

Tiempo aproximado: 30 minutos

Nota: Existe un video donde se realiza el proceso partiendo de una instalación limpia de Ubuntu 20.04. Tambié existe un video explicando Virtual Box.

Observación: Todo el proceso de instalación del entorno de desarrollo ha sido probado utilizando "Ubunto 20.x" tanto en máquina real como en una máquina virtual usando Virtual Box.

## Los permisos en los sistemas operativos UNIX

En los sistema operativos basados en UNIX existe un usuario superadministrador llamado "root" que posee todos los privilegios para actuar sobre el sistema, acceder, editar, modificar, eliminar, crear cualquier archivo. Su carpeta personal se sitúa en la ruta /root (los sistemas operativos basados en UNIX usan "/" como separador de rutas a diferencia de en Windows que se una "\").

Los restantes usuarios poseen una carpeta para guardar sus archivos que cuelga de /home. Por ejemplo para el usuario lucas es /home/lucas, para el usuario alice /home/alice.

A partir de ahora llamamos "user" a tu usuario y  /home/user a tu carpeta de usuario. Sobre ella puedes escribir, crear, editar cualquier archivo.

Para instalar aplicaciones o realizar ciertas accciones provilegiadas necesitamos ser el superadministrador o root para poderlas llevar a cabo. Para ello Unix utiliza el comando "sudo" (que significa "superuser do") que precisamente permite hacer una acción como sui se fuese el superusuario:

sudo apt install net-tools

Esto ejecuta como superusuario, el comando:

apt install net-tools

que se usa, en este caso, para instalar ciertas utilidades básicas de red.

Nota: Precaución. En lo siguiente que fijarse cuando una acción debe ser ejecutada con "sudo" y cuando no.

Los permisos en UNIX se controlan con las órdenes chmod y chown. Los permisos se conceden, en este orden, al usuario, al grupo al que forma parte el usuario, ya la resto de usuarios. Para cada unos de estos tres grupos podemos conceder los permisos de lectura (r), escritura (w) y ejecución (x). De este modo para un archivo llamado my-text.txt con los siguientes permisos:

usuario: lectura (r), escritura(w) y ejecución (x)
grupo  : lectura(r) y ejecución (x)
resto  : lectura(r) y ejecución (x)

el esquema de permisos se representa como:

  rwx r-x r-x

y se representa como un número octal así:

  755

Las combinaciones posibles para cada grupo de permisos son:

    --- 0
    --x 1
    -w- 2
    -wx 3
    r-- 4
    r-x 5
    rw- 6
    rwx 7

La sentencia chmod cambia los permisos de un archivo o directorio (o un conjunto de ellos):

chmod 777 my-text.txt

Concede permisos de lectura, escruitura y ejecución a todo el mundo sobre el archivo my-text.txt.

chmod 400 my-text.txt

Sólo yo podré leer el archivo my-text.txt

La orden:

chown user:grp my-text.txt

Define que el archivo my-text.txt pertenece al usuario user y al grupo grp.

## Virtual BOX

Si se quiere crear ujn máquina virtual con Ubuntu, lo primero será descargar Oracle Virtual Box:

https://www.virtualbox.org/

Luego descargar Ubuntu (edición Desktop):

https://ubuntu.com/download/desktop

Lo que permite obtener una imagen iso, por ejemplo, para Ubuntu 20.10:

ubuntu-20.10-desktop-amd64.iso

de unos 3 GB, y que se usa para instalar.

Luego crearemos una nueva máquina virtual en VirtualBox y la iniciaremos ejcutando la imagen iso descargada de Ubuntu, siguiendo los sencillos pasos de instalación.

Cuando hallamos instalado Ubuntu, mejoraremos la integración instalando las "guest aditions", desde una terminal:

sudo apt-get install virtualbox-guest-utils virtualbox-guest-x11 virtualbox-guest-dkms

Reiniciamos.
## Actualizar los paquetes

sudo apt update

## Instalar PHP 7.4 y sus extensiones

Instalar PHP 7.4:

sudo apt install php7.4

Instalar las extensiones comunes inlcuyendo gd e Imagick:

sudo apt install php-cli php-mysql php-gd php-tidy php-xmlrpc php-imagick php-intl php-mbstring php-curl php-xml php-zip php-bcmath php-json

## Configurar PHP

sudo nano /etc/php/7.4/cli/php.ini

Fijar las siguientes opciones que optimizan el uso de la memoria y permiten que se puedan subir archivos de gran tamaño (2 Gb) al servidor: 

memory_limit       = 1024M
max_file_size      = 2048M
max_post_size      = 2048M
max_execution_time = 600
max_input_time     = 600

Y descomentar la directiva browscap y fijar la ruta del archivo browscap.ini:

browscap = /etc/php/7.4/cli/extra/browscap.ini

El archivo browscap.ini con información de los navegadores y sistemas no viene con php, por lo que hay que descargarlo de https://www.browscap.org. La versión reducida es suficiente para la mayor parte de los propósitos y se llama lite_php_browscap.ini:

https://www.browscap.org/stream?q=Lite_PHP_BrowsCapINI

Nosotros descargaremos este archivo y lo guardaremos en la ruta /etc/php/7.4/cli, creando la carpeta  extra y guardando en ella el archivo con el sombre browscap.ini:

/etc/php/7.4/cli/extra/browscap.ini

Comprobar que el archivo tiene permisos de lectura:

sudo chmod 755 etc/php/7.4/cli/extra/browscap.ini

Se puede comprobar que todo va bien, escribiendo desde la terminal:

php -v

PHP 7.4.9 (cli) (built: Oct 26 2020 15:17:14) ( NTS )
...

que nos da le versión de PHP y leerá el archivo php.ini para comprobar que no hay ningún error.
## Configurar Imagick

La extensión Imagick (ImageMagick) debe ser configurada. Para ello editar el archivo de políticas:

sudo nano /etc/ImageMagick-6/policy.xml

Se debe permitir, explícitamente, el procesamiento de archivos PDF, para lo que se debe comentar la línea:

<policy domain="coder" rights="none" pattern="PDF" />

que debe quedar, por tanto, así:

<!--<policy domain="coder" rights="none" pattern="PDF" />-->

La conversión de archivos en imágenes hace un uso intensivo de la memoria del sisema. Para dar más memoria a Imagick para procesar archivos grandes, por ejemplo, 8 GB, modificar la directiva siguiente:

<policy domain="resource" name="memory" value="8GiB" />

## Instalar Poppler-Utils

sudo apt install poppler-utils

## Instalar Tesseract

Instalar la librería tesseract:

sudo apt install tesseract-ocr

Los archivos de idioma para realizar un reconocimiento óptimo se almacenan en (para la versión 4.0):

/usr/share/tesseract-ocr/4.0/tessdata

Se pueden obtener los archivos de idioma que se necesiten (para la versión 4.0), desde su repositorio en github:

https://github.com/tesseract-ocr/tessdata

Por ejemplo, para el archivo en español:

wget https://github.com/tesseract-ocr/tessdata/raw/master/spa.traineddata

Alternativamente:

sudo apt install tesseract-ocr-spa

## Instalar node y npm

sudo apt install nodejs

sudo apt install npm

## Instalar docker

Existen varias maneras de instalar Docker, pero la más simple y suficiente para el proyecto es:

sudo apt install docker

Aprovechamos para instalar "Docker Compose":

sudo apt install docker-compose

Docker se usa desde la línea de comandos de este modo:

docker [nombre-de-comando] [opciones]

pero requiere de permisos de administrados, es decir, preceder el comando de "sudo" e introducir la contraseña.
Sin embargo, los contenedores de la aplicación van a ser lanzados y parados por la propia aplicación. Por ello debemos dar permisos para poder utilizar docker sin hacer sudo. Para ello:

sudo usermod -aG docker ${USER}

Cerrar la terminal y abrir otra para recargar los privilegios o, mejor, reiniciar. Ahora se podrá lanzar un contenedor con docker run ... sin preceder el comando por sudo.

## Instalar Composer 2.0

Siguiendo https://getcomposer.org/download/, ejecutar:

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

Luego:

sudo mv composer.phar /usr/bin/composer

Ahora en /usr/bin tenemos la aplicación composer. Para comprobar que tenemos composer funcionamiento:

composer -v

nos da la versión e instrucciones sobre su uso.

## Instalar MySQL

Instalaremos la versión MySQL 8.x:

sudo apt install mysql-server

Y configurar la instalación con el script que viene preparado a tal fin:

sudo mysql_secure_installation

donde fijaremos una contraseña (que llamamos 'password') para el usuario root.

Ahora accedemos a myql (como administrador, o sea con sudo):

sudo mysql

Cambiamos la contraseña del usuario actual a 'password' con autenticación nativa (esto se debe a que MySQL 8.x cambia el sistema de autenticación por una basada en SHA-2, pero aplicaciones como PHP usutilizan la autenticación nativa).

Escribir la sentencia SQL:

ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';

y cremos un nuevo usuario root que se conecte desde cualquier equipo fijándose una contraseña 'password', y le otorgamos todos los privilegios:

CREATE USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost';
FLUSH PRIVILEGES;

Salimos de MySQL:

exit

Reniciar MySQL:

sudo service mysql restart

## Crear la base de datos

Entramos en mysql ya sin ser administradores, es decir, como usuario normal (sinn sudo):

mysql -u root -p'password'

Ejecutamos la sentencia SQL de creación de una nueva base de datos de nombre "fikrea":

CREATE DATABASE fikrea;

y salimos:

exit

## Guardar la clave privada

Estamos en nuestra carpeta personal /home/user.

Crear el archivo fikrea.pem:

nano fikrea.pem

y escribir:

-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQEAhs1lfzECmmBFjlfMnLwfekBgfbpAyTcUmf+vhQMD6Y9wGwAe
nlPsncvV6JyOaQxbiDnaNQ0LpWA+gFJoW73nAJZEAxbocFdNQpwQdbhXF/O8CPxI
uFC/ftQHl6IN/GZHIeFnG3GQjDZJnRvbaaNMEuz4F3hylFeHfRXfCaCcDGufzqos
ObNNXjW8c30Upw14/wN+y9mniTRu9iTqYYsZFlUDa8cWovJ6t0pL1f/lp2RPFg02
P8meMLoirZgHpDGp0k+1y8BKRbOsFCsXWfRAnBEu6S3wwPROnuR+YIHXnopkyAl5
T1Mnv6GeZPleAtb2GQMYd5yg0bBsuYiwR+nNZQIDAQABAoIBAFTqH6xucAhU6pvO
3NpCesWkteOdJuBZbQXRFXyBOL6wl1q6yY7MqPmBhF7B98defG4kmzrd298x8E0f
HBPmXRR2k6MqlALoS4fRDWFKXLp+040Jdrl8TBSVda7j61F6EAbcmPmloPth2x83
hBBYSDyHv4PxYKPnOzblRxkmbgY3/1hoBRGtcbwsUwWr/jtlt6KHu2aE7NW/m9JB
qem4pe8z0yk2bwtL+2cnuEEeb5k8gz/TDfRXTDHtuM2e3rTMdnxzWdbd/mXROIKT
BBaFr/VE6zWKPRG6v2cfxybjYm/NCTj2Ty9iIuO7jzwuymo4w1+gVD1j+T6Lm8wN
Me2IBoECgYEAvkR6gthVRO2Wt2P3+M+kOtaopSGAUedRybuNf/FXWqaQemDjJbFu
GSCFYruB7UxFA6/VU5AyApcGsKbZ3aU7Xzk1Dt4uOU1E1BY2T/JlTZ6gZ0PTQbyq
HvZb8sGWdjbSczbBtDSuc6PEO1LWoFP+Wvl3MiNVjPu5N+bhW0CgjeECgYEAtV+A
clXGNw/qJq1DcJCNK/l3Uec3iUu2TD5yDy42dkzVC+3NtWofRJd83RfL6EW3IbN1
EreYpXJDMdyHG9Dq+P38D2vgJ7RwtuROHsl+ZtOqHL2ijPXkWD03TZZIwS+p8pWh
VnQcYHtMA8rkH+7iPrZHSQ8EKFCX4xnGK5tYCAUCgYAWMezMqKnBwalsOTgrCSXS
S0ofATpz03UiAEPwYdnb1ERUUksNEZaTTczhiJgOc3A1juRmvBJpZO692qjdhzHW
2NiMpHRl8NKPnwu05cTVeiSiBzJ0n6qEtB5jwhtoLNr936xW9jJHgRoaBX7N8x5f
xGwYKDEJoi+oCsHNIEvaYQKBgCRCeX1iJIRxjBA7Z1Yjr/VwpCgdn361Qjav7UGX
rY+tAJKn8g4oryOZXX8d+Mfw2BHGvMsHJV8+HqzjN6eZ9r/clv3GRUPq0PXfYrbI
9Zo9Cq9AQjUIG6SAaIP8XYMxsjorK9wKznmDbMj9UOoJafW7nzi95cjhg9wFFRc2
sTy5AoGADtJ5ewvF+u71zhPDom3wxN/U29eQssGJf1gCucXWTZ/z6PvKO2UMh2AQ
uv8PSTELAMKRQN/fqf+AzdisbsHM3Bcq+iWXPwmWUSLU60BhYWDi5D7ymg9sOW5/
NNCHSibU2D0i8XttIiT+TAv2Oc1u+yMUuMiqaep1iG+fpEameHA=
-----END RSA PRIVATE KEY-----

Guardarlo y limitar los permisos a nosotros mismos:

chmod 400 fikrea.pem

Esta clave privada se utiliza para acceder al servidor de producción vía SSH.

## Configurar ssh

Copiar la clave privada al almacen personal:

cp fikrea.pem ~/.ssh/id_rsa_fikrea

Generar la clave pública para la clave privada:

ssh-keygen -y -f fikrea.pem > ~/.ssh/id_rsa_fikrea.pub

Iniciar el agente ssh

eval "$(ssh-agent -s)"

Añadir la clave al agente ssh:

ssh-add ~/.ssh/id_rsa_fikrea

Si todo ha ido bien, al hacer:

ssh ubuntu@fikrea.com

se entra en el servidor. No tocar nada. Salir del servidor con:

exit

## Crear una carpeta para el repositorio

mkdir fikrea

cd fikrea

Estamos en la carpeta que albergará nuestro respositorio.

## Clonar el repositorio

git clone git+ssh://ubuntu@fikrea.com/var/git/www.fikrea.com.git

Accedemos al contenido del repositorio:

cd www.fikrea.com

## Crear el archivo de configuración

Crear el archivo .env de ocnfiguración de la aplicación. Para ello copiamos el archivo de ejemplo .env.example:

cp .env.example .env

editar este archivo de configuración:

nano .env

Lo importante es fijar el nombre del administrador (APP_ADMIN) con nuestra direccuión de correo y la establecer la conexión a la base de datos de MySQL (DB_HOST, DN_USERNAME, DN_PASSWORD) de nuestro equipo. Indicamos pues la configuración de nuestra dirección de correo y nuestra conexión a la base de datos:

APP_NAME=Fikrea
APP_ENV=local
APP_KEY=base64:PZuX6lCXH815JJi3G4nOatDcXHGn41lOsBNdwHEu1UE=
APP_DEBUG=true
APP_URL=https://www.fikrea.com
APP_ADMIN=????????????????

DB_CONNECTION=mysql
DB_HOST=????????????????
DB_PORT=3306
DB_DATABASE=fikrea
DB_USERNAME=????????????????
DB_PASSWORD=????????????????

Completar los valores marcados como: ???????????????? y guardar (Control+O, Control+X). Los valores, para el ejemplo que estamos considerando son:

APP_ADMIN=usuario@fikrea.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=fikrea
DB_USERNAME=root
DB_PASSWORD=password

## Ejecutar el proceso de instalación automático

Ya podemos realizar el proceso de instalación automático que realiza todo el proceso:

npm run setup

Esto crea la estructura de carpetas de la aplicación, descarga las librerías de PHP y de Frontend necesarias, desacraga una imagen de docker, compila las fuentes.

Al finaliza el proceso, el servidor es lanzado:

PHP 7.4.3 Development Server (http://127.0.0.1:8000) started

Se puede ver la aplicaicón en la dirección que se indica. En este caso, http://127.0.0.1:8000.

## Acceder como usuario registrado

La instalación crea ya un usuario registrado que además es administrador de Fikrea:

usuario:    usuario@fikrea.com
contraseña: F1kre@*-+

Puede accederse con este usuario pulsando en la opción "Ingreso" desde la página de inicio mostrada (landing page).