<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RewardController extends Controller
{
    public function index(): View
    {
        $rewards = Reward::query()->orderBy('points_cost')->paginate(20);

        return view('admin.rewards.index', compact('rewards'));
    }

    public function create(): View
    {
        return view('admin.rewards.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'points_cost' => ['required', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Reward::create([...$data, 'is_active' => (bool) ($data['is_active'] ?? false)]);

        return redirect()->route('admin.rewards.index')->with('success', 'Reward berhasil ditambahkan.');
    }

    public function edit(Reward $reward): View
    {
        return view('admin.rewards.edit', compact('reward'));
    }

    public function update(Request $request, Reward $reward): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'points_cost' => ['required', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $reward->update([...$data, 'is_active' => (bool) ($data['is_active'] ?? false)]);

        return redirect()->route('admin.rewards.index')->with('success', 'Reward berhasil diperbarui.');
    }

    public function destroy(Reward $reward): RedirectResponse
    {
        $reward->delete();

        return redirect()->route('admin.rewards.index')->with('success', 'Reward berhasil dihapus.');
    }
}
