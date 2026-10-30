<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $categories = Category::withCount('items')
            ->withSum('items', 'stok_total')
            ->withSum('items', 'stok_tersedia')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', "%{$search}%")
                        ->orWhere('kode_kategori', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->orderBy('kode_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $category = Category::create($validated);

        ActivityLog::record(
            'CREATE',
            'Categories',
            "Menambahkan kategori barang baru: {$category->nama_kategori} ({$category->kode_kategori}).",
            $category,
            ['new' => $category->only(['kode_kategori', 'nama_kategori', 'deskripsi'])]
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori barang "'.$validated['nama_kategori'].'" berhasil ditambahkan.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();
        $oldData = $category->only(['kode_kategori', 'nama_kategori', 'deskripsi']);

        $category->update($validated);

        ActivityLog::record(
            'UPDATE',
            'Categories',
            "Memperbarui kategori barang: {$category->nama_kategori} ({$category->kode_kategori}).",
            $category,
            [
                'old' => $oldData,
                'new' => $category->only(['kode_kategori', 'nama_kategori', 'deskripsi']),
            ]
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori barang "'.$category->nama_kategori.'" berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->items()->exists()) {
            return back()->with('error', 'Kategori "'.$category->nama_kategori.'" tidak dapat dihapus karena masih memiliki data barang.');
        }

        $deletedData = $category->only(['id', 'kode_kategori', 'nama_kategori', 'deskripsi']);
        $nama = $category->nama_kategori;
        $category->delete();

        ActivityLog::record(
            'DELETE',
            'Categories',
            "Menghapus kategori barang: {$deletedData['nama_kategori']} ({$deletedData['kode_kategori']}).",
            null,
            ['deleted' => $deletedData]
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori barang "'.$nama.'" berhasil dihapus.');
    }
}
