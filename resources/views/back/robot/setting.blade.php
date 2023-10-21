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
                                    <li class="breadcrumb-item active">تنظیمات بروزرسانی</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-12 col-md-6 col-sm-12">
                        <div class="card">
                            <div id="main-card" class="card-content">
                                <div class="card-body">
                                    <div class="card m-0">
                                        <div class="card-header h4">
                                            خلاصه وضعیت بروزرسانی لیست محصولات
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    تعداد محصولات مانده تا اتمام بروزرسانی
                                                    <span class="badge badge-primary badge-pill">{{ $count }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    تخمین زمانی تا اتمام بروزرسانی
                                                    <span class="badge badge-primary badge-pill">حدودا {{ ceil($count / 40) }} دقیقه</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-sm-12">
                        <div class="card">
                            <div id="main-card" class="card-content">
                                <div class="card-body">
                                    <div class="card m-0">
                                        <div class="card-header h4">
                                            تنظیمات فاصله زمانی بروزرسانی
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                زمان بروزرسانی را بر حسب دقیقه فقط یک عدد وارد کنید.
                                                این عدد نمیتواند کوچک تر از ۵ دقیقه باشد.
                                            </p>
                                            <div class="input-group">
                                                <form action="{{ route('admin.robot.setting.update-period') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <div class="form-row align-items-center">
                                                        <div class="col-auto">
                                                            <label class="sr-only" for="inlineFormInput">فرکانس بروزرسانی</label>
                                                            <input value="{{ $periodOption }}" name="period" type="text" class="form-control mb-2" id="inlineFormInput" placeholder="فرکانس بروزرسانی">
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="submit" class="btn btn-primary mb-2">بروزرسانی</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                @if(session('periodUpdateMessage'))
                                                    <hr>
                                                    <div class="alert alert-success" role="alert">
                                                        {{ session('periodUpdateMessage') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 col-sm-12">
                        <div class="card">
                            <div id="main-card" class="card-content">
                                <div class="card-body">
                                    <div class="card m-0">
                                        <div class="card-header h4">
                                            بروزرسانی در لحظه
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                جهت بروزرسانی در لحظه روی دکمه زیر کلیک کنید.
                                                هر بار بروزرسانی ممکن است تا ۱۵ دقیقه بطول انجامد.
                                            </p>
                                            <a href="{{ route('admin.robot.setting.live-update') }}"
                                               class="btn btn-primary">بروزرسانی</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-sm-12">
                        <div class="card">
                            <div id="main-card" class="card-content">
                                <div class="card-body">
                                    <div class="card m-0">
                                        <div class="card-header h4">
                                            بروزرسانی یک محصول با کد
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                جهت بروزرسانی یک محصول، مد مورد نظر را در باکس زیر وارد کنید و روی دکمه بروزرسانی کلیک کنید.
                                            </p>
                                            <div class="input-group">
                                                <form action="{{ route('admin.robot.setting.update-single-product') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <div class="form-row align-items-center">
                                                        <div class="col-auto">
                                                            <label class="sr-only" for="inlineFormInput">کد محصول</label>
                                                            <input name="code" type="text" class="form-control mb-2" id="inlineFormInput" placeholder="کد محصول">
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="submit" class="btn btn-primary mb-2">بروزرسانی</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                @if(session('singleUpdateMessage'))
                                                    <div class="alert alert-success" role="alert">
                                                        {{ session('singleUpdateMessage') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-sm-12">
                        <div class="card">
                            <div id="main-card" class="card-content">
                                <div class="card-body">
                                    <div class="card m-0">
                                        <div class="card-header h4">
                                            بروزرسانی یک محصول با ID
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                جهت بروزرسانی یک محصول، ID مورد نظر را در باکس زیر وارد کنید و روی دکمه بروزرسانی کلیک کنید.
                                            </p>
                                            <div class="input-group">
                                                <form action="{{ route('admin.robot.setting.update-single-product-id') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <div class="form-row align-items-center">
                                                        <div class="col-auto">
                                                            <label class="sr-only" for="inlineFormInput">کد محصول</label>
                                                            <input name="code" type="text" class="form-control mb-2" id="inlineFormInput" placeholder="کد محصول">
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="submit" class="btn btn-primary mb-2">بروزرسانی</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                @if(session('singleUpdateMessage'))
                                                    <div class="alert alert-success" role="alert">
                                                        {{ session('singleUpdateMessage') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col">
                        <div class="card">
                            <div id="main-card" class="card-content">
                                <div class="card-body">
                                    <div class="card">
                                        <div class="card-header h4">
                                            کپشن محتوا
                                        </div>
                                        <div class="card-body">
                                            @if(session('captionsMessage'))
                                                <div class="alert alert-success" role="alert">
                                                    {{ session('captionsMessage') }}
                                                </div>
                                            @endif
                                            <p class="card-text">
                                                متغییر های در دسترس
                                                <span class="p-1" dir="rtl">
                                                    <hr>
                                                    نام محصول: <b dir="ltr">{$name}</b>
                                                    <br>
                                                    کد محصول: <b dir="ltr">{$barcode}</b>
                                                    <br>
                                                    جنس: <b dir="ltr">{$genus}</b>
                                                    <br>
                                                    سایزبندی/اندازه: <b dir="ltr">{$sizes}</b>
                                                    <br>
                                                    طول: <b dir="ltr">{$width}</b>
                                                    <br>
                                                    تنخور: <b dir="ltr">{$tankhor}</b>
                                                    <br>
                                                    ارتفاع: <b dir="ltr">{$height}</b>
                                                    <br>
                                                    رنگبندی: <b dir="ltr">{$colors}</b>
                                                    <br>
                                                    نوع: <b dir="ltr">{$type}</b>
                                                    <br>
                                                    قیمت: <b dir="ltr">{$price}</b>
                                                    <hr>
                                                </span>
                                            </p>
                                            <form action="{{ route('admin.robot.setting.update-captions') }}" method="post">
                                                {{ csrf_field() }}
                                                <div class="form-group">
                                                    <label for="socks-caption">کپشن جوراب</label>
                                                    <select  name="caption_category[socks_caption]" class="form-control mb-1 col-3">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach ($localCategories as $key => $localCategory)
                                                            <option value="{{ $key }}" @if(isset($captions['caption_category']['socks_caption']) && $captions['caption_category']['socks_caption'] == $key) selected @endif>
                                                                {{ $localCategory }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <textarea
                                                        name="captions[socks_caption]"
                                                        class="form-control"
                                                        id="socks-caption" r
                                                        ows="3">{{ isset($captions['captions']['socks_caption']) ? $captions['captions']['socks_caption'] : '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="shall-caption">کپشن شال و روسری</label>
                                                    <select  name="caption_category[shall_caption]" class="form-control mb-1 col-3">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach ($localCategories as $key => $localCategory)
                                                            <option value="{{ $key }}" @if(isset($captions['caption_category']['shall_caption']) && $captions['caption_category']['shall_caption'] == $key) selected @endif>
                                                                {{ $localCategory }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <textarea
                                                        name="captions[shall_caption]"
                                                        class="form-control"
                                                        id="shall-caption"
                                                        rows="3">{{ isset($captions['captions']['shall_caption']) ? $captions['captions']['shall_caption'] : '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="women-caption">کپشن پوشاک زنانه</label>
                                                    <select  name="caption_category[women_caption]" class="form-control mb-1 col-3">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach ($localCategories as $key => $localCategory)
                                                            <option value="{{ $key }}" @if(isset($captions['caption_category']['women_caption']) && $captions['caption_category']['women_caption'] == $key) selected @endif>
                                                                {{ $localCategory }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <textarea
                                                        name="captions[women_caption]"
                                                        class="form-control"
                                                        id="women-caption"
                                                        rows="3">{{ isset($captions['captions']['women_caption']) ? $captions['captions']['women_caption'] : '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="under-caption">کپشن لباس زیر</label>
                                                    <select  name="caption_category[under_caption]" class="form-control mb-1 col-3">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach ($localCategories as $key => $localCategory)
                                                            <option value="{{ $key }}" @if(isset($captions['caption_category']['under_caption']) && $captions['caption_category']['under_caption'] == $key) selected @endif>
                                                                {{ $localCategory }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <textarea
                                                        name="captions[under_caption]"
                                                        class="form-control"
                                                        id="under-caption"
                                                        rows="3">{{ isset($captions['captions']['under_caption']) ? $captions['captions']['under_caption'] : '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="bag-caption">کپشن کیف</label>
                                                    <select  name="caption_category[bag_caption]" class="form-control mb-1 col-3">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach ($localCategories as $key => $localCategory)
                                                            <option value="{{ $key }}" @if(isset($captions['caption_category']['bag_caption']) && $captions['caption_category']['bag_caption'] == $key) selected @endif>
                                                                {{ $localCategory }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <textarea
                                                        name="captions[bag_caption]"
                                                        class="form-control"
                                                        id="bag-caption"
                                                        rows="3">{{ isset($captions['captions']['bag_caption']) ? $captions['captions']['bag_caption'] : '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="hat-caption">کپشن کلاه</label>
                                                    <select  name="caption_category[hat_caption]" class="form-control mb-1 col-3">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach ($localCategories as $key => $localCategory)
                                                            <option value="{{ $key }}" @if(isset($captions['caption_category']['hat_caption']) && $captions['caption_category']['hat_caption'] == $key) selected @endif>
                                                                {{ $localCategory }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <textarea
                                                        name="captions[hat_caption]"
                                                        class="form-control"
                                                        id="hat-caption"
                                                        rows="3">{{ isset($captions['captions']['hat_caption']) ? $captions['captions']['hat_caption'] : '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="shoe-caption">کپشن کفش</label>
                                                    <select  name="caption_category[shoe_caption]" class="form-control mb-1 col-3">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach ($localCategories as $key => $localCategory)
                                                            <option
                                                                value="{{ $key }}"
                                                                @if(isset($captions['caption_category']['shoe_caption']) && $captions['caption_category']['shoe_caption'] == $key)
                                                                    selected
                                                                @endif>
                                                                {{ $localCategory }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <textarea
                                                        name="captions[shoe_caption]"
                                                        class="form-control"
                                                        id="bag-caption"
                                                        rows="3">{{ isset($captions['captions']['shoe_caption']) ? $captions['captions']['shoe_caption'] : '' }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    ذخیره
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush
