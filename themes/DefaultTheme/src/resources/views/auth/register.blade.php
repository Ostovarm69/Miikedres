@extends('front::auth.layouts.master', ['title' => trans('front::messages.auth.register-on-the-site') ])

@section('content')

    <!-- Start main-content -->
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">

            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7 col-12 mx-auto">
                    <div class="form-ui dt-sl dt-sn pt-4">
                        <div class="section-title title-wide mb-1 no-after-title-wide">
                            <h2 class="font-weight-bold">{{ trans('front::messages.auth.register-on-the-site') }}</h2>
                        </div>

                        <form id="register-form" action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="form-row-title">
                                <h3>{{ trans('front::messages.auth.name') }}</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="text" name="first_name" class="input-ui pr-2" placeholder="{{ trans('front::messages.auth.enter-your-name') }}">
                            </div>
                            <div class="form-row-title">
                                <h3>{{ trans('front::messages.auth.lname') }}</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="text" name="last_name" class="input-ui pr-2" placeholder="{{ trans('front::messages.auth.enter-your-fname') }}">
                            </div>
                            <div class="form-row-title">
                                <h3>ایمیل / اختیاری</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="text" name="email" class="input-ui pr-2" placeholder="ایمیل خود را وارد کنید">
                            </div>
                            <div class="form-row-title">
                                <h3>{{ trans('front::messages.auth.phone-number') }}</h3>
                            </div>
                            <div class="form-row with-icon form-group">
                                <input type="text" name="username" class="input-ui pr-2" placeholder="{{ trans('front::messages.auth.enter-mobile-number') }}">
                                <i class="mdi mdi-account-circle-outline"></i>
                            </div>

                            <div class="form-row-title">
                                <h3>{{ trans('front::messages.auth.password') }}</h3>
                            </div>
                            <div class="form-row with-icon form-group">
                                <input id="password" type="password" name="password" class="input-ui pr-2" placeholder="{{ trans('front::messages.auth.enter-your-password') }}">
                                <i class="mdi mdi-lock-open-variant-outline"></i>
                            </div>

                            <div class="form-row-title">
                                <h3>{{ trans('front::messages.auth.repeat-the-password') }}</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="password" name="password_confirmation" class="input-ui pr-2" placeholder="{{ trans('front::messages.auth.repeat-your-password') }}">
                            </div>

                            @if (option('user_refrral_enable', 0) == 1)
                                <div class="form-row-title">
                                    <h3>کد معرف</h3>
                                </div>
                                <div class="form-row form-group">
                                    <input type="text" name="referral_code" class="input-ui pr-2" placeholder="کد معرف (اختیاری)">
                                </div>
                            @endif

                            <div class="form-row mt-4">
                                <div class="col-md-8 col-6">
                                    <div class="form-group">
                                        <input type="text" class="input-ui pl-2 captcha" autocomplete="off" name="captcha" placeholder="{{ trans('front::messages.auth.security-code') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <img class="captcha w-100" src="{{ captcha_src('flat') }}" alt="captcha">
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <button class="btn-primary-cm btn-with-icon mx-auto w-100">
                                    <i class="mdi mdi-account-circle-outline"></i>
                                    {{ trans('front::messages.auth.register-on-the-site') }}
                                </button>
                            </div>
                            <div class="form-footer text-right mt-3">
                                <span class="d-block font-weight-bold">{{ trans('front::messages.auth.have-you-registered-before') }}</span>
                                <a href="{{ route('login') }}" class="d-inline-block mr-3 mt-2">{{ trans('front::messages.auth.enter') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <!-- End main-content -->

@endsection

@push('scripts')
    <script>
        var redirect_url = '{{ request("redirect") ?: Redirect::intended()->getTargetUrl() }}';
    </script>

    <script src="{{ theme_asset('js/pages/register.js') }}?v=2"></script>
@endpush
