function reloadCaptcha() {
    $.ajax({
        url: BASE_URL + '/get-new-captcha',
        type: 'GET',
        data: {},
        success: function (data) {
            $('img.captcha').attr('src', data.captcha);
        }
    });
}

function block(el) {
    var block_ele = $(el);

    // Block Element
    block_ele.block({
        message: '<div class="mdi mdi-refresh icon-spin text-primary" style="font-size: 12px !important;"></div>',
        overlayCSS: {
            backgroundColor: '#fff',
            cursor: 'wait'
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: 'none'
        }
    });
}

function unblock(el) {
    $(el).unblock();

    setTimeout(function () {
        $('[data-toggle="popover"]').popover();
    }, 200);
}

$.ajaxSetup({
    error: function (data) {
        reloadCaptcha();

        if (data.status == 403) {
            toastr.error('اجازه ی دسترسی ندارید', 'خطا', {
                positionClass: 'toast-bottom-left',
                containerId: 'toast-bottom-left'
            });
            return;
        } else if (data.status == 429) {
            toastr.error(
                'تعداد درخواست ها بیش از حد مجاز است لطفا پس از دقایقی مجدد تلاش کنید',
                'خطا',
                {
                    positionClass: 'toast-bottom-left',
                    containerId: 'toast-bottom-left'
                }
            );
            return;
        } else if (data.status == 401) {
            toastr.error('لطفا وارد حساب کاربری خود شوید', {
                positionClass: 'toast-bottom-left',
                containerId: 'toast-bottom-left'
            });
            return;
        } else if (data.status == 500) {
            toastr.error('خطایی در سرور رخ داده است', 'خطا', {
                positionClass: 'toast-bottom-left',
                containerId: 'toast-bottom-left'
            });
            return;
        } else if (!data.responseJSON.errors) {
            toastr.error('خطایی رخ داده است', 'خطا', {
                positionClass: 'toast-bottom-left',
                containerId: 'toast-bottom-left'
            });
            return;
        }

        for (var key in data.responseJSON.errors) {
            // skip loop if the property is from prototype
            if (!data.responseJSON.errors.hasOwnProperty(key)) continue;

            var obj = data.responseJSON.errors[key];
            for (var prop in obj) {
                // skip loop if the property is from prototype
                if (!obj.hasOwnProperty(prop)) continue;

                toastr.error(obj[prop], 'خطا', {
                    positionClass: 'toast-bottom-left',
                    containerId: 'toast-bottom-left'
                });
            }
        }
    }
});

$(document).on('click', '#checkout-link', function () {
    $('#checkout-form').trigger('submit');
});

$('[data-toggle="popover"]').popover();

$('#province').change(function () {
    $('#city').empty();
    $('#city').append('<option value="">انتخاب کنید</option>');
    $('#city').trigger('change');
    $('.custom-select-ui select').niceSelect('update');

    if (!$(this).val()) {
        return;
    }

    var id = $(this).find(':selected').val();

    $.ajax({
        type: 'get',
        url: '/province/get-cities',
        data: {id: id},
        success: function (data) {
            $(data).each(function () {
                $('#city').append(
                    '<option value="' +
                        $(this)[0].id +
                        '">' +
                        $(this)[0].name +
                        '</option>'
                );
            });

            $('.custom-select-ui select').niceSelect('update');
        },
        beforeSend: function () {
            //
        }
    });
});

// **************  search
$('header.main-header .search-area form.search input').keyup(
    delay(function (e) {
        var q = $(this).val();
        q = $.trim(q);

        if (!q) {
            $(
                'header.main-header .search-area form.search .search-result'
            ).removeClass('open');
            $(
                'header.main-header .search-area form.search .close-search-result'
            ).removeClass('show');
            return;
        }

        $.ajax({
            url: '/search',
            type: 'POST',
            data: {
                q: q
            },
            success: function (data) {
                $(
                    'header.main-header .search-area form.search .search-result'
                ).removeClass('open');
                $(
                    'header.main-header .search-area form.search .search-result ul'
                ).empty();
                $(
                    'header.main-header .search-area form.search .close-search-result'
                ).removeClass('show');

                if (data.length) {
                    $(data).each(function (index, el) {
                        $(
                            'header.main-header .search-area form.search .search-result ul'
                        ).append(
                            `<li class="d-flex p-2 px-3">
                                <div>
                                    <img src="${el.image}" alt="${el.title}">
                                </div>
                                <div class="search-result-text">
                                    <div class="search-result-body">
                                        <a class="mr-3" href="${el.link}">${el.title}</a>
                                        <span class="text-muted mr-3">${el.category}</span>
                                    </div>
                                </div>
                                <div class="box-search-price">
                                    <span class="ml-3">${el.price}</span>
                                    <a href="${el.link}" class="d-flex align-items-center">
                                        <i class="mdi mdi-eye text-center search-icon "></i>
                                    </a>
                                </div>
                            </li>`
                        );
                    });
                    // Otherwise show it
                    $(
                        'header.main-header .search-area form.search .search-result'
                    ).addClass('open');
                    $(
                        'header.main-header .search-area form.search .close-search-result'
                    ).addClass('show');
                }
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader(
                    'X-CSRF-TOKEN',
                    $('meta[name="csrf-token"]').attr('content')
                );
            },
            error: function () {
                //
            }
        });
    }, 300)
);

$('header.main-header .search-area form.search .close-search-result').on(
    'click',
    function () {
        $(this).removeClass('show');
        $(
            'header.main-header .search-area form.search .search-result'
        ).removeClass('open');
    }
);

