<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Riwayat & Laporan (Tahap 10) — tabel data sensor historis mentah
     * (bukan grafik), bisa difilter & diunduh CSV. Melengkapi halaman
     * device (Tahap 9) yang cuma menampilkan grafik, untuk kebutuhan
     * pelaporan/audit yang butuh angka presisi per baris data.
     */
    public function index(Request $request): View
    {
        $logs = $this->filteredQuery($request)->paginate(25)->withQueryString();

        return view('reports.index', [
            'records' => $logs,
            'locations' => $this->allowedLocations(),
        ]);
    }

    /**
     * Unduh hasil filter yang sama sebagai CSV — dibatasi 5000 baris
     * supaya tidak membebani server kalau rentang tanggalnya sangat luas.
     */
    public function export(Request $request): StreamedResponse
    {
        $records = $this->filteredQuery($request)->limit(5000)->get();

        $filename = 'laporan-sensor-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($records) {
            $out = fopen('php://output', 'w');
          
            fputcsv($out, ['Waktu', 'Lokasi', 'Perangkat', 'Status', 'Skor Fuzzy', 'Freeboard (m)', 'TMA (cm)', 'Curah Hujan (mm/jam)', 'Level Kritis']);
            foreach ($records as $r) {
                fputcsv($out, [
                    $r->recorded_at?->format('Y-m-d H:i:s'),
                    $r->device->location->name ?? '-',
                    $r->device->name ?: $r->device->device_id ?? '-',
                    $r->status,
                    $r->getReading('status_skor'),
                    $r->getReading('freeboard_m'),
                    $r->getReading('tma_cm'),
                    $r->getReading('hujan_intensitas'),
                    $r->getReading('level_kritis'),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function filteredQuery(Request $request)
    {
        $query = SensorData::query()->with('device.location')->latest('recorded_at');

        $allowedLocationIds = $this->allowedLocationIds();
        if ($allowedLocationIds !== null) {
            $query->whereHas('device', fn($q) => $q->whereIn('location_id', $allowedLocationIds));
        }
        if ($request->filled('location_id')) {
            $query->whereHas('device', fn($q) => $q->where('location_id', $request->location_id));
        }
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('recorded_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('recorded_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function allowedLocationIds()
    {
        $user = Auth::user();

        return $user->hasRole('superadmin') ? null : $user->locations()->pluck('locations.id');
    }

    private function allowedLocations()
    {
        $query = Location::where('is_active', true)->with('devices');
        $allowedLocationIds = $this->allowedLocationIds();

        if ($allowedLocationIds !== null) {
            $query->whereIn('id', $allowedLocationIds);
        }

        return $query->get();
    }
}
