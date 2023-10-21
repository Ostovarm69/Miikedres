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
                                    <li class="breadcrumb-item active">تنظیمات دسته بندی ها</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="content-body">
                <section class="users-edit">
                    <div class="card">
                        <div id="main-card" class="card-content">
                            <div class="card-body">

                                <div class="tab-content">

                                    <form id="information-form" action="{{ route('admin.robot.categories-mapping.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>دسته بندی مبدا</th>
                                                <th>دسته بندی پدر</th>
                                                <th>دسته بندی مقصد</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($havinmodeCategories as $category)
                                                <tr>

                                                    <td>
                                                        {{ $category['title'] }} ({{ $category['id'] }})
                                                        <input type="hidden" name="source_category_id[]" value="{{ $category['id'] }}">
                                                    </td>
                                                    <td>{{ $category['parent'] ?? '-' }}</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select  name="target_category_id[]" class="form-control category-mapping">
                                                                <option value="">انتخاب کنید</option>
                                                                @foreach ($localCategories as $localCategory)
                                                                    <option value="{{ $localCategory['id'] }}" @if(isset($mappedCategories[$category['id']]) && $localCategory['id'] == $mappedCategories[$category['id']]) selected @endif>
                                                                        {{ $localCategory['title'] }}
                                                                        @if($localCategory['parent'] !== null)
                                                                             ({{ $localCategoriesPluck[$localCategory['parent']] }})
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                            <button type="submit" class="btn btn-primary glow">ذخیره تغییرات</button>

                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@include('back.partials.plugins', ['plugins' => ['jquery-tagsinput', 'jquery.validate', 'mapp', 'google-map']])
@push('scripts')
    <script src="{{ asset('back/assets/js/pages/categories-mapping/index.js') }}?v=2"></script>
@endpush
