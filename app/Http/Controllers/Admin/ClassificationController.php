<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassificationController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $query = Classification::query()->with('user');

        if ($category && in_array($category, ['organik', 'anorganik', 'tidak_diketahui'])) {
            $query->where('category', $category);
        }

        $classifications = $query->latest('detected_at')->paginate(20)->withQueryString();

        return view('admin.classifications.index', compact('classifications', 'category'));
    }
}
