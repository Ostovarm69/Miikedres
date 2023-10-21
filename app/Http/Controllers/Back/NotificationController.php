<?php

namespace App\Http\Controllers\Back;

use App\Events\NotificationCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Back\ClientNotification\ClientNotificationRequest;
use App\Http\Resources\Api\V1\ClientNotification\ClientNotificationCollection;
use App\Models\ClientNotification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function __construct()
    {

    }

    public function index()
    {
        $this->authorize('client-notifications.index');

        return view('back.clientNotifications.index');
    }

    public function store(ClientNotificationRequest $request)
    {
        $this->authorize('client-notifications.create');

        if ($request->input('sms', 'off') === 'on' && option('notification_sms', 'off') === 'off') {
            return $this->respondError(
                'سرویس پیامکی برای اطلاعیه ها غیر فعال است',
                422
            );
        }

        $notification = ClientNotification::create($request->all());

        if ($request->input('sms', 'off') === 'on' && option('notification_sms', 'off') === 'on') {
            event(new NotificationCreated($notification->user));
        }

        return response('success');
    }

    public function create()
    {
        $this->authorize('client-notifications.create');

        $users = User::all();

        return view('back.clientNotifications.create', compact('users'));
    }

    public function apiIndex(Request $request)
    {
        $this->authorize('client-notifications.index');

        $clientNotifications = ClientNotification::with(['user'])->filter($request);

        $clientNotifications = datatable($request, $clientNotifications);

        return new ClientNotificationCollection($clientNotifications);
    }

    public function destroy(ClientNotification $clientNotification)
    {
        $this->authorize('client-notifications.delete');

        $clientNotification->delete();

        return response('success');
    }

    public function multipleDestroy(Request $request)
    {
        $this->authorize('client-notifications.delete');

        $this->authorize('products.delete');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:client_notifications,id',
        ]);

        foreach ($request->ids as $id) {
            $product = ClientNotification::find($id);
            $this->destroy($product);
        }

        return response('success');
    }
}
