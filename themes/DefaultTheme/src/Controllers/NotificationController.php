<?php

namespace Themes\DefaultTheme\src\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientNotification;
use Illuminate\Database\Eloquent\Builder;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = ClientNotification::whereIn('user_id', [auth()->user()->id, 0])
            ->with('logs')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return view('front::user.notifications.index', compact('notifications'));
    }

    public function read($id)
    {
        $notification = ClientNotification::whereIn('user_id', [auth()->user()->id, 0])
            ->where('id', $id)
            ->first();

        $notification->logs()->create([
            'user_id' => auth()->user()->id
        ]);

        return true;
    }

    public function indexUnread()
    {
        $unreadNotification = ClientNotification::whereIn('user_id', [auth()->user()->id, 0])
            ->whereDoesntHave('logs', function (Builder $q){
            return $q->where('user_id', auth()->user()->id);
        })->orderBy('id', 'DESC')->get();

        return $unreadNotification->toArray();
    }
}
