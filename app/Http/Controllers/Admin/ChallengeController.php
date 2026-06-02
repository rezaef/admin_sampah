<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function index(): View
    {
        $challenges = Challenge::query()->latest('starts_at')->paginate(20);

        return view('admin.challenges.index', compact('challenges'));
    }

    public function create(): View
    {
        return view('admin.challenges.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'target' => ['required', 'integer', 'min:1'],
            'reward_points' => ['required', 'integer', 'min:0'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Challenge::create([...$data, 'is_active' => (bool) ($data['is_active'] ?? false)]);

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge berhasil ditambahkan.');
    }

    public function edit(Challenge $challenge): View
    {
        return view('admin.challenges.edit', compact('challenge'));
    }

    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'target' => ['required', 'integer', 'min:1'],
            'reward_points' => ['required', 'integer', 'min:0'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $challenge->update([...$data, 'is_active' => (bool) ($data['is_active'] ?? false)]);

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge berhasil diperbarui.');
    }

    public function destroy(Challenge $challenge): RedirectResponse
    {
        $challenge->delete();

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge berhasil dihapus.');
    }
}
