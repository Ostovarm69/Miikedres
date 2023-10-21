// validate form with jquery validation plugin
jQuery('#ticket-create-form').validate({
    rules: {
        'message': {
            required: true,
        },
        'user_id': {
            required: false,
        },
        'type': {
            required: true,
        },

    },
});

$('#user_id').select2({
    rtl: true,
    width: '100%',
    placeholder: "انتخاب کنید",
});

$('#clientNotification-create-form').submit(function(e) {
    e.preventDefault();

    if ($(this).valid() && !$(this).data('disabled')) {

        var formData = new FormData(this);

        var form = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(data) {
                $('#clientNotification-create-form').data('disabled', true);
                window.location.href = form.data('redirect');
            },
            beforeSend: function(xhr) {
                block('#main-card');
                xhr.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            },
            complete: function() {
                unblock('#main-card');
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }

});
