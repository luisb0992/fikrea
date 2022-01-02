<section class="ftco-section bg-light">
    <h2 class="d-none">prices</h2>
    <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <span class="subheading">@lang('Plan de Precios')</span>
                <h2 class="mb-4">@lang('Nuestros precios son inmejorables')</h2>
            </div>
        </div>
        <div class="row d-flex">

            @foreach ($plans as $plan)
                <div class="col-lg-4 col-md-6 ftco-animate">
                    <div class="block-7">
                        <div class="text-center">

                            @switch ($plan->id)
                                @case (\App\Models\Plan::TRIAL)
                                    <h2><i class="fas fa-user-alt text-info fa-4x"></i></h2>
                                @break

                                @case (\App\Models\Plan::PREMIUM)
                                    <h2><i class="fas fa-user-tie text-info fa-4x"></i></h2>
                                @break

                                @case (\App\Models\Plan::ENTERPRISE)
                                    <h2><i class="fas fa-handshake text-info fa-4x"></i></h2>
                                @break
                            @endswitch

                            <h2 class="heading">{{ $plan->name }}</h2>

                            @switch ($plan->id)
                                @case (\App\Models\Plan::TRIAL)
                                <span class="excerpt d-block">@lang('100 % Gratis')</span>
                                <span class="excerpt d-block">@lang('por un periodo de :trial días', ['trial' =>
                                    $plan->trial_period])</span>
                                @break

                                @case (\App\Models\Plan::PREMIUM)
                                <span class="excerpt d-block">@lang('La mejor solución para Pymes')</span>
                                <span class="excerpt d-block">@lang('Autónomos, Startups')</span>
                                @break

                                @case (\App\Models\Plan::ENTERPRISE)
                                <span class="excerpt d-block">@lang('La mejor solución definitiva')</span>
                                <span class="excerpt d-block">@lang('para tu empresa')</span>
                                @break
                            @endswitch

                            <span class="price">
                                <sup>EUR</sup>
                                <span class="text-info h1">{{ $plan->monthly_price }}</span>
                            </span>

                            @if ($plan->monthly_price > 0)
                                <span class="excerpt d-block">@lang('Pago Mensual')</span>
                            @else
                                <span class="excerpt d-block">
                                    @lang('Periodo de prueba de :trial dias', ['trial' => $plan->trial_period])
                                </span>

                                <span class="excerpt d-block pt-1">
                                    @lang('Durante ese periodo podrá disfrutar de todas las características de la
                                    aplicación, sin restricciones y sin compromiso de permanencia')
                                </span>
                            @endif

                            @if ($plan->yearly_price > 0)
                                <span class="price">
                                    <sup>@config('app.currency')</sup>
                                    <span class="text-info h1">{{ $plan->yearly_price }}</span>
                                </span>

                                {{--  importe de ahorro anual %  --}}
                                <span class="price">
                                    <span class="excerpt">@lang('Pago Anual')</span>
                                    <br>
                                    <span class="excerpt">
                                        @lang('Ahorra :discount :currency ', [
                                            'discount' => $plan->annualPaymentDiscount,
                                            'currency' => config('app.currency'), 
                                        ])
                                    </span>
                                </span>
                                <br>
                            @endif

                            <h3 class="heading-2 mb-3">@lang('Todas las características')</h3>

                            <ul class="pricing-text mb-4">
                                <li><strong>{{ $plan->disk_space }} MB</strong> @lang('Espacio de Usuario')</li>

                                @if ($plan->signers)
                                    <li>
                                        <strong>
                                            @if($plan->signers == 200)
                                                @lang('Ilimitado')
                                            @else
                                                {{ $plan->signers }}
                                            @endif
                                        </strong>
                                        @lang('Firmantes/Documento')
                                    </li>
                                @else
                                    <li><strong>&infin;</strong> @lang('Firmantes/Documento')</li>
                                @endif
                            </ul>
                            @if ($plan->monthly_price > 0)
                                <a href="{{ Auth::check() ? `@route('subscription.select')` : `@route('dashboard.register')` }}" class="btn btn-info d-block px-3 py-3 mb-4">
                                    @lang('Seleccionar Plan')
                                </a>
                            @else
                                <a href="@route('dashboard.home')" class="btn btn-info d-block px-3 py-3 mb-4 disabled">
                                    @lang('Ya lo puedes disfrutar')
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
