<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationLogController extends Controller
{
    /**
     * Log Notifikasi (Tahap 10) — jejak audit tiap alert Telegram yang
     * dikirim Node-RED. Scoping sama seperti dashboard admin: superadmin
     * lihat semua, admin_lokasi cuma lokasi yang ditugaskan ke dia.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $allowedLocationIds = $user->hasRole('superadmin')
            ? null
            : $user->locations()->pluck('locations.id');

        $query = NotificationLog::query()->with('device.location')->latest('sent_at');

        if ($allowedLocationIds !== null) {
            $query->whereHas('device', fn($q) => $q->whereIn('location_id', $allowedLocationIds));
        }
        if ($request->filled('location_id')) {
            $query->whereHas('device', fn($q) => $q->where('location_id', $request->location_id));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('is_sent')) {
            $query->where('is_sent', $request->is_sent === '1');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        $locationsQuery = Location::where('is_active', true);
        if ($allowedLocationIds !== null) {
            $locationsQuery->whereIn('id', $allowedLocationIds);
        }
        $locations = $locationsQuery->get();

        return view('notifications.index', compact('logs', 'locations'));
    }
}
