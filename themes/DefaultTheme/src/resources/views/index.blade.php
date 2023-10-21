@extends('front::layouts.master')

@push('meta')
    <meta name="description" content="مایک دریس ، خاص ترین فروشگاه پوشاک بانوان">
    <meta name="keywords" content="{{ option('info_tags') }}">

    <link rel="canonical" href="{{ url('/') }}" />

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "{{ route('front.index') }}",
            "name": "{{ option('site_title') }}",
            "logo": "{{ option('info_logo') ? asset(option('info_logo')) : asset(config('front.asset_path') . 'img/logo.png') }}",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ route('front.products.search') }}/?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
    </script>
@endpush

@section('content')

    <!-- Start main-content -->
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">
            @foreach ($widgets as $widget)
                @switch($widget->key)
                    @case('main-slider')
                        @include('front::widgets.main-slider')
                        @break

                    @case('products-default-block')
                        @include('front::widgets.products-default-block')
                        @break

                    @case('products-colorful-block')
                        @include('front::widgets.products-colorful-block')
                        @break

                    @case('middle-banners')
                        @include('front::widgets.middle-banners')
                        @break

                    @case('coworker-sliders')
                        @include('front::widgets.coworker-sliders')
                        @break

                    @case('sevices-sliders')
                        @include('front::widgets.sevices-sliders')
                        @break

                    @case('categories')
                        @include('front::widgets.categories')
                        @break

                    @case('posts')
                        @include('front::widgets.posts')
                        @break
                @endswitch
            @endforeach

        </div>

    </main>
    <!-- End main-content -->

@endsection
