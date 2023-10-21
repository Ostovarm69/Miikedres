<div class="sms-panel-fields" id="melipayamak-sms-fields" style="{!! option('sms_panel_provider', 'melipayamak') != 'melipayamak' ? 'display: none;' : '' !!}">
    <h5 class="my-2">اطلاعات پنل ملی پیامک</h5>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <label>نام کاربری</label>
            <div class="input-group mb-75">
                <input type="text" name="MELIPAYAMAK_PANEL_USERNAME" class="form-control ltr" value="{{ option('MELIPAYAMAK_PANEL_USERNAME') }}">
            </div>
        </div>
        <div class="col-md-4">
            <label>رمز عبور</label>
            <div class="input-group mb-75">
                <input type="text" name="MELIPAYAMAK_PANEL_PASSWORD" class="form-control ltr" value="{{ option('MELIPAYAMAK_PANEL_PASSWORD') }}">
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد متن خوش آمدگویی</label>
                    <div class="input-group mb-75">
                        <input type="text" name="user_register_pattern_code_melipayamak" class="form-control ltr sms_on_user_register" value="{{ option('user_register_pattern_code_melipayamak') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control sms_on_user_register" rows="4">{0} عزیز خوش آمدید. &#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد متن ارسال کد تایید</label>
                    <div class="input-group mb-75">
                        <input type="text" name="user_verify_pattern_code_melipayamak" class="form-control ltr" value="{{ option('user_verify_pattern_code_melipayamak') }}" >
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control" rows="4">کد تایید: {0} &#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد متن پرداخت سفارش</label>
                    <div class="input-group mb-75">
                        <input type="text" name="order_paid_pattern_code_melipayamak" class="form-control ltr sms_on_order_paid" value="{{ option('order_paid_pattern_code_melipayamak') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control sms_on_order_paid" rows="4">سفارش جدید با شماره سفارش {0} ثبت و پرداخت شد.&#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد متن پرداخت سفارش برای کاربر</label>
                    <div class="input-group mb-75">
                        <input type="text" name="user_order_paid_pattern_code_melipayamak" class="form-control ltr user_sms_on_order_paid" value="{{ option('user_order_paid_pattern_code_melipayamak') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control user_sms_on_order_paid" rows="4">سفارش شما با شماره سفارش {0} با موفقیت ثبت شد.&#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد متن افزایش موجودی کیف پول</label>
                    <div class="input-group mb-75">
                        <input type="text" name="wallet_increase_pattern_code_melipayamak" class="form-control ltr wallet_increase_sms" value="{{ option('wallet_increase_pattern_code_melipayamak') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control wallet_increase_sms" rows="4">مبلغ {0} تومان به اعتبار کیف پول شما اضافه شد.&#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد متن کاهش موجودی کیف پول</label>
                    <div class="input-group mb-75">
                        <input type="text" name="wallet_decrease_pattern_code_melipayamak" class="form-control ltr wallet_decrease_sms" value="{{ option('wallet_decrease_pattern_code_melipayamak') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control wallet_decrease_sms" rows="4">مبلغ {0} تومان از اعتبار کیف پول شما کسر شد.&#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد پترن تبریک تولد</label>
                    <div class="input-group mb-75">
                        <input type="text" name="birth_day_pattern_code_melipayamak" class="form-control ltr birth_day_sms" value="{{ option('birth_day_pattern_code_melipayamak') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control birth_day_sms" rows="4">{0} عزیز کدتخفیف {1} درصدی {2} به مناسبت روز تولد شما ایجاد شد.&#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد پترن برای ثبت نظر سفارشات</label>
                    <div class="input-group mb-75">
                        <input type="text" name="comment_pattern_ippanel" class="form-control ltr comment_sms" value="{{ option('comment_pattern_ippanel') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control comment_sms" rows="4">{0} عزیز سفارش  {1} بدستتان رسیده است . لطفا از لینک زیر وارد شده و نظر خودرا وارد کنید {2}&#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 form-group mb-0">
                    <label>کد پترن برای ایجاد اطلاعیه</label>
                    <div class="input-group mb-75">
                        <input type="text" name="notification_pattern_melipayamak" class="form-control ltr notification_sms" value="{{ option('notification_pattern_melipayamak') }}" required>
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label>متن نمونه ایجاد پترن</label>
                    <textarea readonly class="form-control notification_sms" rows="4">{0} عزیز شما یک اطلاعیه جدید در سایت دارید.&#13;&#10 {{ option('info_site_title') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
