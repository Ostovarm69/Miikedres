$('#print-order').click(function () {
    window.print();
});

$('#shipping-status').change(function () {
    var select = this;

    console.log($(select).data('action'))
    $.ajax({
        url: $(select).data('action'),
        type: 'POST',
        data: {
            status: $(select).val()
        },
        success: function (data) {
            Swal.fire({
                type: 'success',
                title: 'تغییرات با موفقیت ذخیره شد',
                confirmButtonClass: 'btn btn-primary',
                confirmButtonText: 'باشه',
                buttonsStyling: false,
            })
        },
        beforeSend: function (xhr) {
            block('#main-card');
            xhr.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
        },
        complete: function () {
            unblock('#main-card');
        },

    });
});