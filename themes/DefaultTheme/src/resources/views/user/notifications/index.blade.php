@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12 card-content-dg">

        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                    <h2>لیست اطلاعیه ها</h2>
                </div>
            </div>
        </div>

        @if($notifications->count())

            <div class="row">
                <div class="col-12">
                    <div class="dt-sl">
                        <div class="table-responsive">
                            <table class="table table-order">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>متن اطلاعیه</th>
                                    <th>تاریخ اطلاعیه</th>
                                    <th>خواندن</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($notifications as $notification)
                                    <tr @if(!$notification->logs->where('user_id', auth()->user()->id)->first()) style="font-weight: bold; color: black" @else style="font-weight: lighter;" @endif>
                                        <td>{{ $loop->iteration}}</td>
                                        <td>{{ $notification->message }}</td>
                                        <td>{{ jdate($notification->created_at)->format('%d %B %Y') }}</td>
                                        <td class="details-link">
                                            <a class="read-item" href="#" data-id="{{ $notification->id }}">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="row">
                <div class="col-12">
                    <div class="page dt-sl dt-sn pt-3">
                        <p>{{ trans('front::messages.partials.there-is-nothing-to-show') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-3">
            {{ $notifications->links('front::components.paginate') }}
        </div>

    </div>
    <!-- End Content -->
@endsection

@push('scripts')
    <script>
        $('.read-item').click(function (){
            var thisItem = $(this);
            var id = $(thisItem).data('id');

            $.ajax(
                {
                    url: '{{ route('front.notifications.index-unread') }}' + '/' + id,
                    method: 'get',
                    dataType: 'json',
                    beforeSend: function (){

                    },
                    success: function (response){
                        $(thisItem).parents('tr').css({
                            'font-weight': 'lighter',
                            'color': '#212529',
                        });
                    },
                }
            );
        })
    </script>
@endpush
