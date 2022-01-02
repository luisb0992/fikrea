<?php

/**
 * Controlador del Backend o zona del usuario administrador del sitio
 *
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

/**
 * Modelos requeridos
 */
use App\Models\User;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Sms;
use App\Models\Subscription;

use Fikrea\ModelAndView;

use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Carbon\Carbon;

use Fikrea\Altiria;

class BackendController extends Controller
{
    use Traits\Statistical;

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muuestra la página de inicio del backend del administrador del sitio
     *
     * @return string                           Una vista
     */
    public function home(): string
    {
        $mav = new ModelAndView('backend.home');

        // Añade las estadísticas básicas a la vista
        // y que son compartidas por todas las vistas pues se muestran en el menú del backend
        $this->appendStats($mav);

        // Obtiene las analíticas particulares de la aplicación
        $analytical = (object)
        [
            // Usuarios invitados creados hoy
            'usersToday'  =>
            User::whereDate('created_at', Carbon::today()->toDateString())
                ->where('email_verified_at', null)->get(),
            // Usuarios que se ha registrado hoy
            'registeredToday' =>
            User::registered()->whereDate('email_verified_at', Carbon::today()->toDateString())->get(),
            // Clientes que han realizado un pago hoy
            'payedToday' =>
            User::clients()->get()->filter(
                function ($client) {
                    return $client->subscription->payed_at &&  $client->subscription->payed_at->format('d-m-Y')
                        ==
                        Carbon::today()->format('d-m-Y');
                }
            ),
            // Facturación diaria
            // Suma total de las cantidades pagadas hoy
            'billingToday' =>
            User::clients()->get()->sum(
                function ($client) {
                    return $client->subscription->payed_at &&  $client->subscription->payed_at->format('d-m-Y')
                        ==
                        Carbon::today()->format('d-m-Y')
                        ?
                        $client->subscription->payment
                        :
                        0;
                }
            ),
        ];

        $mav->append(
            [
                'analytical' => $analytical,
            ]
        );

        return $mav->render();
    }

    /**
     * Muestra la página con la lista de todos los usuarios incluídos invitados
     *
     * @return string                           Una vista
     */
    public function users(): string
    {
        $mav = new ModelAndView('backend.users');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render(
            [
                'users' => User::orderBy('lastname')->paginate(config('backend.list.pagination')),
            ]
        );
    }

    /**
     * Muestra la página con la lista de los usuarios registrados
     *
     * @return string                           Una vista
     */
    public function registered(): string
    {
        $mav = new ModelAndView('backend.registered');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render(
            [
                'users' => User::registered()->paginate(config('backend.list.pagination')),
            ]
        );
    }

    /**
     * Muestra la página con la lista de clientes
     *
     * @return string                           Una vista
     */
    public function clients(): string
    {
        $mav = new ModelAndView('backend.clients');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render(
            [
                'users' => User::clients()->orderBy('lastname')->paginate(config('backend.list.pagination')),
            ]
        );
    }

    /**
     * Deshabilita una cuenta de usuario
     *
     * @param int $id El id del usuario a deshabilitar
     *
     * @return RedirectResponse                 Una redirección
     */
    public function disableAccount(int $id): RedirectResponse
    {
        // Obtiene el usuario cuya cuenta hay que cerrar
        $user = User::findOrFail($id);

        // El usuario es desactivado
        $user->disable();

        return back()->with('message', Lang::get('La cuenta de usuario ha sido deshabilitada'));
    }

    /**
     * Habilita una cuenta de usuario
     *
     * @param int $id El id del usuario a habilitar
     *
     * @return RedirectResponse                 Una redirección
     */
    public function enableAccount(int $id): RedirectResponse
    {
        // Obtiene el usuario cuya cuenta hay que cerrar
        $user = User::findOrFail($id);

        // El usuario es desactivado
        $user->enable();

        return back()->with('message', Lang::get('La cuenta de usuario ha sido habilitada'));
    }

