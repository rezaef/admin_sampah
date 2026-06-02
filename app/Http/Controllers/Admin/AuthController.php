<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = strtolower(trim($data['identifier']));
        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [$identifier])
            ->orWhereRaw('LOWER(email) = ?', [$identifier])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password) || ! $user->isAdmin()) {
            return back()->withErrors(['identifier' => 'Kredensial admin tidak sesuai.'])->withInput($request->only('identifier'));
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
