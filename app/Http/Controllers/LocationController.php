<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::withCount('devices')->orderBy('name')->get();

        return view('locations.index', compact('locations'));
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {

        Location::create($request->validated());
        return back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {

        $location->update($request->validated());
        return back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        // cascadeOnDelete di migration devices akan otomatis
        // menghapus semua device di lokasi ini juga — beri
        // peringatan jelas di halaman sebelum konfirmasi hapus.
        $location->delete();

        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
