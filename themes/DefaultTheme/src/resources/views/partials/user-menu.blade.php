<!--start .author-author__info-->
@if(auth()->check())
    <ul class="nav float-left">
        @include('front::partials.language')

        <li class="nav-item account dropdown">
            <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
                <span class="label-dropdown">{{ trans('front::messages.header.account') }}</span>
                <i class="mdi mdi-account-circle-outline"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-left">

                @if (auth()->user()->level == 'admin' || auth()->user()->level == 'creator')
                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                        {{ trans('front::messages.header.control-panel') }}
                    </a>
                @endif
                <a class="dropdown-item" href="{{ route('front.user.profile') }}">
                    <i class="mdi mdi-account-card-details-outline"></i>{{ trans('front::messages.header.profile') }}
                </a>
                <a class="dropdown-item" href="{{ route('front.orders.index') }}">
                    <i class="mdi mdi-account-edit-outline"></i>{{ trans('front::messages.header.my-orders') }}

                </a>
                <div class="dropdown-divider" role="presentation"></div>
                <a class="dropdown-item" href="{{ route('logout') }}">
                    <i class="mdi mdi-logout-variant"></i>{{ trans('front::messages.header.exit') }}
                </a>
            </div>
        </li>
    </ul>
    <a id="news" class="nav-link float-left ml-2 notification-show" data-action="{{ route('front.notifications.index-unread') }}" style="position:relative;" href="#">
        <i class="mdi mdi-bell-outline" style="font-size: 22px;"></i>
        <span class="badge badge-light notification-badge" style="position:absolute;right: -5px; bottom: 5px; border-radius: 20px;display: none"></span>
    </a>
    <div style="display:none" class="alert_list">
        <ul class="popover-notification-list">

        </ul>
        <div class="text-center">
            <a href="{{ route('front.notifications.index') }}">
                مشاهده همه
            </a>
        </div>
    </div>
    <style>
        .alert_list{font-size: 11px; color:grey}
        li.alert_li {
            font-size: 14px;
            color: #464646;
            padding: 5px;
            font-weight: bold;
            border-radius: 4px;
            border-bottom: thin solid #c0c0c0;
        }
        li.alert_li:hover{background-color:#eee}
        .turn_off_alert{float:right;margin-bottom :1px}
        a.alert_message{color : grey}
        a.alert_message:hover{color : grey}
        li.no-notification-text {
            font-size: 11px;
            font-weight: lighter;
        }
    </style>
@else
    <ul class="nav float-left">
        @include('front::partials.language')

        <li class="nav-item account dropdown">
            <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true"
               aria-expanded="false">
                <span class="label-dropdown"> {{ trans('front::messages.header.account') }}</span>
                <i class="mdi mdi-account-circle-outline"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-left">
                <a class="dropdown-item" href="{{ route('login') }}">
                    <i class="mdi mdi-account-card-details-outline"></i>
                    {{ trans('front::messages.header.sign-in-to-site') }}
                </a>
                <a class="dropdown-item" href="{{ route('register') }}">
                    <i class="mdi mdi-account-edit-outline"></i>
                    {{ trans('front::messages.header.register') }}
                </a>
            </div>
        </li>
    </ul>
@endif
<!--end /.author-author__info-->
