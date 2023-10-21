@extends('front::layouts.master')
<link rel="stylesheet" href="{{ theme_asset('css/dg.css') }}">
@section('content')

    <!-- Start main-content -->
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">
            <div class="row">

                <!-- Start Sidebar -->
                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12 sticky-sidebar">
                    <div class="profile-sidebar dt-sl">
                        <div class="dt-sl dt-sn border-none-dg mb-none-dg pb-none-dg">
                            <div class="profile-sidebar-header dt-sl">
                                <div class="profile-avatar float-right">
                                    <img data-src="{{ theme_asset('img/theme/fd4840b2.svg') }}" alt="">
                                </div>
                                <div class="profile-header-content mr-3 mt-2 float-right">
                                    <span class="d-block profile-username">{{ $user->fullname }}</span>
                                    <span class="d-block profile-phone">{{ $user->username }}</span>
                                </div>
                                <div class="profile-edit">
                                    <a href="{{ route('front.user.profile.edit') }}">
                                        <i class="mdi mdi-pencil-minus-outline"></i>
                                    </a>
                                </div>
                                <div title="{{ trans('front::messages.currency.prefix') . convert_number($user->getWallet()->balance()) .  trans('front::messages.currency.suffix')  }}" class="profile-point mt-3 dt-sl">
                                    <span class="timeline-icon-bar">
                                        <i class="dot-timeline"></i>
                                        <i class="dash-timeline"></i>
                                    </span>
                                    <span class="value-profile-point mr-10-dg">
                                        {{ trans('front::messages.profile.wallet-balance') }}
                                        <br>
                                        <a class="mr-10-dg" href="{{ route('front.wallet.create') }}">
                                            افزایش موجودی
                                            <i class="mdi mdi-arrow-left"></i>
                                        </a>
                                    </span>
                                    <div class="float-left label-profile-point"><strong class="">{{ trans('front::messages.currency.prefix') }}{{ number_format($user->getWallet()->balance()) }}</strong> {{ trans('front::messages.currency.suffix') }}</div>
                                    <div class="side-divider"></div>
                                    <span class="timeline-icon-bar">
                                        <i class="dot-timeline"></i>
                                        <i class="dash-timeline"></i>
                                    </span>
                                    <span class="value-profile-point mr-10-dg">
                                        مایک کلاب
                                        <br>
                                        <a class="mr-10-dg" href="{{ route('front.wallet.create') }}">
                                            مشاهده جوایز
                                            <i class="mdi mdi-arrow-left"></i>
                                        </a>
                                    </span>
                                    <div class="float-left label-profile-point">
                                        <strong class="">
                                            - امتیاز
                                        </strong>
                                    </div>
                                    <div class="side-divider"></div>
                                </div>
                            </div>
                        </div>

                        @if (option('user_refrral_enable', 0) == 1)
                            <div class="dt-sl dt-sn mb-3 py-4 px-3">
                                <div class="profile-sidebar-header dt-sl">
                                    <p>با دعوت از دوستان تان به {{ option('info_site_title') }} <b>{{ option('owner_refrral_amount', 0) }}</b> درصد کد تخفیف بگیرید.</p>
                                    <span>کد معرفی شما:</span><strong class="text-info"> {{ $user->referral_code }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="dt-sl dt-sn mb-3 pt-none-dg plt-none-dg">
                            <div class="profile-menu-section dt-sl">
                                <div class="profile-menu">
                                    <ul>
                                        <li>
                                            <a href="{{ route('front.user.profile') }}" class="{{ active_class('front.user.profile') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-account-outline"></i>
                                                 {{ trans('front::messages.profile.profile') }}
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('front.wallet.index') }}" class="{{ active_class('front.wallet.index') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-credit-card-outline"></i>
                                                {{ trans('front::messages.profile.wallet') }}
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('front.orders.index') }}" class="{{ active_class('front.orders.index') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-card-bulleted-settings-outline"></i>
                                                {{ trans('front::messages.profile.all-orders') }}
                                                <span class="menu-bottom-dg"></span>

                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{ route('front.user.comments') }}" class="{{ active_class('front.user.comments') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-glasses"></i>
                                                {{ trans('front::messages.profile.your-views') }}
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>

                                        @if (option('user_refrral_enable', 0) == 1)
                                            <li>
                                                <a href="{{ route('front.user.referrals.index') }}"
                                                    class="{{ active_class('front.user.referrals.index') }}">
                                                    <span class="menu-r-dg"></span>
                                                    <i class="mdi mdi-lan"></i>
                                                    کد های تخفیف معرفی
                                                    <span class="menu-bottom-dg"></span>
                                                </a>
                                            </li>
                                        @endif

                                        <li>
                                            <a href="{{ route('front.reviews.index') }}" class="{{ active_class('front.reviews.index') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-comment-outline"></i>
                                                {{ trans('front::messages.profile.reviews') }}
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('front.tickets.index') }}" class="{{ active_class('front.tickets.index') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-ticket-outline"></i>
                                                {{ trans('front::messages.profile.your-tickets') }}
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('front.notifications.index') }}" class="{{ active_class('front.notifications.index') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-bell-outline"></i>
                                                اطلاعیه ها
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('front.favorites.index') }}" class="{{ active_class('front.favorites.index') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-heart-outline"></i>
                                                {{ trans('front::messages.profile.wishlist') }}
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{ route('front.user.profile.edit') }}" class="{{ active_class('front.user.profile.edit') }}">
                                                <span class="menu-r-dg"></span>
                                                <i class="mdi mdi-account-edit-outline"></i>
                                                {{ trans('front::messages.profile.personal-information') }}
                                                <span class="menu-bottom-dg"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sidebar -->

                @yield('user-content')

            </div>

            @if($random_products->count())
                <section class="slider-section slider-section-dg dt-sl mt-5 mb-5">
                    <div class="row mb-3 card-content-dg">
                        <div class="col-12">
                            <div class="section-title text-sm-title title-wide no-after-title-wide">
                                <h2>{{ trans('front::messages.profile.recommended-products') }}</h2>
                            </div>
                        </div>

                        <!-- Start Product-Slider -->
                        <div class="col-12 px-res-0">
                            <div class="product-carousel carousel-md owl-carousel owl-theme">
                                @foreach ($random_products as $product)
                                    @include('front::partials.product-block')
                                @endforeach
                            </div>
                        </div>
                        <!-- End Product-Slider -->

                    </div>
                </section>
            @endif

        </div>
    </main>
    <!-- End main-content -->

@endsection
