<?php

namespace App\Models;

use App\Traits\Languageable;
use App\Traits\ProductScopes;
use App\Traits\Taggable;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class Product extends Model
{
    use HasFactory, sluggable, Taggable, ProductScopes, Languageable;

    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug',
            ],
        ];
    }

    //------------- start relations

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function gallery()
    {
        return $this->morphMany(Gallery::class, 'galleryable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function specificationGroups()
    {
        return $this->belongsToMany(SpecificationGroup::class, 'product_specification')->withPivot(['specification_group_id', 'group_ordering', 'specification_ordering', 'value', 'special'])->withTimestamps()->orderBy('group_ordering');
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class, 'product_specification')
            ->withPivot(['specification_group_id', 'group_ordering', 'specification_ordering', 'value', 'special'])
            ->withTimestamps()
            ->orderBy('group_ordering')
            ->orderBy('specification_ordering');
    }

    public function specialSpecifications()
    {
        return $this->specifications()->where('special', true)->get();
    }

    public function specType()
    {
        return $this->belongsTo(SpecType::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function source()
    {
        return $this->hasOne(ProductMapping::class, 'target_product_id', 'id');
    }

    public function lowestPrice()
    {
        return $this->hasOne(Price::class)
            ->where('stock', '>', 0)
            ->orderBy('discount_price');
    }

    public function sizeGuide()
    {
        return $this->hasOne(ProductSizesGuide::class);
    }

    public function getPrices($stock = 0)
    {
        return $this->hasMany(Price::class)
            ->where('stock', '>', $stock)
            ->orderBy('discount_price');
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function priceChanges()
    {
        return $this->hasMany(PriceChange::class);
    }

    public function files()
    {
        return $this->hasManyThrough(File::class, Price::class, 'product_id', 'fileable_id')
            ->where(
                'fileable_type',
                Price::class
            );
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function relatedProducts()
    {
        if ($this->category) {
            return $this->category->allPublishedProducts()->where('id', '!=', $this->id);
        }

        return Product::query()->where('id', '!=', $this->id);
    }

    public function labels()
    {
        return $this->morphToMany(Label::class, 'labelable')->withTimestamps();
    }

    //------------- end relations

    public function getDiscountPriceAttribute()
    {
        if ($this->discount) {
            return $this->price - ($this->price * ($this->discount / 100));
        }

        return $this->price;
    }

    public function link()
    {
        return Route::has('front.products.show') ? route('front.products.show', ['product' => $this]) : '#';
    }

    public function isPhysical()
    {
        return $this->type == 'physical';
    }

    public function isDownload()
    {
        return $this->type == 'download';
    }

    public function isSpecial()
    {
        return $this->special;
    }

    public function addableToCart()
    {
        if ($this->type != 'physical') {
            return true;
        }

        if ($this->price_type == "multiple-price" && !$this->prices()->where('stock', '>', 0)->first()) {
            return false;
        }

        return true;
    }

    public function isPublished()
    {
        if ($this->category && !$this->category->isPublished()) {
            return false;
        }

        return ($this->published && (!$this->publish_date || $this->publish_date <= Carbon::now()));
    }

    public function isShowable()
    {
        if ($this->isPublished()) {
            return true;
        }

        if (auth()->check() && auth()->user()->can('products')) {
            return true;
        }

        return false;
    }

    public function isComparable()
    {
        return $this->spec_type_id != null;
    }

    public function isUserFavorite()
    {
        return auth()->check() && auth()->user()->favorites()->where('product_id', $this->id)->first();
    }

    public function getLowestPrice($numeric = false)
    {
        $price = $this->lowestPrice;

        if ($this->isDownload()) {
            return $numeric ? null : 'محصول دانلودی';
        }

        if ($price && $price->stock) {
            return $numeric ? $price->discountPrice() : trans('messages.currency.prefix') . number_format($price->discountPrice()) . trans('messages.currency.suffix');
        }

        return $numeric ? null : 'ناموجود';
    }

    public function getLowestDiscount($numeric = false)
    {
        $price = $this->lowestPrice;

        if ($price && $price->discount) {
            return $numeric ? $price->tomanPrice() : trans('messages.currency.prefix') . number_format($price->tomanPrice()) . trans('messages.currency.suffix');
        }

        return null;
    }

    public function getDiscountAttribute()
    {
        $price = $this->lowestPrice;

        if ($price && $price->discount) {
            return $price->discount;
        }

        return null;
    }

    public function getDiscount()
    {
        $price = $this->lowestPrice;

        if ($price && $price->discount) {
            return $price->discount;
        }

        return null;
    }

    public function get_attributes($attributeGroup, $prev_attribute, $groups, $attributes_id, $stock = 0)
    {
        $prices = $this->getPrices($stock)->pluck('id');

        $group_attributes = $attributeGroup->get_attributes()->pluck('id');
        $attributes       = DB::table('attribute_price')->whereIn('price_id', $prices)->whereIn('attribute_id', $group_attributes);

        if ($groups) {
            $group_prices = $this->getPrices();

            foreach ($attributes_id as $att) {
                $group_prices->whereHas('get_attributes', function ($q) use ($att) {
                    $q->where('attribute_id', $att);
                });
            }

            $group_prices = $group_prices->pluck('id');

            $attributes->whereIn('price_id', $group_prices);
        }

        if ($prev_attribute) {
            $prices_have_this_attribute = $this->prices()->whereHas('get_attributes', function ($q) use ($prev_attribute) {
                $q->where('attribute_id', $prev_attribute->id);
            })->pluck('id');

            $this_price_attributes = DB::table('attribute_price')->whereIn('price_id', $prices_have_this_attribute)->pluck('attribute_id');

            $attributes->whereIn('attribute_id', $this_price_attributes);
        }

        $attributes = $attributes->pluck('attribute_id');

        if ($attributes->count()) {
            return Attribute::whereIn('id', $attributes)->get();
        }

        return null;
    }

    public function getPriceWithAttributes($attributes_id)
    {
        foreach ($this->getPrices as $price) {
            $price_attributes = $price->get_attributes()->pluck('attributes.id')->toArray();

            sort($price_attributes);
            sort($attributes_id);

            if ($price_attributes == $attributes_id) {
                return $price;
            }
        }
    }

    public function imageUrl($default = '/empty.jpg')
    {
        if ($this->image) {
            return asset($this->image);
        }

        return $default == '/empty.jpg' ? asset($default) : $default;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public static function clearCache()
    {
        $cache_keys = config('front.cache-forget.products');

        if ($cache_keys) {
            foreach ($cache_keys as $key) {
                Cache::forget($key);
            }
        }

        $cache_keys = self::cacheKeys();

        foreach ($cache_keys as $key) {
            Cache::forget($key);
        }
    }

    public function increaseViewCount()
    {
        $this->increment('view');
    }

    public function getLabels()
    {
        return implode(',', $this->labels()->pluck('title')->toArray());
    }

    public function isSinglePrice()
    {
        return $this->getPrices()->count() == 1;
    }

    public static function cacheKeys()
    {
        return [
            'admin.products_count'
        ];
    }

    public function refreshRating()
    {
        $rating = $this->reviews()->accepted()->sum('rating') / ($this->reviews()->accepted()->count() ?: 1);

        $this->update([
            'rating'        => $rating,
            'reviews_count' => $this->reviews()->accepted()->count()
        ]);
    }

    public function suggestionCount()
    {
        return $this->reviews()->accepted()->where('suggest', 'yes')->count();
    }

    public function suggestionPercent()
    {
        return ($this->suggestionCount() * 100) / $this->reviews()->accepted()->count();
    }

    public function sizeType()
    {
        return $this->belongsTo(SizeType::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class)
            ->withPivot(['value', 'group', 'ordering'])
            ->withTimestamps()
            ->orderBy('group')
            ->orderBy('ordering');
    }
}