function delay(callback, ms) {
    var timer = 0;
    return function () {
        var context = this,
            args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

$('img.captcha').on('click', reloadCaptcha);

if (typeof $.lazyLoadXT != 'undefined') {
    $.lazyLoadXT.onload.addClass = 'animated fadeIn lazyLoadXT-completed';
    $.lazyLoadXT.selector = 'img[data-src]:not(.lazyLoadXT-completed)';

    setInterval(() => {
        $(window).lazyLoadXT();
    }, 1500);

    $(document).on('lazyerror', function (e, el) {
        $(el).attr('data-src', '');
    });
}

function inputFilter(e) {
    var key = e.keyCode || e.which;

    if (
        (!e.shiftKey && !e.altKey && !e.ctrlKey && key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105) ||
        key == 8 ||
        key == 9 ||
        key == 13 ||
        key == 37 ||
        key == 39
    ) {
    } else {
        return false;
    }

    if ($(e.target).val().length > 0) {
        $(e.target).val('');
    }
}

jQuery.fn.activationCodeInput = function (options) {
    var defaults = {
        number: 4,
        length: 1
    };
    var settings = $.extend({}, defaults, options);

    return this.each(function () {
        var self = $(this);
        var activationCode = $('<div />').addClass('activation-code');
        var placeHolder = self.attr('placeholder');
        activationCode.append($('<span />').text(placeHolder));
        self.replaceWith(activationCode);
        activationCode.append(self);

        var activationCodeInputs = $('<div />').addClass(
            'activation-code-inputs'
        );

        for (var i = 1; i <= settings.number; i++) {
            activationCodeInputs.append(
                $('<input />').attr({
                    maxLength: settings.length,
                    onkeydown: 'return inputFilter(event)',
                    oncopy: 'return false',
                    onpaste: 'return false',
                    oncut: 'return false',
                    ondrag: 'return false',
                    ondrop: 'return false',
                    type: 'number'
                })
            );
        }

        activationCode.prepend(activationCodeInputs);

        activationCode.on('click touchstart', function (event) {
            if (!activationCode.hasClass('active')) {
                activationCode.addClass('active');
                setTimeout(function () {
                    activationCode
                        .find('.activation-code-inputs input:first-child')
                        .focus();
                }, 200);
            }
        });

        activationCode
            .find('.activation-code-inputs')
            .on('keyup input', 'input', function (event) {
                if (
                    $(this).val().toString().length == settings.length ||
                    event.keyCode == 39
                ) {
                    $(this).next().focus();
                    if ($(this).val().toString().length) {
                        $(this).css('border-color', '#46b2f0');
                    }
                }
                if (event.keyCode == 8 || event.keyCode == 37) {
                    $(this).prev().focus();
                    if (!$(this).val().toString().length) {
                        $(this).css('border-color', '#ccc');
                    }
                }
                var value = '';
                activationCode
                    .find('.activation-code-inputs input')
                    .each(function () {
                        value += $(this).val().toString();
                    });
                self.attr({
                    value: value
                });
            });

        $(document).on('click touchstart', function (e) {
            if (
                !$(e.target).parent().is(activationCode) &&
                !$(e.target).is(activationCode) &&
                !$(e.target).parent().parent().is(activationCode)
            ) {
                var hide = true;

                activationCode
                    .find('.activation-code-inputs input')
                    .each(function () {
                        if ($(this).val().toString().length) {
                            hide = false;
                        }
                    });
                if (hide) {
                    activationCode.removeClass('active');
                } else {
                    activationCode.addClass('active');
                }
            }
        });
    });
};

function number_format(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

String.prototype.toEnglishDigit = function () {
    var find = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    var replace = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    var replaceString = this;
    var regex;
    for (var i = 0; i < find.length; i++) {
        regex = new RegExp(find[i], 'g');
        replaceString = replaceString.replace(regex, replace[i]);
    }
    return replaceString;
};

(function ($) {
    $.fn.customPersianDate = function () {
        $(this).each(function (i, el) {
            var pickerId = 'customPersianDate' + Date.now();
            var input = this;
            $(input).after(
                '<input type="hidden" name="' +
                    $(input).attr('name') +
                    '" id="' +
                    pickerId +
                    '">'
            );
            $(input).removeAttr('name');
            $(input).attr('autocomplete', 'off');

            $(input).on('keydown', function (e) {
                e.preventDefault();
                $(input).val('');
                $('#' + pickerId).val('');
                $(input).trigger('change');
            });

            var publishDatePicker = $(input).pDatepicker({
                toolbox: {
                    calendarSwitch: {
                        enabled: false
                    }
                },
                format: 'YYYY-MM-DD',
                initialValue: false,
                altField: '#' + pickerId,
                altFormat: 'YYYY-MM-DD',
                altFieldFormatter: function (unixDate) {
                    var self = this;
                    var thisAltFormat = self.altFormat.toLowerCase();
                    if (
                        thisAltFormat === 'gregorian' ||
                        thisAltFormat === 'g'
                    ) {
                        return new Date(unixDate);
                    }
                    if (thisAltFormat === 'unix' || thisAltFormat === 'u') {
                        return unixDate;
                    } else {
                        var pd = new persianDate(unixDate);
                        pd.formatPersian = this.persianDigit;

                        setTimeout(function () {
                            $(input).trigger('change');
                        }, 100);

                        return pd.format(self.altFormat).toEnglishDigit();
                    }
                },
                onSelect: function (unixDate) {
                    // $(input).trigger('change');
                },
                onSet: function (unixDate) {
                    // $(input).trigger('change');
                }
            });

            var date = $(input).val();

            if (date) {
                publishDatePicker.setDate(parseInt(date + '000'));
            }
        });

        return this;
    };
})(jQuery);

setTimeout (() => {
    if (typeof persianDate != 'undefined') {
        console.log('asdasd')
        $('.persian-date-picker').customPersianDate();
    }
}, 500);
