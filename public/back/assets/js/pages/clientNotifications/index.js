'use strict';
// Class definition

var datatable;

var client_notifications_datatable = (function () {
    // Private functions

    var options = {
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: $('#notifications_datatable').data('action'),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content'
                        )
                    },
                    map: function (raw) {
                        // sample data mapping
                        var dataSet = raw;
                        if (typeof raw.data !== 'undefined') {
                            dataSet = raw.data;
                        }
                        return dataSet;
                    },
                    params: {
                        query: $('#filter-client-notifications-form').serializeJSON()
                    }
                }
            },
            pageSize: 10,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        },

        layout: {
            scroll: true
        },

        rows: {
            autoHide: false
        },

        // columns definition
        columns: [
            {
                field: 'id',
                title: '#',
                sortable: false,
                width: 20,
                selector: {
                    class: ''
                },
                textAlign: 'center'
            },
            {
                field: 'user_id',
                title: 'شماره مشتری',
                sortable: false,
                template: function (row) {
                    if (row.user_id == 0){
                        return 'همه مشتریان';
                    }

                    return row.user_fullname + '(' + row.user_id + ')';
                }
            },
            {
                field: 'type',
                title: 'نوع اطلاعیه',
                width: 80,
                template: function (row) {
                    return row.type;
                }
            },
            {
                field: 'message',
                title: 'متن اطلاعیه',
                width: 200,
                template: function (row) {
                    return row.message;
                }
            },
            {
                field: 'created_at_jalali',
                sortable: 'desc',
                title: 'تاریخ ایجاد',
                template: function (row) {
                    return '<span class="ltr">' + row.created_at_jalali + '</span>';
                }
            },
            // {
            //     field: 'actions',
            //     title: 'عملیات',
            //     textAlign: 'center',
            //     sortable: false,
            //     width: 200,
            //     overflow: 'visible',
            //     autoHide: false,
            //     template: function (row) {
            //         return (
            //             '<a href ="' +
            //             row.links.edit +
            //             '"class="btn btn-warning waves-effect waves-light">ویرایش</a>\
            //         <a href="' +
            //             row.links.show +
            //             '" class="btn btn-info waves-effect waves-light">مشاهده</a>'
            //         );
            //     }
            // }
        ]
    };

    var initDatatable = function () {
        // enable extension
        options.extensions = {
            // boolean or object (extension options)
            checkbox: true
        };

        datatable = $('#notifications_datatable').KTDatatable(options);

        $('#filter-client-notifications-form .datatable-filter').on('change', function () {
            formDataToUrl('filter-client-notifications-form');
            datatable.setDataSourceQuery(
                $('#filter-client-notifications-form').serializeJSON()
            );
            datatable.reload();
        });

        datatable.on('datatable-on-click-checkbox', function (e) {
            var ids = datatable.checkbox().getSelectedId();
            var count = ids.length;

            $('#datatable-selected-rows').html(count);

            if (count > 0) {
                $('.datatable-actions').collapse('show');
            } else {
                $('.datatable-actions').collapse('hide');
            }
        });

        datatable.on('datatable-on-reloaded', function (e) {
            $('.datatable-actions').collapse('hide');
        });
    };

    return {
        // public functions
        init: function () {
            initDatatable();
        }
    };
})();

jQuery(document).ready(function () {
    client_notifications_datatable.init();
});

$(document).on('click', '.btn-delete', function () {
    $('#client-notifications-multiple-delete-form').attr('action', $(this).data('action'));
});

$('#client-notifications-multiple-delete-form').on('submit', function (e) {
    e.preventDefault();

    $('#multiple-delete-modal').modal('hide');

    var formData = new FormData(this);
    var ids = datatable.checkbox().getSelectedId();

    ids.forEach(function (id) {
        formData.append('ids[]', id);
    });

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        success: function (data) {
            toastr.success('اطلاعیه های انتخاب شده با موفقیت حذف شدند.');
            datatable.reload();
        },
        beforeSend: function (xhr) {
            block('#main-card');
            xhr.setRequestHeader(
                'X-CSRF-TOKEN',
                $('meta[name="csrf-token"]').attr('content')
            );
        },
        complete: function () {
            unblock('#main-card');
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

$('#users-export-form').on('submit', function (e) {
    e.preventDefault();

    let formData = datatable.getDataSourceParam();
    let queryString = $.param(formData);

    let formData2 = new FormData(this);
    let queryString2 = new URLSearchParams(formData2).toString();

    let url = `${$(this).attr('action')}?${queryString}&${queryString2}`;

    window.open(url);
});
