<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with(['roles', 'locations'])->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('users.index', compact('users', 'locations'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->validated('role'));

        if ($request->validated('role') === 'admin_lokasi') {
            $user->locations()->sync($request->input('location_ids', []));
        }

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->validated('password'));
        }

        $user->update($data);

        $user->syncRoles([$request->validated('role')]);

        $user->locations()->sync(
            $request->validated('role') === 'admin_lokasi'
                ? $request->input('location_ids', [])
                : []
        );

        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        abort_if($user->id === $currentUser->id, 403, 'Tidak bisa menghapus akun sendiri.');

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
