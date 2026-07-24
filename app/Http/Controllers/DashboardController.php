<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $locationsQuery = $user->hasRole('superadmin')
            ? Location::where('is_active', true)
            : $user->locations()->where('is_active', true);

        $locations = $locationsQuery->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'province' => $location->province,
                'latest' => Cache::get("location.{$location->id}.latest"),
            ];
        });

        return view('dashboard', compact('locations'));
    }
}
