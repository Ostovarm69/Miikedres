$(document).on('click', '.add-to-cart', function () {
    var btn = this;
    var groups = [];

    $('.product-info-block input:checked').each(function (index, el) {
        groups.push($(el).val());
    });

    $.ajax({
        type: 'POST',
        url: $(btn).data('action'),
        data: {
            quantity: $('#cart-quantity').val(),
            price_id: $(btn).data('price_id')
        },
        success: function (data) {
            if (data.status == 'success') {
                Swal.fire({
                    type: 'success',
                    title: 'با موفقیت اضافه شد',
                    text: 'محصول مورد نظر با موفقیت به سبد خرید شما اضافه شد برای رزرو محصول سفارش خود را نهایی کنید.',
                    confirmButtonText: 'باشه',
                    footer: '<h5><a href="/cart">مشاهده سبد خرید</a></h5>'
                });

                $('#cart-list-item').replaceWith(data.cart);
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'خطا',
                    text: data.message,
                    confirmButtonText: 'باشه',
                    footer: '<h5><a href="/cart">مشاهده سبد خرید</a></h5>'
                });
            }
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader(
                'X-CSRF-TOKEN',
                $('meta[name="csrf-token"]').attr('content')
            );
            block(btn);
        },
        complete: function () {
            unblock(btn);
        }
    });
});

$('#stock_notify_btn').click(function () {
    var btn = this;

    if ($(btn).data('user')) {
        sendStockNotify();
    } else {
        $('#modal-stock-notify').modal('show');
    }
});

function sendStockNotify() {
    var btn = $('#stock_notify_btn');

    if ($(btn).data('user')) {
        var data = {
            product_id: $(btn).data('product')
        };
    } else {
        var data = {
            product_id: $(btn).data('product'),
            name: $('#stock-name').val(),
            mobile: $('#stock-mobile').val()
        };
    }

    $.ajax({
        type: 'POST',
        url: BASE_URL + '/stock-notify',
        data: data,
        success: function (data) {
            toastr.success(
                'نام شما در لیست اطلاع از موجودی این محصول قرار گرفت.',
                '',
                {
                    positionClass: 'toast-bottom-left',
                    containerId: 'toast-bottom-left'
                }
            );
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader(
                'X-CSRF-TOKEN',
                $('meta[name="csrf-token"]').attr('content')
            );
            block(btn);
        },
        complete: function () {
            unblock(btn);
        }
    });
}

$('#sendStockNotifyBtn').click(sendStockNotify);

// product prices js codes

$(document).on('click', '.product-attribute', function () {
    var input = $(this).find('input');

    if (input.is(':checked')) {
        return;
    }

    setTimeout(() => {
        var product = input.data('product');
        var groups = [];

        $('.product-info-block input:checked').each(function (index, el) {
            groups.push($(el).val());
        });

        $.ajax({
            type: 'GET',
            url: BASE_URL + '/product/' + product + '/prices',
            data: {
                groups: groups
            },
            success: function (data) {
                setTimeout(() => {
                    $('.product-info-block').replaceWith(data);
                }, 200);

                setTimeout(() => {
                    writeColorsName();
                }, 250);
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader(
                    'X-CSRF-TOKEN',
                    $('meta[name="csrf-token"]').attr('content')
                );
                block('.product-info');
            },
            complete: function () {
                unblock('.product-info');
            }
        });
    }, 50);
});

function writeColorsName() {
    $('.ui-variant-shape[checked]').each(function (index, item) {
        $('#' + $(item).data('group-id')).text($(item).data('name'));
    });
}

writeColorsName();

//-------------------------- Add to favorites
$(document).on('click', '#add-to-favorites', function () {
    var btn = this;

    $.ajax({
        type: 'POST',
        url: $(btn).data('action'),
        data: {
            product_id: $(btn).data('product')
        },
        success: function (data) {
            toastr.success('با موفقیت انجام شد', '', {
                positionClass: 'toast-bottom-left',
                containerId: 'toast-bottom-left'
            });

            if (data.action == 'create') {
                $(btn).addClass('favorites');
                $(btn).parent().find('span').text('حذف از علاقمندی ها');
            } else {
                $(btn).removeClass('favorites');
                $(btn).parent().find('span').text('افزودن به علاقمندی ها');
            }
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader(
                'X-CSRF-TOKEN',
                $('meta[name="csrf-token"]').attr('content')
            );
            block('#add-to-favorites');
        },
        complete: function () {
            unblock('#add-to-favorites');
        }
    });
});

//-------------------------- tabs
$(document).ready(function () {
    $('.tabs-product-info .ah-tab-item:first').trigger('click');
});

$('#price-changes-modal').on('show.bs.modal', function (e) {
    if (!$(this).find('.chart-prices-label label.active').length) {
        setTimeout(() => {
            $(this).find('.chart-prices-label label').first().trigger('click');
        }, 100);
    }
});

