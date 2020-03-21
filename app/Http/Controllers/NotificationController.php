<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function makeAsRead($notifID) {
        $notification = auth()->user()->notifications()->find($notifID);

        if($notification) {
            $notification->markAsRead();
        }

        return count(Auth::user()->unreadNotifications);
    }

    /*
    public function displayNotifications() {
        $notificationCount = Auth()->user()->unreadNotifications()->count();
        $notifications = Auth()->user()->unreadNotifications()->get();
        $data =  ['notification_count' => $notificationCount,
                  'unread_notification' => json_encode($notifications)];
        return json_encode($data);
    }*/

    public function displayNotifications() {
        Auth::user()->unreadNotifications()->update(['read_at' => Carbon::now()]);
    }

    public function showAllNotifications() {
        return view('modules.notification.index');
    }
}
