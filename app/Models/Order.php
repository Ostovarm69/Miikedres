<?php

namespace App\Models;

use App\Events\WalletAmountDecreased;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class Order extends Model
{
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class)->withTrashed();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function OrderTracking()
    {
        return $this->hasMany(OrderTracking::class);
    }

    public function hasPhysicalItem()
    {
        return $this->products()->where('type', 'physical')->first() ? true : false;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items');
    }

    public function isPaid()
    {
        return $this->status == 'paid';
    }

    public function getShipStatusAttribute()
    {
        return $this->shippingStatusText();
    }

    public function shippingStatusText()
    {
        if ($this->hasPhysicalItem()) {

            if ($this->status != 'paid') {
                return 'منتظر پرداخت';
            }

            $text = '';

            switch ($this->shipping_status) {
                case 'pending': {
                        $text = 'در حال بررسی';
                        break;
                    }
                case 'wating': {
                        $text = 'منتظر ارسال';
                        break;
                    }
                case 'sent': {
                        $text = 'ارسال شد';
                        break;
                    }
                case 'canceled': {
                        $text = 'ارسال لغو شد';
                        break;
                    }
            }
            return $text;
        }

        return '-';
    }

    public function statusText()
    {
        switch ($this->status) {
            case "paid": {
                    return 'پرداخت شده';
                }

            case "unpaid": {
                    return 'پرداخت نشده';
                }

            case "canceled": {
                    return 'لغو شده';
                }
        }
    }

    public function scopeFilter($query, Request $request)
    {

        if ($fullname = $request->input('query.fullname')) {
            $query->whereHas('user', function ($q) use ($fullname) {
                $q->WhereRaw("concat(first_name, ' ', last_name) like '%{$fullname}%' ");
            });
        }

        if ($username = $request->input('query.username')) {
            $query->whereHas('user', function ($q) use ($username) {
                $q->where('username', 'like', "%$username%");
            });
        }

        if ($product_name = $request->input('query.product_name')) {
            $query->whereHas('products', function ($q) use ($product_name) {
                $q->where('products.title', 'like', "%$product_name%");
            });
        }

        if ($product_id = $request->input('query.product_id')) {
            $query->whereHas('products', function ($q) use ($product_id) {
                $q->where('products.id', 'like', "%$product_id%");
            });
        }

        $status = $request->input('query.status');

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        $shipping_status = $request->input('query.shipping_status');

        if ($shipping_status && $shipping_status != 'all') {
            $query->where('shipping_status', $shipping_status);
        }

        if ($id = $request->input('query.id')) {
            $query->where('id', $id);
        }

        if ($from_date = $request->input('query.from_date')) {
            $from_date = Jalalian::fromFormat('Y-m-d', $from_date)->toCarbon();

            $query->whereDate('created_at', '>=', $from_date);
        }

        if ($to_date = $request->input('query.to_date')) {
            $to_date = Jalalian::fromFormat('Y-m-d', $to_date)->toCarbon();

            $query->whereDate('created_at', '<=', $to_date);
        }

        if ($request->sort) {

            switch ($request->sort['field']) {
                case 'fullname': {
                        $query->join('users', 'orders.user_id', '=', 'users.id')
                            ->orderBy('users.first_name', $request->sort['sort'])
                            ->orderBy('users.last_name', $request->sort['sort'])
                            ->select('orders.*');
                        break;
                    }
                case 'order_id': {
                        $query->orderBy('id', $request->sort['sort']);
                        break;
                    }
                default: {
                        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $request->sort['field'])) {
                            $query->orderBy($request->sort['field'], $request->sort['sort']);
                        }
                    }
            }
        }

        return $query;
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class)->withTrashed();
    }

    public function gatewayRelation()
    {
        return $this->belongsTo(Gateway::class, 'gateway_id');
    }

    public function totalDiscount()
    {
        return $this->discount_amount ?: 0;
    }

    public function scopeNotCompleted($query)
    {
        return $query->where('status', 'paid')->whereNotIn('shipping_status', ['sent', 'canceled']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeNotPaid($query)
    {
        return $query->where('status', '!=', 'paid');
    }

    public function scopeNotCanceled($query)
    {
        return $query->where('status', '!=', 'canceled');
    }

    public function hasPhysicalProduct()
    {
        foreach ($this->products as $product) {
            if ($product->isPhysical()) {
                return true;
            }
        }

        return false;
    }

    public function payUsingWallet()
    {
        $order  = $this;
        $user   = $order->user;
        $wallet = $user->getWallet();

        if ($wallet->balance() >= $order->price) {
            DB::transaction(function () use ($wallet, $order) {
                $order->update([
                    'status' => 'paid'
                ]);

                $wallet->histories()->create([
                    'type'        => 'withdraw',
                    'amount'      => $order->price,
                    'description' => 'ثبت سفارش',
                    'source'      => 'user',
                    'status'      => 'success',
                    'order_id'    => $order->id
                ]);

                $wallet->refereshBalance();
            });

            event(new WalletAmountDecreased($wallet));

            return true;
        }

        return false;
    }

    public function walletHistory()
    {
        return $this->hasOne(WalletHistory::class)->where('status', 'success');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class)->withTrashed();
    }

    public static function cacheKeys()
    {
        return [
            'admin.orders_count',
            'admin.total_sell'
        ];
    }
}
