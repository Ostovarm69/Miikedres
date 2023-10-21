<div class="product-card mb-2 mx-res-0">
    @if($product->special)
        <div class="promotion-badge">
         {{ trans('front::messages.categories.special-sale') }}
        </div>
    @endif
    <div class="product-head">
        @if ($product->labels->count())
            <div class="row">
                <div class="btn-group" role="group">
                    @foreach ($product->labels as $label)
                        <div class="fild_products">
                            <span>{{ $label->title }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="rating-stars">
            <i class="mdi mdi-star active"></i>
            <i class="mdi mdi-star active"></i>
            <i class="mdi mdi-star active"></i>
            <i class="mdi mdi-star active"></i>
            <i class="mdi mdi-star active"></i>
        </div>
        @if($product->discount)
            <div class="discount">
                <span>{{ $product->discount }}%</span>
            </div>
        @endif
    </div>
    <a class="product-thumb" href="{{ route('front.products.show', ['product' => $product]) }}">
        <img data-src="{{ $product->image ? asset($product->image) : asset('/no-image-product.png') }}" src="{{ theme_asset('images/600-600.png') }}" alt="{{ $product->title }}">
    </a>
    <div class="product-card-body">

        <h5 class="product-title">
            <a href="{{ route('front.products.show', ['product' => $product]) }}">{{ $product->title }}</a>
            <div class="product-rating text-left">
                {{ $product->rating ?? 0 }}
                <i class="mdi mdi-star" style="color: gold;"></i>
            </div>
        </h5>

        @if($product->category)
            <a class="product-meta" href="{{ route('front.products.category', ['category' => $product->category]) }}">{{ $product->category->title }}</a>
        @endif

        <div class="product-prices-div">
            <span class="product-price">{{ $product->getLowestPrice() }}</span>

            @if($product->getLowestDiscount())
                <del class="product-price text-danger">{{ $product->getLowestDiscount() }}</del>
            @endif
        </div>

        @if ($product->isSinglePrice())
            <div class="cart">
                <a data-action="{{ route('front.cart.store', ['product' => $product]) }}" class="d-flex align-items-center add-to-cart-single" href="javascript:void(0)"><i class="mdi mdi-plus px-2"></i>
                    <span>{{ trans('front::messages.categories.add-to-cart') }}</span>
                </a>
            </div>
        @endif

    </div>
</div>
