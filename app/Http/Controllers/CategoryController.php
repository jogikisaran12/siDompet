<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('categories')
                    ->where('user_id', auth()->id())
                    ->where('type', $request->type)
            ],
            'type' => 'required|in:income,expense'
        ]);

        $request->user()->categories()->create($data);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        abort_unless($category->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('categories')
                    ->where('user_id', auth()->id())
                    ->where('type', $category->type)
                    ->ignore($category->id)
            ]
        ]);

        $category->update($data);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        abort_unless($category->user_id === auth()->id(), 403);

        if ($category->transactions()->exists()) {
            return back()->withErrors([
                'category' => 'Kategori sedang digunakan oleh transaksi.'
            ]);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}