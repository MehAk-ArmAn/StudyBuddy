<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $data['password'] = $request->input('password', 'ChangeMe12345!');
        User::create($data);
        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user, 'method' => 'PUT']);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $user->update($this->validated($request));
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

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'role' => ['required', Rule::in(['student','parent','teacher','independent_learner','admin'])],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'avatar_style' => ['nullable', 'string', 'max:120'],
            'cosmic_points' => ['nullable', 'integer', 'min:0'],
            'is_admin' => ['nullable', 'boolean'],
        ]);
    }
}
