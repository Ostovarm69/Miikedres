<?php

namespace App\Http\Controllers\Back;

use App\Events\OrderCreated;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Back\Order\OrderStoreRequest;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Http\Resources\Datatable\Order\OrderCollection;
use App\Jobs\OrderCommentSms;
use App\Models\Carrier;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Price;
use App\Models\Product;
use App\Models\Province;
use App\Models\SizeType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index()
    {
        $sizeTypes = SizeType::latest()->get();

        return view('back.orders.index' , compact('sizeTypes'));
    }

    public function apiIndex(Request $request)
    {
        $this->authorize('orders.index');

        $orders = Order::filter($request);

        $orders = datatable($request, $orders);

        return new OrderCollection($orders);
    }

    public function show(Order $order)
    {
        return view('back.orders.show', compact('order'));
    }

    public function storeTracking(Order $order, Request $request)
    {
        $request->validate([
            'type'        => 'required|string',
            'tracking_id' => 'required|string'
        ]);

        $order->OrderTracking()->create($request->only(['type', 'tracking_id']));

        return redirect()->back();
    }

    public function deleteTracking(OrderTracking $trackingId)
    {
        $trackingId->delete();

        return redirect()->back();
    }

    public function create()
    {
        $provinces = Province::detectLang()->orderBy('ordering')->get();
        $carriers  = Carrier::active()->get();

        return view('back.orders.create', compact('provinces', 'carriers'));
    }

    public function store(OrderStoreRequest $request)
    {
        $user = User::firstOrCreate(
            [
                'username' => $request->username
            ],
            [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name
            ]
        );

        $order_price = 0;

        foreach ($request->products as $requestProduct) {
            $product = Product::find($requestProduct['id']);
            $price   = $product->prices()->find($requestProduct['price_id']);

            $orderItems[] = [
                'product_id'      => $product->id,
                'title'           => $product->title,
                'price'           => $price->discountPrice(),
                'real_price'      => $price->tomanPrice(),
                'quantity'        => $requestProduct['quantity'],
                'discount'        => $price->discount,
                'price_id'        => $price->id,
            ];

            $order_price += $price->discountPrice() * $requestProduct['quantity'];
        }

        $order_price += $request->shipping_cost;
        $order_price -= $request->discount_amount;

        $order = Order::create([
            'user_id'           => $user->id,
            'name'              => $request->first_name . ' ' . $request->last_name,
            'mobile'            => $request->username,
            'province_id'       => $request->province_id,
            'city_id'           => $request->city_id,
            'postal_code'       => $request->postal_code,
            'carrier_id'        => $request->carrier_id,
            'address'           => $request->address,
            'description'       => $request->description,
            'shipping_cost'     => $request->shipping_cost ?: 0,
            'status'            => 'paid',
            'shipping_status'   => $request->shipping_status,
            'discount_amount'   => $request->discount_amount,
            'price'             => $order_price
        ]);

        $order->items()->createMany($orderItems);

        event(new OrderCreated($order));

        return response('success');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->transactions()->delete();

        $order->delete();
        toastr()->success('سفارش با موفقیت حذف شد.');

        return redirect()->route('admin.orders.index');
    }

    public function multipleDestroy(Request $request)
    {
        $this->authorize('orders.delete');

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:orders,id',
        ]);

        foreach ($request->ids as $id) {
            $order = Order::find($id);
            $this->destroy($order);
        }

        return response('success');
    }

    public function printAllShippingForms(Request $request)
    {
        $this->authorize('orders.view');

        foreach ($request->ids as $id) {
            $orders = Order::paid()->whereIn('id', $request->ids)->get();
        }

        return view('back.orders.print-all-shipping-forms', compact('orders'));
    }

    public function printAll(Request $request)
    {
        $this->authorize('orders.view');

        foreach ($request->ids as $id) {
            $orders = Order::paid()->whereIn('id', $request->ids)->get();
        }

        return view('back.orders.print-all', compact('orders'));
    }

    public function shipping_status(Order $order, Request $request)
    {
        $this->authorize('orders.update');

        $this->validate($request, [
            'status' => 'required',
        ]);

        $order->update([
            'shipping_status' => $request->status
        ]);

        if ($request->status=="sent") {
            dispatch(new OrderCommentSms($order))->delay(now()->addDay(4));

        }

        return response('success');
    }

    public function shippingsStatus(Request $request)
    {
        $this->authorize('orders.update');

        $request->validate([
            'status' => 'required',
        ]);

        $orders = Order::whereIn('id', $request->ids)->get();

        foreach ($orders as $order) {
            if (!$order->isPaid()) {
                throw ValidationException::withMessages(['id' => 'سفارش شماره ' . $order->id . 'پرداخت نشده است ']);
            }
        }

        foreach ($orders as $order) {
            $order->update([
                'shipping_status' => $request->status
            ]);
        }

        return response('success');
    }

    public function notCompleted()
    {
        $this->authorize('orders.index');

        $prices = Price::whereHas('orderItems', function ($q) {
            $q->whereHas('order', function ($q2) {
                $q2->notCompleted();
            })->whereHas('product', function ($q3) {
                $q3->physical();
            });
        })->paginate(20);

        return view('back.orders.not-completed', compact('prices'));
    }

    public function print(Order $order)
    {
        $this->authorize('orders.view');

        return view('back.orders.print', compact('order'));
    }

    public function shippingForm(Order $order)
    {
        $this->authorize('orders.view');

        return view('back.orders.shipping-form', compact('order'));
    }

    public function export(Request $request)
    {
        $this->authorize('orders.export');

        $orders = Order::filter($request)->get();

        switch ($request->export_type) {
            case 'excel': {
                    return $this->exportExcel($orders, $request);
                    break;
                }
            default: {
                    return $this->exportPrint($orders, $request);
                }
        }
    }

    public function userInfo(Request $request)
    {
        $this->authorize('orders.create');

        $request->validate([
            'input' => 'required|in:username',
        ]);

        if (!$request->term) {
            return;
        }

        $input = $request->input('input');
        $term  = $request->input('term');

        switch ($input) {
            case "username": {
                    $users = User::with('address')
                        ->where('username', 'like', "%$term%")
                        ->latest()->take(10)
                        ->get();
                    break;
                }
        }

        return response()->json($users);
    }

    public function productsList(Request $request)
    {
        $this->authorize('orders.create');

        $term = $request->term;

        if (!$term) {
            return;
        }

        $products = Product::with('getPrices')
            ->available()
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', "%$term%")->orWhere('title_en', 'like', "%$term%");
            })
            ->orderByStock()
            ->latest()
            ->take(10)
            ->get();

        return ProductResource::collection($products);
    }

    private function exportExcel($orders)
    {
        return Excel::download(new OrdersExport($orders), 'orders.xlsx');
    }
}