$('.chart-prices-label label').on('click', function () {
    if ($(this).hasClass('active')) {
        return;
    }

    $('#selected-chart-price-title').text($(this).data('title'));

    $('.chart-prices-label label').removeClass('active');
    $(this).addClass('active');

    var action = $(this).data('action');

    $.ajax({
        url: action,
        type: 'GET',
        success: function (data) {
            data = data.data;

            var categories = [];
            var discountPrices = [];
            var realPrices = [];
            var discounts = [];

            for (const [key, value] of Object.entries(data)) {
                categories.push(value.date);
                discountPrices.push(value.discount_price);
                discounts.push(value.discount);

                if (
                    value.discount_price == value.price &&
                    (data[key - 1] == undefined ||
                        data[key - 1].discount_price == data[key - 1].price) &&
                    (data[parseInt(key) + 1] == undefined ||
                        data[parseInt(key) + 1].discount_price ==
                            data[parseInt(key) + 1].price)
                ) {
                    realPrices.push(null);
                } else {
                    realPrices.push(value.price);
                }
            }

            renderPriceChart(
                discountPrices.reverse(),
                realPrices.reverse(),
                discounts.reverse(),
                categories.reverse()
            );
        },

        beforeSend: function (xhr) {
            block('#price-changes-modal .modal-dialog');
        },
        complete: function () {
            unblock('#price-changes-modal .modal-dialog');
        },
        contentType: false,
        processData: false
    });
});

var chart;

//---------------------- modal
function renderPriceChart(discountPrices, realPrices, discounts, categories) {
    if (discountPrices.every((element) => element === null)) {
        $('#chart').hide();
        $('#empty-chart').show();
        return;
    }

    $('#chart').show();
    $('#empty-chart').hide();

    var options = {
        series: [
            {
                name: 'با تخفیف',
                data: discountPrices
            },
            {
                name: 'بدون تخفیف',
                data: realPrices
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            },
            fontFamily: 'iranyekan'
        },

        tooltip: {
            custom: function ({series, seriesIndex, dataPointIndex, w}) {
                if (!series[0][dataPointIndex]) {
                    return '';
                }

                if (discounts[dataPointIndex]) {
                    var discountTemplate = `<div><del>${number_format(
                        series[1][dataPointIndex]
                    )}</del> <span class="chart-tooltip-discount">${
                        discounts[dataPointIndex]
                    }%</span></div>`;
                } else {
                    var discountTemplate = ``;
                }

                return `<div class="chart-tooltip-container">
                    <div class="chart-tooltip-title ml-3">کمترین قیمت:</div>
                    <div class="chart-tooltip-prices">
                        ${discountTemplate}
                        <div class="mt-1"><strong>${number_format(
                            series[0][dataPointIndex]
                        )}</strong> <small> تومان </small></div>
                    </div>
                </div>`;
            }
        },
        stroke: {
            width: [5, 4],
            curve: 'straight',
            dashArray: [0, 5]
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                rotate: 0,
                rotateAlways: false,
                formatter: function (value, timestamp, opts) {
                    if (
                        categories[0] == value ||
                        categories[9] == value ||
                        categories[19] == value ||
                        categories[29] == value
                    ) {
                        return value;
                    }

                    return '';
                }
            },
            tooltip: {
                formatter: function (value, timestamp, opts) {
                    return categories[value - 1];
                }
            }
        },
        colors: ['#00bfd6', '#cdcdcd'],
        markers: {
            size: [4, 0]
        },
        yaxis: {
            labels: {
                formatter: (value) => {
                    if (value == null) {
                        return '';
                    }
                    return number_format(value);
                }
            }
        }
    };

    if (chart == undefined) {
        chart = new ApexCharts(document.querySelector('#chart'), options);
        chart.render();
    } else {
        chart.destroy();
        chart = new ApexCharts(document.querySelector('#chart'), options);
        chart.render();
    }
}

// product review js codes

