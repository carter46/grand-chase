<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(15);
        
        return view('user.notifications.index', [
            'title' => 'All Notifications',
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if notification belongs to authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->is_read = true;
        $notification->save();
        
        if ($notification->link) {
            return redirect($notification->link);
        }
        
        return back()->with('message', 'Notification marked as read.')
            ->with('type', 'success');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notifications()->update(['is_read' => true]);
        
        return back()->with('message', 'All notifications marked as read.')
            ->with('type', 'success');
    }

    /**
     * Delete a notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if notification belongs to authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->delete();
        
        return back()->with('message', 'Notification deleted successfully.')
            ->with('type', 'success');
    }

    /**
     * Delete all notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();
        
        return back()->with('message', 'All notifications deleted successfully.')
            ->with('type', 'success');
    }
} 