    /**
     * Muestra la página con la lista de subscripciones
     *
     * @return string                           Una vista
     */
    public function subscriptions(): string
    {
        $mav = new ModelAndView('backend.subscriptions');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render(
            [
                'users' => User::clients()->orderBy('lastname')->paginate(config('backend.list.pagination')),
            ]
        );
    }

    /**
     * Muestra la página con la lista de facturas pagadas
     *
     * @return string                           Una vista
     */
    public function orders(): string
    {
        $mav = new ModelAndView('backend.orders');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render(
            [
                'orders' => Order::payed()->orderBy('payed_at')->paginate(config('backend.list.pagination')),
            ]
        );
    }

    /**
     * Edita una suscripción
     *
     * @param int $id El id de la suscripción
     *
     * @return string                           Una vista
     */
    public function editSubscription(int $id): string
    {
        // Obtiene la suscripción
        $subscription = Subscription::findOrFail($id);

        $this->authorize('edit', $subscription);

        $mav = new ModelAndView('backend.subscription');

        // Añade las estadísticas básicas a la vista
        // y que son compartidas por todas las vistas pues se muestran en el menú del backend
        $this->appendStats($mav);

        return $mav->render(
            [
                'plans'         => Plan::all(),
                'subscription'  => $subscription,
            ]
        );
    }

    /**
     * Guarda una suscripción
     *
     * @param Request $request
     * @param int $id El id de la suscripción
     *
     * @return RedirectResponse                 Una redirección
     */
    public function saveSubscription(Request $request, int $id): RedirectResponse
    {
        // Obtiene la suscripción
        $subscription = Subscription::findOrFail($id);

        $this->authorize('save', $subscription);

        // Obtiene los valores de la suscripción
        $data = request()->validate(
            [
                'plan_id'           => 'required|integer',
                'custom_disk_space' => 'nullable|integer',
                'starts_at'         => 'required|date|before:ends_at',
                'ends_at'           => 'required|date|after:starts_at',
            ]
        );

        // Guarda la suscripción
        $subscription->update([
            'plan_id'        => $request->plan_id,
            'starts_at'      => $request->starts_at,
            'ends_at'        => $request->ends_at,
        ]);

        // Guarda el almacenamiento personalizado definido para el usuario
        $user = $subscription->user;
        $user->custom_disk_space = $data['custom_disk_space'];

        $user->save();

        // Redirige a la lists de subscripciones
        return redirect()->route('backend.subscriptions.list')
            ->with('message', Lang::get('La suscripción se ha guardado con éxito'));
    }

    /**
     * Cree la suscripcion
     *
     * @return string                           Una vista
     */
    public function subscriptionCreate(): string
    {
        $mav = new ModelAndView('backend.subscriptionCreate');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render([
            'users' => User::whereNotIn('name', ['Usuario sin Registro'])->get(),
            'plans' => Plan::all(),
        ]);
    }

