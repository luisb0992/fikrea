<?php

/**
 * SubscriptionController
 *
 * Controlador para las subscripciones
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use App\Utils\FileUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

use App\Events\SubscriptionRenewed;

/**
 * Paypal
 */

use Fikrea\PayPal;
use PayPalHttp\HttpException;

/**
 * Modelos necesarios
 */

use App\Models\Plan;
use App\Models\Order;
use App\Models\Subscription;

/**
 * Fikrea
 */

use Fikrea\ModelAndView;

use PDF;

class SubscriptionController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra el estado de la subscripción actual
     *
     * @return string                           Una vista
     */
    public function show(): string
    {
        $mav = new ModelAndView('dashboard.subscription.subscription-show');

        return $mav->render();
    }

    /**
     * Muestra la vista que la subscripción debe ser renovada para seguir utilizando la aplicación
     *
     * @return string
     */
    public function subscriptionMustBeRenew(): string
    {
        $mav = new ModelAndView('dashboard.subscription.subscription-must-be-renew');
        return $mav->render();
    }

    /**
     * Muestra la vista para renovar la subscripción actual
     * El usuario debe seleccionar el plan y el tiempo
     *
     * @return string
     */
    public function select(): string
    {
        $mav = new ModelAndView('dashboard.subscription.subscription-select');

        // los planes diferentes al Trial y al FIKREA
        $plans = Plan::all()->filter(fn ($plan) => !$plan->isTrial() && $plan->id !== 3);

        return $mav->render(
            [
                'plans'             => $plans,
                // Obtiene el precio mensual por cambio a un plan superior
                // El cambio de plan "premium" a "pro"
            ]
        );
    }

    /**
     * Redirige a la plataforma para efectuar el pago de la subscripción
     *
     * @return RedirectResponse
     */
    public function pay(): RedirectResponse
    {
        // Obtiene el usuario
        $user = Auth::user();

        // Obtiene los datos de la subscripción
        $subscription = [
            'user'              => $user,
            'subscription_id'   => $user->subscription->id,
            'plan_id'           => request()->input('plan'),
            'plan'              => Plan::findOrFail(request()->input('plan')),
            'months'            => request()->input('months'),

            // Define un número de pedido u orden de pago
            // utilizando la fecha actual y el id del usuario
            // de manera que es único y correlativo
            'order'             => (new \DateTime)->format('YmdHi') . Str::padLeft($user->id, 8, '0'),
        ];

        // Obtenemos el tipo de subscripción, es decir, si se mantiene, se amplia o de devalua
        // la subscripción actual, de acuerdo, con la tabla de tipos de pedidos
        // @see App\Models\Order
        if ($user->subscription->plan->id == Plan::TRIAL || $user->subscription->plan->id == $subscription['plan_id']) {
            $subscription['type'] = Order::TYPE_PLAN_MAINTAIN;
        } elseif ($subscription['plan_id'] > $user->subscription->plan->id) {
            $subscription['type'] = Order::TYPE_PLAN_UPGRADE;
        } else {
            $subscription['type'] = Order::TYPE_PLAN_DOWNGRADE;
        }

        // Calcula el importe de la subscripción
        // se pasa la subscripción actual y los datos de la nueva subscripción
        $this->getAmount($user->subscription, $subscription);

        // Crea una solicitud de pago en Paypal
        $paypal = new Paypal(
            [
                'items'        =>
                [
                    [
                        'name'      => Lang::get('Subscripción a :app', ['app' => config('app.name')]),
                        'price'     => $subscription['price'],
                        'qty'       => $subscription['units'],
                    ]
                ],

                'order'       => $subscription['order'],

                'total'       => $subscription['amount'],

                'description' => Lang::get('Subscripción a :app', ['app' => config('app.name')]),

                'return_url'  => url(route('subscription.payment.success')),

                'cancel_url'  => url(route('subscription.payment.cancel')),
            ]
        );

        /**
         * Obtiene la respuesta de Paypal
         */
        try {
            $response = (object) $paypal->execute();
        } catch (HttpException $e) {
            abort(400, Lang::get('La solicitud a Paypal no es correcta'));
        }

        // Fija el token para la orden de pedido
        $subscription['token']  = $response->result->id;

        // Crea un pedido para asociarle una orden de pago de Paypal
        $user->orders()->create($subscription);

        // La plataforma de Paypal proporciona un conjunto de enlaces para la transacción
        $links = (array) $response->result->links;

        // Obtiene la dirección de Paypal para la confirmación del pago o "approval Link"
        $approvalLink = current(array_filter($links, fn ($link) => $link->rel == 'approve'));

        return redirect($approvalLink->href);
    }

    /**
     * Añade el importe de la subscripción
     *
     * @param Subscription $current      La subscripción actual del usuario
     * @param array        $subscription Los datos de una subscripción
     *
     * @return void
     */
    protected function getAmount(Subscription $current, array &$subscription): void
    {
        // Obtiene el precio unitario de la subscripción
        $subscription['price'] = $subscription['months'] < 12 ?
            $subscription['plan']->monthly_price : $subscription['plan']->yearly_price;

        // El número de unidades adquiridas
        // Es el número de meses, pero si la subscripción es anual, vale la unidad (1 año)
        $subscription['units'] = $subscription['months'] < 12 ? $subscription['months'] : 1;

        // El importe de la venta sin impuestos (IVA)
        $subscription['amountTaxExcluded'] = $subscription['price'] * $subscription['units'];

        // Si el plan ha cambiado
        // Obtiene el número de meses de ampliación de la subscripción
        // A partir de los 30 primeros días por cada mes se efectúa un incremento
        if ($current->plan != $subscription['plan']) {
            $subscription['change_months'] = intval($current->remainingDays / 30);

            // Calcula el importe adicional por ampliación de subscripción
            $subscription['aditional_amount'] = round($subscription['change_months'] * $current->plan->change_price, 2);
            $subscription['amountTaxExcluded'] += $subscription['aditional_amount'];
        }

        // Los impuestos aplicables a la venta (IVA)
        $subscription['tax']    = round($subscription['amountTaxExcluded'] * $subscription['plan']->tax / 100, 2);

        // El importe total a pagar impuestos (IVA) incluidos
        $subscription['amount'] = $subscription['amountTaxExcluded'] + $subscription['tax'];
    }

    /**
     * Cuando la plataforma Paypal confirma que el pago ha sido aprovado
     *
     * Este es el webhook que se dispara cuando se realiza una petición de pago
     * para confirmar
     *
     * En la cuenta se ha creado tanto para el modo Sandbox como para el modo Live
     * el webhook correspondiente
     *
     * @link https://developer.paypal.com
     *
     * Usuario    : mikel@retailexternal.com
     * Contraseña : ************************
     * MerchantId : DZSLQ7NY8L2YL
     * Aplicación : Fikrea
     *
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     * | Modo      | Webhook                            | Webhook ID            | Events Tracked         |
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     * | sandbox   | https://www.fikrea.com/subscription| 76T97469ER7048048     | * All Events           |
     * |           | /payment/approved                  |                       |
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     * | live      | https://www.fikrea.com/subscription| 7L144643RY138393W     | * All Events           |
     * |           | /payment/approved                  |                       |                        |
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *
     * Para más información sobre los webhooks de Paypal puede consultase:
     *
     * @link https://developer.paypal.com/docs/api-basics/notifications/webhooks/rest/
     *
     *
     * Para pruebas de pago (sandbox) puede utilizarse el siguiente usuario:
     *
     * usuario (dirección de correo) : sb-zcmbo2251228@personal.example.com
     * contraseña                    : Demo.1234
     * nombre                        : John Doe
     *
     * La solicitud que recibe el Webhook de Paypal es de tipo POST y tiene la siguiente forma:
     *
     *   {
     *       "id": "WH-3FL530633W811294S-3P1084084J113925N",
     *       "create_time": "2020-12-17T18:52:04.090Z",
     *       "resource_type": "checkout-order",
     *       "event_type": "CHECKOUT.ORDER.APPROVED",
     *       "summary": "An order has been approved by buyer",
     *       "resource": {
     *           "create_time": "2020-12-17T18:51:13Z",
     *           "purchase_units": [
     *               {
     *                   "reference_id": "20201217195100000001",
     *                   "amount": {
     *                       "currency_code": "EUR",
     *                       "value": "12.08"
     *                   },
     *                   "payee": {
     *                       "email_address": "mikel@retailexternal.com",
     *                       "merchant_id": "DZSLQ7NY8L2YL"
     *                   },
     *                   "shipping": {
     *                       "name": {
     *                           "full_name": "John Doe"
     *                       },
     *                       "address": {
     *                           "address_line_1": "calle Vilamarín 76993- 17469",
     *                           "admin_area_2": "Albacete",
     *                           "admin_area_1": "Albacete",
     *                           "postal_code": "02001",
     *                           "country_code": "ES"
     *                       }
     *                   }
     *               }
     *           ],
     *           "links": [
     *               {
     *                   "href": "https://api.sandbox.paypal.com/v2/checkout/orders/41874430WA863730S",
     *                   "rel": "self",
     *                   "method": "GET"
     *               },
     *               {
     *                   "href": "https://api.sandbox.paypal.com/v2/checkout/orders/41874430WA863730S",
     *                   "rel": "update",
     *                   "method": "PATCH"
     *               },
     *               {
     *                   "href": "https://api.sandbox.paypal.com/v2/checkout/orders/41874430WA863730S/capture",
     *                   "rel": "capture",
     *                   "method": "POST"
     *               }
     *           ],
     *           "id": "41874430WA863730S",
     *           "intent": "CAPTURE",
     *           "payer": {
     *               "name": {
     *                   "given_name": "John",
     *                   "surname": "Doe"
     *               },
     *               "email_address": "sb-zcmbo2251228@personal.example.com",
     *               "payer_id": "35K9CQRVZQMKW",
     *               "address": {
     *                   "country_code": "ES"
     *               }
     *           },
     *           "status": "APPROVED"
     *       },
     *       "status": "PENDING",
     *       "transmissions": [
     *           {
     *               "webhook_url": "https://www.fikrea.com/subscription/payment/approved",
     *               "http_status": 405,
     *               "reason_phrase": "HTTP/1.1 200 Connection established",
     *              "response_headers": {
     *                   "allow": "GET, HEAD",
     *                   "Server": "Apache/2.4.41 (Ubuntu)",
     *                   "Cache-Control": "no-cache, private",
     *                   "Connection": "close",
     *                   "Date": "Thu, 17 Dec 2020 18:56:12 GMT",
     *                   "Content-Type": "text/html; charset=UTF-8"
     *               },
     *               "transmission_id": "f67cc1f0-4098-11eb-b4ad-8d37926f3ff4",
     *               "status": "PENDING",
     *               "timestamp": "2020-12-17T18:52:07Z"
     *           }
     *       ],
     *       "links": [
     *           {
     *               "href": "https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-3FL530633W811294S-3P1084084J113925N",
     *               "rel": "self",
     *               "method": "GET",
     *               "encType": "application/json"
     *           },
     *           {
     *               "href": "https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-3FL530633W811294S-3P1084084J113925N/resend",
     *               "rel": "resend",
     *               "method": "POST",
     *               "encType": "application/json"
     *           }
     *       ],
     *       "event_version": "1.0",
     *       "resource_version": "2.0"
     *   }
     *
     * @return void
     */
    public function paymentApproved(): void
    {
        // Obtiene la información de la solicitud
        $input  = file_get_contents('php://input');

        // Si la solicitud no tiene datos
        if (!$input) {
            abort(403, Lang::get('Solicitud sin datos'));
        }

        // Decodifica la solicitud
        $request = json_decode($input);

        // Si no se recibe un evento del tipo CHECKOUT.ORDER.APPROVED finaliza con error
        if ($request->event_type != 'CHECKOUT.ORDER.APPROVED') {
            abort(403);
        }

        // Obtenemos el pedido por su token
        $order = Order::where('token', $request->resource->id)->firstOrFail();

        // El pago del pedido ha sido aprobado por Paypal
        $order->approve();

        // Si el pedido no está pagado
        if ($order->isNotPayed()) {
            // Marca el pedido como pagado
            $order->pay();

            // Actualiza la subscripción con el pedido realizado
            $order->subscription->renew($order);
        }
    }

    /**
     * El pago ha sido aprobado por la plataforma y se procede a completarlo
     *
     * @param Request $request La solicitud
     *
     * @return string|RedirectResponse          Una vista informando que el pago ha sido realizado
     *                                          y la subscripción actualizada o
     *                                          una redirección si el pago no se ha realizado
     */
    public function paymentSuccess(Request $request)
    {
        // Obtiene el usuario
        $user = Auth::user();

        // Obtenemos el token de la solicitud
        $token = $request->get('token');

        // Obtenemos el pedido por el token
        $order  = Order::where('token', $token)->firstOrFail();

        // Se comprueba si el pago se ha completado
        $paypal = new Paypal;

        try {
            $paypal->completed($token);
        } catch (HttpException $e) {
            // Carga la vista que indica que el proceso de renovación de la subscripción ha fallado
            $mav = new ModelAndView('dashboard.subscription.subscription-renew-failed');

            return $mav->render(
                [
                    'order' => $order,
                ]
            );
        }

        //
        // El webhook tarda un tiempo en ser disparado, y en desarrollo no se recibirá respuesta
        // porque el servidor de Paypal no podrá conectar con nuestro servidor local
        //
        // Si el pago ya fue completado anteriormente no se volverá
        // fallará el método completed
        //

        // Pero la plataforma indica que el proceso de pago ha sido completado
        // por lo que lo marcamos como aprobado por la plataforma y que ha sido pagado

        if ($order->isNotPayed()) {
            // Aprobar y pagar el pedido
            $order->approve();
            $order->pay();

            // Actualiza la subscripción con el pedido realizado
            $order->subscription->renew($order);
        }

        // Desbloquear archivos, si existe espacio suficiente
        FileUtils::unlockFiles();

        // Lanza el evento de subscripción renovada
        event(new SubscriptionRenewed($user, $order));

        // Carga la vista que indica que se ha renovado la subscripción con éxito
        $mav = new ModelAndView('dashboard.subscription.subscription-renew-success');

        return $mav->render(
            [
                'order' => $order,
            ]
        );
    }

    /**
     * El pago se ha cancelado (el intento de pago ha fallado)
     *
     * @param Request $request La solicitud
     *
     * @return string                           Una vista informando que el pago se ha cancelado
     */
    public function paymentCancel(Request $request): string
    {
        // Obtenemos el token de la solicitud
        $token = $request->get('token');

        // Obtenemos el pedido por el token
        $order  = Order::where('token', $token)->firstOrFail();

        // Carga la vista que indica que el proceso de renovación de la subscripción ha fallado
        $mav = new ModelAndView('dashboard.subscription.subscription-renew-failed');

        return $mav->render(
            [
                'order' => $order,
            ]
        );
    }

    /**
     * Obtiene la factura de una subscripción
     *
     * @param int $id El id del pedido u orden de pago
     *
     * @return Response                         Una respuesta
     */
    public function bill(int $id): Response
    {
        // Obtenemos el pedido
        $order  = Order::findOrFail($id);

        // Comprueba si el usuario acual puede acceder a la factura
        $this->authorize('bill', $order);


        $pdf = PDF::loadView(
            'dashboard.pdf.bill',
            [
                'order'   => $order,            // El pedido
            ]
        );

        // Descarga la factura
        return $pdf->download("{$order->order}.pdf");
    }
}
