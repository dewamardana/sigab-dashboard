<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\Location;
use App\Models\SensorType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $devicesQuery = Device::with(['location', 'sensorTypes']);

        if (! $user->hasRole('superadmin')) {
            $locationIds = $user->locations()->pluck('locations.id');
            $devicesQuery->whereIn('location_id', $locationIds);
        }

        $devices = $devicesQuery->orderBy('device_id')->get();

        $locations = $user->hasRole('superadmin')
            ? Location::orderBy('name')->get()
            : $user->locations()->orderBy('name')->get();

        $sensorTypes = SensorType::orderBy('is_core', 'desc')->orderBy('name')->get();

        return view('devices.index', compact('devices', 'locations', 'sensorTypes'));
    }

    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        $device = Device::create($request->safe()->except('sensor_type_ids'));

        $device->sensorTypes()->sync($request->input('sensor_type_ids', []));

        return back()->with('success', 'Perangkat berhasil ditambahkan.');
    }

    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
        $device->update($request->safe()->except('sensor_type_ids'));

        $device->sensorTypes()->sync($request->input('sensor_type_ids', []));

        return back()->with('success', 'Perangkat berhasil diperbarui.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasRole('superadmin')) {
            $allowedIds = $user->locations()->pluck('locations.id')->toArray();
            abort_if(! in_array($device->location_id, $allowedIds), 403);
        }

        $device->delete();

        return back()->with('success', 'Perangkat berhasil dihapus.');
    }
}
