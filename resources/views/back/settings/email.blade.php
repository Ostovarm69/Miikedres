@extends('back.layouts.master')

@section('content')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb no-border">
                                    <li class="breadcrumb-item">مدیریت
                                    </li>
                                    <li class="breadcrumb-item">تنظیمات
                                    </li>
                                    <li class="breadcrumb-item active">ایمیل ها
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- users edit start -->
                <section class="users-edit">
                    <div class="card">
                        <div id="main-card" class="card-content">
                            <div class="card-body">
                                <div class="tab-content">
                                    <form id="socials-form" action="{{ route('admin.settings.email.save') }}" method="POST">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <label for="email_order_created">ایمیل ثبت سفارش</label>
                                                <p style="color: gray;">
                                                    پازامترهای مجاز:
                                                    <br>
                                                    {$first_name}, {last_name}, {order_number}, {order_delivery_status}, {order_payment_status}, {order_amount}
                                                </p>
                                                <div class="input-group mb-75">
                                                    <textarea rows="5" id="email_order_created" name="email_order_created" class="form-control rtl">{{option('email_order_created')}}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email_order_updated">ایمیل تغییر وضعیت سفارش</label>
                                                <p style="color: gray;">
                                                    پازامترهای مجاز:
                                                    <br>
                                                    {$first_name}, {last_name}, {order_number}, {order_delivery_status}, {order_payment_status}, {order_amount}
                                                </p>
                                                <div class="input-group mb-75">
                                                    <textarea rows="5" id="email_order_updated" name="email_order_updated" class="form-control rtl">{{option('email_order_updated')}}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email_product_in_stock">ایمیل موجود شدن محصول</label>
                                                <p style="color: gray;">
                                                    پازامترهای مجاز:
                                                    <br>
                                                    {$first_name}, {last_name}, {product_name}
                                                </p>
                                                <div class="input-group mb-75">
                                                    <textarea rows="5" id="email_product_in_stock" name="email_product_in_stock" class="form-control rtl">{{option('email_product_in_stock')}}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email_comment_approvals">ایمیل ثبت نظر</label>
                                                <p style="color: gray;">
                                                    پازامترهای مجاز:
                                                    <br>
                                                    {$first_name}, {last_name}, {product_name}
                                                </p>
                                                <div class="input-group mb-75">
                                                    <textarea rows="5" id="email_comment_approvals" name="email_comment_approvals" class="form-control rtl">{{option('email_comment_approvals')}}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                                <button type="submit" class="btn btn-primary glow">ذخیره تغییرات</button>

                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- users edit ends -->

            </div>
        </div>
    </div>

@endsection

@include('back.partials.plugins', ['plugins' => ['jquery.validate']])

@push('scripts')
    <script src="{{ asset('back/assets/js/pages/settings/socials.js') }}"></script>
@endpush
