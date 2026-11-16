<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudyBuddyAdminAccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.account.edit', [
            'adminUser' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'real_name' => ['nullable', 'string', 'max:160'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        foreach (['name', 'email', 'real_name'] as $column) {
            if (Schema::hasColumn('users', $column)) {
                $user->{$column} = $data[$column] ?? null;
            }
        }

        if ($request->hasFile('profile_photo') && Schema::hasColumn('users', 'profile_photo_path')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            if (!empty($user->profile_photo_path) && !preg_match('/^https?:\/\//i', $user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $path;
        }

        $user->save();

        return back()->with('status', 'Admin profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Type your current admin password first.',
            'password.required' => 'Type your new admin password.',
            'password.min' => 'Your new password must be at least 8 characters.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.different' => 'Your new password must be different from the current password.',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is not correct. If you forgot it, use the reset command I gave below.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ]);

        if (Schema::hasColumn('users', 'remember_token')) {
            $user->remember_token = Str::random(60);
        }

        $user->save();

        $request->session()->regenerate();

        return back()->with('status', 'Admin password updated successfully. Use the new password next login.');
    }
}
