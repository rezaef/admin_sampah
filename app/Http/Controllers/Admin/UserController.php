<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(HttpRequest $request): View
    {
        $role = $request->query('role');
        $search = $request->query('search');
        $sort = $request->query('sort', 'newest');

        $query = User::query()->withCount(['classifications', 'reports']);

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($role && in_array($role, ['user', 'admin'])) {
            $query->where('role', $role);
        }

        // Sorting
        if ($sort === 'points') {
            $query->orderByDesc('points_balance');
        } elseif ($sort === 'scans') {
            $query->orderByDesc('classifications_count');
        } elseif ($sort === 'reports') {
            $query->orderByDesc('reports_count');
        } else {
            $query->orderByDesc('created_at');
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'role', 'search', 'sort'));
    }

    public function update(HttpRequest $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'points_balance' => ['required', 'integer', 'min:0'],
            'role'           => ['required', 'string', 'in:user,admin'],
            'password'       => ['nullable', 'string', 'min:6'],
        ]);

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menurunkan role Anda sendiri.');
        }

        $updateData = [
            'points_balance' => $data['points_balance'],
            'role'           => $data['role'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }

        $user->update($updateData);

        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
