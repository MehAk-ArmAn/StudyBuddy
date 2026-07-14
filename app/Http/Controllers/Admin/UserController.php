<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->paginate(24),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(), 'method' => 'POST']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($request->input('password', 'ChangeMe12345!'));
        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user, 'method' => 'PUT']);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own admin account.']);
        }

        $user->delete();

        return back()->with('status', 'User deleted.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $id = $user?->id;

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($id)],
            'real_name' => ['nullable', 'string', 'max:160'],
            'role' => ['required', Rule::in(['student','parent','teacher','independent_learner','admin'])],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'avatar_style' => ['nullable', 'string', 'max:120'],
            'profile_photo_path' => ['nullable', 'string', 'max:255'],
            'cosmic_points' => ['nullable', 'integer', 'min:0'],
            'is_admin' => ['nullable', 'boolean'],
        ];

        if ($request->isMethod('post')) {
            $rules['password'] = ['required', Password::min(10)->letters()->numbers()];
        } else {
            $rules['password'] = ['nullable', Password::min(10)->letters()->numbers()];
        }

        $data = $request->validate($rules);
        $data['is_admin'] = $request->boolean('is_admin');

        return $data;
    }
}
