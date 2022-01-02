<?php

namespace Fikrea;

/**
 * La Clase PayPal
 *
 * Para la instalación vía Composer:
 * @link https://packagist.org/packages/paypal/paypal-checkout-sdk
 *
 * Para la documentación:
 * @link https://github.com/paypal/Checkout-PHP-SDK
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */

/**
 * API de Paypal
 */
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalHttp\HttpResponse;
use PayPalHttp\HttpException;

class PayPal extends AppObject
{
    public const MODE_SANDBOX = 'sandbox';
    public const MODE_LIVE    = 'live';

    /**
     * Un pedido de Paypal
     *
     * @var array
     */
    protected array $order;

    /**
     * El constructor
     *
     * @param array $order                      Los datos de un pedido de Paypal
     */
    public function __construct(array $order = [])
    {
        parent::__construct();
        
        $this->order = $order;
    }

    /**
     * Obtiene el entorno de la aplicación que puede ser "sandbox" que es el entorno de
     * desarrollo de Paypal o el entorno "live" o de producción
     *
     * @return SandboxEnvironment|ProductionEnvironment
     */
    final protected function getEnvironment()
    {
        $mode = config('paypal.mode');

        if ($mode == self::MODE_SANDBOX) {
            $clientId     = config('paypal.sandbox.client_id');
            $clientSecret = config('paypal.sandbox.client_secret');

            $environment = new SandboxEnvironment($clientId, $clientSecret);
        } elseif ($mode == self::MODE_LIVE) {
            $clientId     = config('paypal.live.client_id');
            $clientSecret = config('paypal.live.client_secret');

            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }

        return $environment;
    }

    /**
     * Ejecuta la solicitud de pago
     *
     * @return HttpResponse                     Una respuesta HTTP
     *                                          El pago ha sido aprobado por la plataforma
     *
     * @throws HttpException                    La solicitud no es correcta, no está bien formada
     *                                          o es sintácticamente incorrecta
     */
    public function execute():HttpResponse
    {
        // Obtiene el entorno sandbox (pruebas) o live (producción)
        $environment = $this->getEnvironment();

        // Creamos un cliente para crear una petición HTTP REST a PayPal
        $client = new PayPalHttpClient($environment);

        // Creamos una solicitud de pago
        $request = new OrdersCreateRequest;
        $request->prefer('return=representation');
        $request->body =
            [
                // Tenemos la intención de capturar el pago inmediatamente después de que el cliente lo realice
                'intent'         => 'CAPTURE',
                // Unidades de compra
                'purchase_units' =>
                    [
                        [
                            'reference_id' => $this->order['order'],
                            // Cantidad a pagar y moneda
                            // Se fija el número de decimales a dos
                            'amount' =>
                                [
                                    'value'         => number_format($this->order['total'], 2, '.', ''),
                                    'currency_code' => config('paypal.currency'),
                                ]
                        ]
                    ],
                // Personalización de la transacción
                'application_context' =>
                    [
                        // Dirección a la que el cliente es dirigido si el proceso de pago falla
                        'cancel_url' => $this->order['cancel_url'],
                        // Dirección a la que el cliente es dirigido para finalizar el pago
                        'return_url' => $this->order['return_url']
                    ]
            ];

        return $client->execute($request);
    }

    /**
     * Verifica si un pago ha sido completado
     *
     * @param  string $token                    El token del pedido u orden de pago
     *
     * @return bool                             true si el pago se ha completado con éxito
     *                                          false en caso contrario
     *
     * @throws HttpException                    La solicitud no puede ser procesada
     *                                          No autorizado, permiso denegado
     */
    public function completed(string $token):bool
    {
        // Obtiene el entorno sandbox (pruebas) o live (producción)
        $environment = $this->getEnvironment();

        // Creamos un cliente para crear una petición HTTP REST a PayPal
        // en función del entorno de despliegue de la aplicación
        $client      = new PayPalHttpClient($environment);

        $request = new OrdersCaptureRequest($token);
        $request->prefer('return=representation');
        
        // Llama a la API para completar el proceso de pago
        $response = (object) $client->execute($request);

        // Si el proceso de pago ha sido completado con éxito o no
        return $response->result->status == 'COMPLETED';
    }

    /**
     * Obtiene un pedido u orden de pago
     *
     * @param  string $token                    El token del pedido u orden de pago
     *
     * @return object                           El pedido u orden de pago
     *
     * @throws HttpException                    La solicitud no puede ser procesada
     *                                          No autorizado, permiso denegado
     */
    public function get(string $token):object
    {
        // Obtiene el entorno sandbox (pruebas) o live (producción)
        $environment = $this->getEnvironment();

        // Creamos un cliente para crear una petición HTTP REST a PayPal
        // en función del entorno de despliegue de la aplicación
        $client      = new PayPalHttpClient($environment);

        $request = new OrdersGetRequest($token);
  
        // Llama a la API para completar el proceso de pago
        $response = (object) $client->execute($request);

        // Los detalles del pedido (orden de pago)
        return $response->result;
    }
}
