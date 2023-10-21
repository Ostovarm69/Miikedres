var alertBox =  $(document).find('.popover-notification-list');

$.ajax(
    {
        url: $('.notification-show').data('action'),
        method: 'get',
        dataType: 'json',
        beforeSend: function (){

        },
        success: function (response){
            if (response.length > 0){
                $('#news').css('color', '#009688');
                $('.notification-badge').text(response.length).fadeIn();
                for (let i = 0; i < response.length; i++){
                        $(alertBox).append('<li data-alert_id="1" class="alert_li">' + response[i].message + '</li>');
                }
            } else {
                $(alertBox).append('<li data-alert_id="1" class="alert_li no-notification-text">در حال حاضر پیامی برای نمایش وجود ندارد</li>');
            }

            $("#news").popover({
                'title' : 'اطلاعیه ها',
                'html' : true,
                'placement' : 'bottom',
                'content' : $(".alert_list").html()
            });

            $('.turn_off_alert').on('click', function(event){
                var alert = $(this).parent();
                var alert_id = alert.data("alert_id");
                alert.hide("fast");

            });
            $(alertBox).append('<li data-alert_id="1" class="alert_li">' + 'okkkkk' + '</li>')
        },
        error: function (response){

        }
    }
);
