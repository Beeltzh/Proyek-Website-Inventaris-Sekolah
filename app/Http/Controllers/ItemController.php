<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category_id');
        $condition = $request->query('condition', $request->query('kondisi'));
        $availability = $request->query('availability', $request->query('ketersediaan'));

        $items = Item::with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('lokasi', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($condition, fn ($q) => $q->where('kondisi', $condition))
            ->when(in_array($availability, ['available', 'tersedia'], true), fn ($q) => $q->where('stok_tersedia', '>', 0)->where('kondisi', '!=', 'rusak_berat'))
            ->when(in_array($availability, ['empty', 'habis'], true), fn ($q) => $q->where('stok_tersedia', '=', 0))
            ->orderBy('nama_barang')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('nama_kategori')->get();

        return view('items.index', compact('items', 'categories', 'search', 'categoryId', 'condition', 'availability'));
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'loans' => function ($query) {
            $query->with(['user', 'approver', 'returnLog.receiver'])->latest()->take(15);
        }]);

        $categories = Category::orderBy('nama_kategori')->get();

        return view('items.show', compact('item', 'categories'));
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['stok_tersedia'] = (int) $validated['stok_total'];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $uploadDir = public_path('uploads/items');
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename);
            $validated['foto'] = 'uploads/items/'.$filename;
        }

        $item = Item::create($validated);

        ActivityLog::record(
            'CREATE',
            'Items',
            "Menambahkan barang inventaris baru: {$item->nama_barang} ({$item->kode_barang}) sebanyak {$item->stok_total} {$item->satuan}.",
            $item,
            ['new' => $item->only(['kode_barang', 'nama_barang', 'merk', 'lokasi', 'stok_total', 'satuan', 'kondisi'])]
        );

        return redirect()->route('items.index')
            ->with('success', 'Data barang "'.$validated['nama_barang'].'" berhasil ditambahkan.');
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $validated = $request->validated();

        $sedangDipinjam = $item->sedang_dipinjam;
        $newStokTotal = (int) $validated['stok_total'];

        if ($newStokTotal < $sedangDipinjam) {
            return back()->with('error', "Stok total tidak boleh kurang dari jumlah barang yang sedang dipinjam saat ini ({$sedangDipinjam} {$item->satuan}).");
        }

        $oldData = $item->only(['kode_barang', 'nama_barang', 'merk', 'lokasi', 'stok_total', 'stok_tersedia', 'satuan', 'kondisi']);
        $validated['stok_tersedia'] = $newStokTotal - $sedangDipinjam;

        if ($request->hasFile('foto')) {
            if ($item->foto && File::exists(public_path($item->foto))) {
                File::delete(public_path($item->foto));
            }
            $file = $request->file('foto');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $uploadDir = public_path('uploads/items');
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename);
            $validated['foto'] = 'uploads/items/'.$filename;
        }

        $item->update($validated);

        ActivityLog::record(
            'UPDATE',
            'Items',
            "Memperbarui data barang inventaris: {$item->nama_barang} ({$item->kode_barang}).",
            $item,
            [
                'old' => $oldData,
                'new' => $item->only(['kode_barang', 'nama_barang', 'merk', 'lokasi', 'stok_total', 'stok_tersedia', 'satuan', 'kondisi']),
            ]
        );

        return redirect()->route('items.index')
            ->with('success', 'Data barang "'.$item->nama_barang.'" berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $hasActiveLoans = $item->loans()
            ->whereIn('status', ['menunggu_persetujuan', 'dipinjam', 'menunggu_pengembalian'])
            ->exists();

        if ($hasActiveLoans) {
            return back()->with('error', 'Barang "'.$item->nama_barang.'" tidak dapat dihapus karena sedang dalam proses peminjaman aktif.');
        }

        $deletedData = $item->only(['id', 'kode_barang', 'nama_barang', 'lokasi', 'stok_total', 'kondisi']);
        $nama = $item->nama_barang;
        $item->delete();

        ActivityLog::record(
            'DELETE',
            'Items',
            "Menghapus data barang inventaris: {$deletedData['nama_barang']} ({$deletedData['kode_barang']}).",
            null,
            ['deleted' => $deletedData]
        );

        return redirect()->route('items.index')
            ->with('success', 'Data barang "'.$nama.'" berhasil dihapus.');
    }
}