$(document).ready(function () {
    var inputs = $('#advantage-input, #disadvantage-input');
    var inputChangeCallback = function () {
        var self = $(this);
        if (self.val().trim().length > 0) {
            self.siblings('.js-icon-form-add').show();
        } else {
            self.siblings('.js-icon-form-add').hide();
        }
    };

    inputs.each(function () {
        inputChangeCallback.bind(this)();
        $(this).on('change keyup', inputChangeCallback.bind(this));
    });

    $('#advantages')
        .delegate('.js-icon-form-add', 'click', function (e) {
            var parent = $('.js-advantages-list');
            if (parent.find('.js-advantage-item').length >= 5) {
                return;
            }

            var advantageInput = $('#advantage-input');

            if (advantageInput.val().trim().length > 0) {
                parent.append(
                    `<div class="ui-dynamic-label ui-dynamic-label--positive js-advantage-item">${advantageInput.val()}
                        <button type="button" class="ui-dynamic-label-remove js-icon-form-remove"></button>
                        <input type="hidden" name="review[advantages][]" value="${advantageInput.val()}">
                    </div>`
                );

                advantageInput.val('').change();
                advantageInput.focus();
            }
        })
        .delegate('.js-icon-form-remove', 'click', function (e) {
            $(this).parent('.js-advantage-item').remove();
        });

    $('#disadvantages')
        .delegate('.js-icon-form-add', 'click', function (e) {
            var parent = $('.js-disadvantages-list');
            if (parent.find('.js-disadvantage-item').length >= 5) {
                return;
            }

            var disadvantageInput = $('#disadvantage-input');

            if (disadvantageInput.val().trim().length > 0) {
                parent.append(
                    `<div class="ui-dynamic-label ui-dynamic-label--negative js-disadvantage-item">${disadvantageInput.val()}
                        <button type="button" class="ui-dynamic-label-remove js-icon-form-remove"></button>
                        <input type="hidden" name="review[disadvantages][]" value="${disadvantageInput.val()}">
                    </div>`
                );

                disadvantageInput.val('').change();
                disadvantageInput.focus();
            }
        })
        .delegate('.js-icon-form-remove', 'click', function (e) {
            $(this).parent('.js-disadvantage-item').remove();
        });

    $('#advantage-input').on('keydown', function (event) {
        if (event.which === 13) {
            $('#advantages .js-icon-form-add').trigger('click');
            event.preventDefault();
        }
    });
    $('#disadvantage-input').on('keydown', function (event) {
        if (event.which === 13) {
            $('#disadvantages .js-icon-form-add').trigger('click');
            event.preventDefault();
        }
    });

    $('.product-review-rate input').on('change', function () {
        $('#selected-rating-text').text($(this).data('title'));
    });

    $('#add-product-review-form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function (data) {
                Swal.fire({
                    text: 'نظر شما با موفقیت ثبت شد و پس از تایید مدیر نمایش داده خواهد شد.',
                    type: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'باشه'
                });

                $('#add-product-review-form').trigger('reset');
                $('.js-icon-form-remove').trigger('click');
                $('#add-product-review-modal').modal('hide');
            },

            beforeSend: function (xhr) {
                block('#add-product-review-form');
                xhr.setRequestHeader(
                    'X-CSRF-TOKEN',
                    $('meta[name="csrf-token"]').attr('content')
                );
            },
            complete: function () {
                unblock('#add-product-review-form');
            },

            cache: false,
            contentType: false,
            processData: false
        });
    });

    $('#add-product-review-modal').on('show.bs.modal', function () {
        $('.js-advantages-list').empty();
        $('.js-disadvantages-list').empty();
        $('#advantage-input').val('');
        $('#disadvantage-input').val('');

        $.ajax({
            url: $(this).data('action'),
            type: 'GET',
            success: function (data) {
                let review = data.review;

                if (review) {
                    $('#add-product-review-form')
                        .find('input[name="title"]')
                        .val(review.title);
                    $('#add-product-review-form')
                        .find(`input[name="rating"][value="${review.rating}"]`)
                        .prop('checked', true);
                    $('#add-product-review-form')
                        .find('textarea[name="body"]')
                        .val(review.body);
                    $('#add-product-review-form')
                        .find(
                            `input[name="suggest"][value="${review.suggest}"]`
                        )
                        .prop('checked', true);

                    review.points.forEach(function (item) {
                        if (item.type == 'positive') {
                            $('#advantage-input').val(item.text);
                            $('#advantages .js-icon-form-add').trigger('click');
                        } else {
                            $('#disadvantage-input').val(item.text);
                            $('#disadvantages .js-icon-form-add').trigger(
                                'click'
                            );
                        }
                    });
                }
            }
        });
    });

    $('.comments-likes button').on('click', function (e) {
        let btn = $(this);

        $.ajax({
            url: $(this).data('action'),
            type: 'POST',
            success: function (data) {
                btn.closest('.comments-likes')
                    .find('.likes-count')
                    .text(data.review.likes_count);

                btn.closest('.comments-likes')
                    .find('.dislikes-count')
                    .text(data.review.dislikes_count);
            },

            beforeSend: function (xhr) {
                block(btn);
                xhr.setRequestHeader(
                    'X-CSRF-TOKEN',
                    $('meta[name="csrf-token"]').attr('content')
                );
            },
            complete: function () {
                unblock(btn);
            }
        });
    });
});

$('.copy-text-btn').on('click', function () {
    var copyText = document.getElementById('shareLink');
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);

    $('.copy-text-btn')
        .tooltip('hide')
        .attr('data-original-title', 'کپی شد')
        .tooltip('show');

    setTimeout(function () {
        $('.copy-text-btn')
            .tooltip('hide')
            .attr('data-original-title', 'کپی لینک')
            .tooltip('show');
    }, 1000);
});

$('#popover').popover({trigger: 'hover'});
