@extends('front::layouts.master')

@push('styles')
    <link rel="stylesheet" href="{{ theme_asset('css/vendor/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset('css/vendor/nice-select.css') }}">
@endpush

@section('wrapper-classes', 'shopping-page')

@section('content')
    <!-- Start main-content -->
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">

            <form id="checkout-form" action="{{ route('front.orders.store') }}" class="setting_form" method="POST">
                @csrf
                <div class="row">

                        <div class="cart-page-content col-xl-9 col-lg-8 col-12 px-0">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            @if(!$discount_status['status'])
                                <div class="alert alert-danger" role="alert">
                                    <p>{{ trans('front::messages.cart.discount-code-is-invalid') }}</p>
                                    <span>{{ $discount_status['message'] }}</span>
                                </div>
                            @endif

                            <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 px-res-1">
                                <h2>{{ trans('front::messages.cart.order-delivery-address') }}</h2>
                            </div>
                            <section class="page-content dt-sl">
                                <div class="form-ui dt-sl pt-4 pb-4 checkout-div">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 mb-2">
                                            <div class="form-row-title">
                                                <h4>
                                                    {{ trans('front::messages.cart.fname-and-lname') }} <sup class="text-danger">*</sup>
                                                </h4>
                                            </div>
                                            <div class="form-row form-group">
                                                <input class="input-ui pr-2 text-right"
                                                        type="text"
                                                        name="name" value="{{ old('name') ?: auth()->user()->fullname }}"
                                                        placeholder="{{ trans('front::messages.cart.enter-your-name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12 mb-2">
                                            <div class="form-row-title">
                                                <h4>
                                                   {{ trans('front::messages.cart.phone-number') }} <sup class="text-danger">*</sup>
                                                </h4>
                                            </div>
                                            <div class="form-row form-group">
                                                <input
                                                    class="input-ui pl-2 dir-ltr text-left"
                                                    type="text"
                                                    name="mobile"  value="{{ old('mobile') ?: auth()->user()->username }}"
                                                    placeholder="09xxxxxxxxx">
                                            </div>
                                        </div>

                                        @if ($cart->hasPhysicalProduct())

                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-row-title">
                                                    <h4>
                                                        {{ trans('front::messages.cart.state') }} <sup class="text-danger">*</sup>
                                                    </h4>
                                                </div>
                                                <div class="form-row form-group">
                                                    <div class="custom-select-ui">
                                                        <select class="right" name="province_id" id="province">
                                                            <option value="">{{ trans('front::messages.cart.select') }}</option>

                                                            @foreach ($provinces as $province)
                                                                <option value="{{ $province->id }}"
                                                                    @if(auth()->user()->address && auth()->user()->address->province->id == $province->id) selected @endif>
                                                                    {{ $province->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-row-title">
                                                    <h4>
                                                        {{ trans('front::messages.cart.city') }} <sup class="text-danger">*</sup>
                                                    </h4>
                                                </div>
                                                <div class="form-row form-group">
                                                    <div class="custom-select-ui ">
                                                        <select class="right" name="city_id" id="city" data-action="{{ route('front.checkout.prices') }}">
                                                            <option value="">{{ trans('front::messages.cart.select') }}</option>

                                                            @if(auth()->user()->address)

                                                                @foreach (auth()->user()->address->province->cities()->active()->orderBy('ordering')->get() as $city)
                                                                    <option value="{{ $city->id }}" @if($city->id == auth()->user()->address->city->id) selected @endif>{{ $city->name }}</option>
                                                                @endforeach

                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-row-title">
                                                    <h4>
                                                        {{ trans('front::messages.cart.postal-address') }}<sup class="text-danger">*</sup>
                                                    </h4>
                                                </div>
                                                <div class="form-row form-group">
                                                    <textarea
                                                        class="input-ui pr-2 text-right"
                                                        name="address"
                                                        placeholder="{{ trans('front::messages.cart.enter-recipient-address') }}">{{ user_address('address') }}</textarea>
                                                </div>
                                            </div>

                                        @endif
                                        <div class="col-md-6 mb-2">
                                            <div class="form-row-title">
                                                <h4>
                                                {{ trans('front::messages.cart.order-description') }}
                                                </h4>
                                            </div>
                                            <div class="form-row">
                                                <textarea
                                                    class="input-ui pr-2 text-right"
                                                    name="description">{{ old('description') }}</textarea>
                                            </div>
                                        </div>

                                        @if ($cart->hasPhysicalProduct())
                                            <div class="col-md-6 mb-2">
                                                <div class="form-row-title">
                                                    <h4>
                                                        {{ trans('front::messages.cart.postal-code') }}<sup class="text-danger">*</sup>
                                                    </h4>
                                                </div>
                                                <div class="form-row form-group">
                                                    <input
                                                        class="input-ui pl-2 dir-ltr text-left placeholder-right"
                                                        type="text" pattern="\d*"
                                                        name="postal_code" value="{{ user_address('postal_code') }}"
                                                        placeholder="{{ trans('front::messages.cart.code-dashes') }}">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-12 mb-2">
                                            <div class="checkout-invoice">
                                                <div class="checkout-invoice-headline">
                                                    <div class="custom-control custom-checkbox pr-0 form-group">
                                                        <input id="agreement" name="agreement" type="checkbox" class="custom-control-input" required>
                                                        <label for="agreement" class="custom-control-label">{{ trans('front::messages.cart.site-rules') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($cart->hasPhysicalProduct())
                                    <div class="section-title no-reletive text-sm-title title-wide no-after-title-wide mb-0 px-res-1">
                                        <h2 class="mt-2">{{ trans('front::messages.cart.choose-how-to-send') }}</h2>
                                    </div>

                                    @include('front::partials.carriers-container', ['cart' => $cart])
                                @endif

                                <section class="page-content dt-sl pt-2">
                                    <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 px-res-1">
                                        <h2> {{ trans('front::messages.cart.choose-payment-method') }}</h2>
                                    </div>

                                    <div class="dt-sn pt-3 pb-3 mb-4">
                                        <div class="checkout-pack">
                                            <div class="row">
                                                <div class="checkout-time-table checkout-time-table-time">

                                                    @if ($wallet->balance)
                                                        <div class="col-12 wallet-select">
                                                            <div class="radio-box custom-control custom-radio pl-0 pr-3">
                                                                <input type="radio" class="custom-control-input" name="gateway" id="wallet" value="wallet">
                                                                <label for="wallet" class="custom-control-label">
                                                                    <i class="mdi mdi-credit-card-multiple-outline checkout-additional-options-checkbox-image"></i>
                                                                    <div class="content-box">
                                                                        <div class="checkout-time-table-title-bar checkout-time-table-title-bar-city">
                                                                            <span class="has-balance">{{ trans('front::messages.cart.pay-with-wallet') }}</span>
                                                                            <span class="increase-balance" style="display: none;">{{ trans('front::messages.cart.increase-and-pay-with-kyiv') }}</span>
                                                                        </div>
                                                                        <ul class="checkout-time-table-subtitle-bar">
                                                                            <li id="wallet-balance" data-value="{{ $wallet->balance }}">
                                                                                {{ trans('front::messages.cart.inventory') }}{{ trans('front::messages.currency.prefix') }}{{ number_format($wallet->balance) }}{{ trans('front::messages.currency.suffix') }}
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @foreach ($gateways as $gateway)

                                                        <div class="col-12">
                                                            <div class="radio-box custom-control custom-radio pl-0 pr-3">
                                                                <input type="radio" class="custom-control-input" name="gateway" id="{{ $gateway->key }}" value="{{ $gateway->key }}" {{ $loop->first ? 'checked' : '' }}>
                                                                <label for="{{ $gateway->key }}" class="custom-control-label">
                                                                    <i class="mdi mdi-credit-card-outline checkout-additional-options-checkbox-image"></i>
                                                                    <div class="content-box">
                                                                        <div class="checkout-time-table-title-bar checkout-time-table-title-bar-city">
                                                                            {{ trans('front::messages.cart.internet-payment') }} {{ $gateway->name }}
                                                                        </div>
                                                                        <ul class="checkout-time-table-subtitle-bar">
                                                                            <li>
                                                                            {{ trans('front::messages.cart.online-with-cards') }}
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>

                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </section>

                            </section>

                        </div>

                    @include('front::partials.checkout-sidebar')

                </div>
            </form>

            @if ($cart->discount)
                <div class="row mt-3">
                    <div class="col-md-4 col-12 px-0">
                        <div class="dt-sn pt-3 pb-3 px-res-1">
                            <div class="section-title text-sm-title title-wide no-after-title-wide mb-0">
                                <h2>{{ trans('front::messages.cart.registered-discount-code') }}</h2>
                            </div>
                            <div class="form-ui">
                                <form action="{{ route('front.discount.destroy') }}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <div class="row text-center">
                                        <div class="col-xl-6">
                                            <h3>{{ $cart->discount->code }}</strong>
                                        </div>
                                        <div class="col-xl-6 text-left">
                                            <button type="submit" class="btn btn-danger mt-res-1">{{ trans('front::messages.cart.remove-discount-code') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row mt-3">
                    <div class="col-sm-6 col-12 px-0">
                        <div class="dt-sn pt-3 pb-3 px-res-1">
                            <div class="section-title text-sm-title title-wide no-after-title-wide mb-0">
                                <h2>{{ trans('front::messages.cart.discount-code') }}</h2>
                            </div>
                            <div class="form-ui">
                                <form id="discount-create-form" action="{{ route('front.discount.store') }}">
                                    @csrf
                                    <div class="row text-center">
                                        <div class="col-xl-8 col-lg-12">
                                            <div class="form-row">
                                                <input type="text" name="code" class="input-ui pr-2" placeholder="{{ trans('front::messages.cart.enter-discount-code') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-12">
                                            <button type="submit" class="btn btn-primary mt-res-1">{{ trans('front::messages.cart.register-discount-code') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-5">
                <a href="{{ route('front.cart') }}" class="float-right border-bottom-dt"><i class="mdi mdi-chevron-double-right"></i>{{ trans('front::messages.cart.return-to-cart') }}</a>
            </div>
        </div>
    </main>
    <!-- End main-content -->
@endsection

@push('scripts')
    <script src="{{ theme_asset('js/vendor/wNumb.js') }}"></script>
    <script src="{{ theme_asset('js/vendor/ResizeSensor.min.js') }}"></script>
    <script src="{{ theme_asset('js/vendor/jquery.nice-select.min.js') }}"></script>
    <script src="{{ theme_asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ theme_asset('js/plugins/jquery-validation/localization/messages_fa.min.js') }}?v=2"></script>

    <script src="{{ theme_asset('js/pages/cart.js') }}?v=3"></script>
    <script src="{{ theme_asset('js/pages/checkout.js') }}?v=11"></script>
@endpush