    /**
     * Guarda las suscripciones creados
     *
     * @param Request $request
     *
     * @return RedirectResponse                 Una redirección
     */
    public function subscriptionStore(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'user_id'        => 'required',
                'plan_id'        => 'required',
                'months'         => 'required',
                'starts_at'      => 'required',
                'ends_at'        => 'required',
                'payment'        => 'required',
                'payed_at'       => 'required',
            ]
        );
        //Obtengo el ultimo plan
        $lastSubscription = Subscription::all()->last();
        
        $subscription = new Subscription;

        $subscription->id = $lastSubscription->id + 1;
        $subscription->user_id = $request->user_id;
        $subscription->plan_id = $request->plan_id;
        $subscription->months = $request->months;
        $subscription->starts_at = $request->starts_at;
        $subscription->ends_at = $request->ends_at;
        $subscription->payment = $request->payment;
        $subscription->payed_at = $request->payed_at;

        $subscription->save();
       

        // Redirigir a la lista de planes
        return redirect()->route('backend.subscriptions.list')
            ->with('message', Lang::get('El plan se ha creado con éxito'));
    }

    /**
     * Muuestra la lista de planes
     *
     * @return string                           Una vista
     */
    public function plans(): string
    {
        $mav = new ModelAndView('backend.plans.plans');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render([
            'plans' => Plan::all(),
        ]);
    }

    /**
     * Cree los planes para la suscripcion
     *
     * @return string                           Una vista
     */
    public function createPlans(): string
    {
        $mav = new ModelAndView('backend.plans.plansCreate');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render();
    }

    /**
     * Guarda los planes creados
     *
     * @param Request $request La solicitud
     *
     * @return RedirectResponse                 Una redirección
     */
    public function storePlans(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name'            => 'required|string|max:150',
                'disk_space'      => 'required',
                'signers'         => 'nullable',
                'monthly_price'   => 'required',
                'yearly_price'    => 'required',
                'change_price'    => 'required',
                'tax'             => 'required',
                'trial_period'    => 'required',
            ]
        );
        //Obtengo el ultimo plan
        $lastPlan = Plan::all()->last();
        
        $plan = new Plan;

        $plan->id = $lastPlan->id + 1;
        $plan->name = $request->name;
        $plan->disk_space = $request->disk_space;
        $plan->signers = $request->signers;
        $plan->monthly_price = $request->monthly_price;
        $plan->yearly_price = $request->yearly_price;
        $plan->change_price = $request->change_price;
        $plan->tax = $request->tax;
        $plan->trial_period = $request->trial_period;

        $plan->save();
       

        // Redirigir a la lista de planes
        return redirect()->route('backend.plans.plans')
            ->with('message', Lang::get('El plan se ha creado con éxito'));
    }

    /**
     * Vista para actualizar los planes
     *
     *
     * @return string                           Una vista
     */
    public function editPlans($id): string
    {

        $mav = new ModelAndView('backend.plans.plansUpdate');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render([
            'plan' => Plan::where('id', $id)->get()->first(),
        ]);
    }

    /**
     * Actualiza los planes
     *
     * @param Request $request La solicitud
     * @param $id El id
     *
     * @return RedirectResponse                 Una redirección
     */
    public function updatePlans(Request $request, $id): RedirectResponse
    {
        $request->validate(
            [
                'name'            => 'required|string|max:150',
                'disk_space'      => 'required',
                'signers'         => 'nullable',
                'monthly_price'   => 'required',
                'yearly_price'    => 'required',
                'change_price'    => 'required',
                'tax'             => 'required',
                'trial_period'    => 'required',
            ]
        );
        //Actualizo el plan
        Plan::where('id', $id)->update([
            'name'            => $request->name,
            'disk_space'      => $request->disk_space,
            'signers'         => $request->signers,
            'monthly_price'   => $request->monthly_price,
            'yearly_price'    => $request->yearly_price,
            'change_price'    => $request->change_price,
            'tax'             => $request->tax,
            'trial_period'    => $request->trial_period,
        ]);
        
       

        // Redirigir a la lista de planes
        return redirect()->route('backend.plans.plans')
            ->with('message', Lang::get('El plan se ha creado con éxito'));
    }

    /**
     * Elimina los planes
     *
     *
     * @return RedirectResponse                 Una redirección
     */
    public function deletePlans($id): RedirectResponse
    {
        //Obtengo el plan
        $plan = Plan::where('id', $id)->get()->first();
        //Elimino el plan
        $plan->delete();
        // Redirigir a la lista de planes
        return redirect()->route('backend.plans.plans')
            ->with('message', Lang::get('El plan se ha eliminado con éxito'));
    }

    /**
     * Muestra la lista de smses
     *
     * @return string                           Una vista
     */
    public function smses(): string
    {
        $mav = new ModelAndView('backend.sms.smses');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);
        
        // Obtengo el crédito que tengo en el api de mensajería
        $smsApi = new Altiria;
        $credits = $smsApi->getCredit() ?? 0;

        return $mav->render([
            'credits'   => $credits,                                // El crédito disponible
            'smses'     => Sms::orderBy('created_at', 'desc')       // Los mensajes
                           ->paginate(config('backend.list.pagination')),
            // Cantidad de sms reales enviados (1 sms = 160 caracteres)
            'smsPieces'     => Sms::all()->sum('pieces'),
        ]);
    }
}
