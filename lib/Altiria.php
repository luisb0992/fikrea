<?php

namespace Fikrea;

use Illuminate\Support\Facades\Log;

/**
 * La Clase SMS
 *
 * Envia un mensaje SMS a uno o varios destinatarios utilizando el servicio de envío de SMS de Altiria
 *
 * Los SMS no se envían en el entorno de desarrollo local, ni en testing pero si en los restantes entornos
 *
 * En el entorno de desarrollo puede consultarse el archivo de registro sms.log
 * en la carpeta de almacanenamiento de archivos de registro (log) para conocer
 * los destinatarios y contenido de los mensajes
 *
 * @link https://www.altiria.com/
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 * @example
 *
 * use Fikrea\Altiria;
 *
 * $sms = new Altiria;
 *
 * El número de teléfono debería ir prefijado por el código internacional
 * Si no lo está se añade automáticamente
 *
 * $destination = '34655315879';
 * $response = $sms->send($destination, 'Mensaje de prueba');
 *
 */

class Altiria extends AppObject implements Interfaces\Sms
{
    /**
     * La url
     *
     * @var string
     */
    protected string $url;

    /**
     * El domain Id
     *
     * @var string
     */
    protected string $domain;

    /**
     * El login
     *
     * @var string
     */
    protected string $login;

    /**
     * El password
     *
     * @var string
     */
    protected string $password;

    /**
     * El sender ID
     *
     * @var string
     */
    protected string $sender;

    /**
     * Debug
     *
     * @var bool
     */
    protected bool $debug;

    /**
     * El contenido del mensaje
     *
     * @var string
     */
    protected string $message;

    /**
     * El número de teléfono o números de teléfono de destino
     *
     * @var string
     */
    protected string $destination;

    /**
     * El constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        // Obtiene la configuración
        $this->getConfig();
    }

    /**
     * Obtiene la configuración del servicio
     *
     * @return void
     */
    protected function getConfig():void
    {
        $this->url      = config('sms.altiria.url');
        $this->domain   = config('sms.altiria.domain');
        $this->login    = config('sms.altiria.login');
        $this->password = config('sms.altiria.password');
        $this->sender   = config('sms.altiria.sender');
        $this->debug    = config('sms.altiria.debug');
    }

    /**
     * Envia el SMS
     *
     * @param mixed  $destination                El número de destino, varios destinos separados por comas
     *                                           o un array de destinos
     * @param string $message                    El mensaje a enviar
     *
     * @return bool                              Devuelve true si el envío del SMS es satisfactorio
     *                                           false en caso contrario
     */
    public function send($destination, string $message):bool
    {
        // En entornos que no son de producción los SMS no se envían
        if (app()->environment() == 'local') {
            if (is_array($destination)) {
                $destination = implode(',', $destination);
            }

            Log::channel('sms')
                ->info("Mensaje SMS enviado a los siguientes destinatarios: {$destination}\n {$message}");

            return true;    // Porque aunque no se envíe el sms, se registra el envío en db
        }

        // Fija los atributos del SMS
        $this->destination = $destination;
        $this->message     = urlencode($message);
    
        $params = "&msg={$this->message}&concat=true";

        // Como destinatarios se admite un array de teléfonos,
        // una cadena de teléfonos separados por comas o un único teléfono
        if (is_array($destination)) {
            foreach ($destination as $phone) {
                // Añade el prefijo 34 internacional si no lo está
                if (substr($phone, 0, 2) != '34') {
                    $phone = "34{$phone}";
                }
                $this->logMsg("Add destination {$phone}");
                $params.="&dest={$phone}";
            }
        } elseif (strpos($destination, ',') !== false) {
            $destinationTmp= '&dest='.str_replace(',', '&dest=', $destination).'&';
            $params.=$destinationTmp;
            $this->logMsg("Add destination {$destinationTmp}");
        } else {
             // Añade el prefijo 34 internacional si no lo está
            if (substr($destination, 0, 2) != '34') {
                $destination = "34{$destination}";
            }
            $this->logMsg("Add destination {$destination}");
            $params.="&dest={$destination}";
        }

        // No es posible utilizar el remitente en América pero sí en España y Europa
        if (!empty($this->sender)) {
            $params.="&senderId={$this->sender}";
            $this->logMsg("Add senderId {$this->sender}");
        } else {
            $this->logMsg("NO senderId");
        }

        // En testing, el SMS no es enviado, pero se devuelve true
        // si la ejecuación del método no posee errores
        if (app()->environment() == 'testing') {
            return true;
        } else {
            $ret= $this->cURL('sendsms', $params);
        }

        return $ret;
    }

    /**
     * Obtiene los créditos disponibles
     *
     * Normalmente un credito es un SMS, pero depende de los paises
     * @link https://www.altiria.com/cobertura-internacional-envio-sms/
     *
     * @return float                            El número de créditos disponibles
     */
    public function getCredit():float
    {
        return floatval(str_replace("OK credit(0):", "", $this->cURL('getcredit')));
    }

    /**
     * Añade una entrada al log
     *
     * @param string $msg
     *
     * @return void
     */
    public function logMsg(string $msg)
    {
        if ($this->getDebug()=== true) {
            error_log("\n".date(DATE_RFC2822)." : ".$msg."\r\n", 3, "altiria.log");
        }
    }

    /**
     * Realiza una llamada cURL
     *
     * @param string $comando                       Acción que se va a realizar
     * @param string $params                        Otros parámetros necesarios para realizar la acción
     *
     * @return bool                                 Devuelve true si la llamada cURL es satisfactoria
     */
    public function cURL(string $comando, string $params = '')
    {
        $return=false;
        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded; charset=UTF-8'));
        curl_setopt($ch, CURLOPT_HEADER, false);
        // Max timeout in seconds to complete http request
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);

        $urlParams='cmd='. $comando .
            '&domainId=' .$this->getDomain().
            '&login='    .$this->getLogin().
            '&passwd='   .$this->getPassword().
            $params;
        
        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlParams);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
            // La respuesta del servidor de Altiria
            $this->logMsg("Server Altiria response: ".$httpResponse);
    
            if (strstr($httpResponse, "ERROR errNum")) {
                $this->logMsg("Error on". $comando .": ".$httpResponse);
                $return = false;
            } else {
                $return = $httpResponse;
            }
        } else {
            $this->logMsg("Error on". $comando .": ".curl_error($ch).'('.curl_errno($ch).')'.$httpResponse);
            $return = false;
        }

        curl_close($ch);

        return $return;
    }
}
