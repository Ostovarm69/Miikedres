<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class Price extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function get_attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function getAttributesName()
    {
        if ($this->product->isDownload()) {
            return $this->file->title;
        }

        $title = '';
        $attributes = $this->get_attributes;

        foreach ($attributes as $attribute) {
            $title .= ' ' . $attribute->group->name . ' : ' . $attribute->name . ($attributes->last() == $attribute ? '' : '،');
        }

        return $title;
    }

    public function price()
    {
        return (float) $this->price;
    }

    public function tomanPrice()
    {
        return to_round_price($this->price, $this->product);
    }

    public function changes()
    {
        return $this->hasMany(PriceChange::class, 'price_id');
    }

    public function createChange($new_price, $new_discount, $new_stock = null, $old_price = null, $old_discount = null, $old_stock = null)
    {
        if ($this->product->currency) {
            $new_price = $new_price * $this->product->currency->amount;
        }

        if ($new_stock === null) {
            $new_stock = $this->stock;
        }

        if ($old_price) {
            if ($this->product->currency) {
                $old_price = $old_price * $this->product->currency->amount;
            }
        } else {
            $old_price = $this->tomanPrice();
        }

        if ($old_discount === null) {
            $old_discount = $this->discount;
        }

        if ($old_stock === null) {
            $old_stock = $this->stock;
        }

        $create_change = false;

        if ($new_price != $old_price) {
            $create_change = true;
        }

        if ($new_discount != $old_discount) {
            $create_change = true;
        }

        if ($this->discount_price != $this->discountPrice()) {
            $create_change = true;
        }

        $last_change = $this->changes()->latest()->first();

        if (!$last_change || ($last_change->is_available && $new_stock <= 0) || (!$last_change->is_available && $new_stock > 0)) {
            $create_change = true;
        }

        if ($create_change) {
            $this->changes()->create([
                'product_id'     => $this->product_id,
                'price'          => $new_price,
                'discount'       => $new_discount,
                'is_available'   => $new_stock > 0
            ]);
        }
    }

    public function createFile($title, $file, $status)
    {
        $filename = date("Y-m-d") . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('product-files', $filename, 'downloads');

        $this->file()->create([
            'title'    => $title,
            'file'     => $filename,
            'disk'     => 'downloads',
            'size'     => $file->getSize(),
            'status'   => $status,
        ]);
    }

    public function updateFile($title, $file, $status)
    {
        if ($file) {
            $filename = date("Y-m-d") . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('product-files', $filename, 'downloads');
            $size = $file->getSize();
        } else {
            $filename = $this->file->file;
            $size     = $this->file->size;
        }

        $this->file()->update([
            'title'    => $title,
            'file'     => $filename,
            'disk'     => 'downloads',
            'size'     => $size,
            'status'   => $status,
        ]);
    }

    public function hasStock($quantity, $with_attributes = false)
    {
        if ($this->product->isDownload()) {
            return [
                'status'  => true,
                'message' => 'ok'
            ];
        }

        if ($this->cart_min !== null && $this->cart_min > $quantity && $this->stock > $quantity) {
            if ($with_attributes) {
                return [
                    'status'  => false,
                    'message' => 'حداقل تعداد برای محصول "' . $this->product->title . '"' . $this->getAttributesName() . ' "' . $this->cart_min . '" میباشد'
                ];
            }

            return [
                'status'  => false,
                'message' => 'لطفا تعداد بیشتر از یا مساوی ' . $this->cart_min . ' انتخاب کنید.'
            ];
        }

        if ($this->stock < $quantity || ($this->cart_max !== null && $this->cart_max < $quantity)) {
            if ($with_attributes) {
                return [
                    'status'  => false,
                    'message' => 'موجودی محصول "' . $this->product->title . ' ' . $this->getAttributesName() . '" کافی نیست.'
                ];
            }

            return [
                'status'  => false,
                'message' => 'موجودی محصول کافی نمی باشد'
            ];
        }

        return [
            'status'  => true,
            'message' => 'ok'
        ];
    }

    public function isDownloadable()
    {
        if ($this->file && $this->file->status == 'inactive') {
            return false;
        }

        if ($this->price == 0) {
            return true;
        }

        if (auth()->check()) {

            return auth()->user()->hasBought($this) || auth()->user()->can('products.update');
        }

        return false;
    }

    public function downloadLink()
    {
        $time = Carbon::now()->addHours(5)->getTimestamp();

        $hash = Hash::make(config('app.key') . $time . $this->id);

        $link = Route::has('front.products.download') ? route('front.products.download', ['price' => $this]) : '#';

        $link .= "?mac=$hash&time=$time";

        return $link;
    }

    public function discountPrice()
    {
        return get_discount_price($this->price, $this->discount, $this->product);
    }

    public function pendingToSend()
    {
        return $this
            ->orderItems()
            ->whereHas('order', function ($q) {
                $q->notCompleted();
            })
            ->whereHas('product', function ($q3) {
                $q3->physical();
            })
            ->sum('order_items.quantity');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
