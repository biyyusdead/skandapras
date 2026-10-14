<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Halaman daftar semua notifikasi.
     */
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'all'); // all | unread

        $query = auth()->user()->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate(20);

        return view('notifications.index', compact('notifications', 'filter'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca & redirect.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('admin.dashboard');

        return redirect($url);
    }

    /**
     * Tandai semua sebagai sudah dibaca.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Hapus notifikasi.
     */
    public function destroy(string $id): RedirectResponse
    {
        auth()->user()->notifications()->findOrFail($id)->delete();

        return back()->with('success', 'Notifikasi dihapus.');
    }

    /**
     * Hapus semua notifikasi.
     */
    public function clearAll(): RedirectResponse
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }

    public function dropdown(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'title'      => $n->data['title'] ?? 'Notifikasi',
                    'message'    => $n->data['message'] ?? '',
                    'url'        => isset($n->data['url'])
                        ? route('notifications.read', $n->id)
                        : '#',
                    'icon'       => $n->data['icon'] ?? 'bell',
                    'color'      => $n->data['color'] ?? 'primary',
                    'read'       => $n->read_at !== null,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications->count(),
        ]);
    }
}