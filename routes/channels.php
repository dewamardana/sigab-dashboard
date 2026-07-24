<?php

use App\Models\Location;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.location.{locationId}', function ($user, $locationId) {
    if ($user->hasRole('superadmin')) {
        return true;
    }

    if ($user->hasRole('admin_lokasi')) {
        return $user->locations()->where('locations.id', $locationId)->exists();
    }

    return false;
});
