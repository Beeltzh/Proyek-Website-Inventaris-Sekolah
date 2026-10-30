@extends('layouts.app')

@section('title', 'Kategori Barang')
@section('page_title', 'Manajemen Kategori Barang')
@section('page_subtitle', 'Kelola pengelompokan jenis barang inventaris sekolah')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Kategori Barang</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ route('categories.index') }}" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:280px;" placeholder="Cari kode atau nama kategori..." value="{{ $search }}" autofocus>
                <button type="submit" class="btn btn-outline">Cari</button>
                @if($search)
                    <a href="{{ route('categories.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>

            <button type="button" class="btn btn-primary" onclick="document.getElementById('addCategoryModal').showModal()">
                + Tambah Kategori
            </button>
        </div>

        <div tabindex="0" role="region" aria-labelledby="categories-table-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Nama Kategori'; --label-3: 'Deskripsi'; --label-4: 'Jumlah Barang'; --label-5: 'Total Stok'; --label-6: 'Aksi';">
                <caption id="categories-table-caption">Daftar kategori barang inventaris sekolah</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Kategori</th>
                        <th scope="col">Nama Kategori</th>
                        <th scope="col">Deskripsi</th>
                        <th scope="col">Jenis Barang</th>
                        <th scope="col">Stok Tersedia / Total</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <th scope="row">
                                <span class="badge badge-primary">{{ $category->kode_kategori }}</span>
                            </th>
                            <td style="font-weight:700;">{{ $category->nama_kategori }}</td>
                            <td style="color:var(--text-muted); max-width:340px;">{{ $category->deskripsi ?: '-' }}</td>
                            <td>
                                <span class="badge badge-neutral">{{ format_number($category->items_count) }} Jenis</span>
                            </td>
                            <td>
                                <strong>{{ format_number((int) $category->items_sum_stok_tersedia) }}</strong> / {{ format_number((int) $category->items_sum_stok_total) }} Unit
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem;">
                                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('editCategoryModal-{{ $category->id }}').showModal()">
                                        Edit
                                    </button>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->nama_kategori }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 2rem; color:var(--text-muted);">
                                Belum ada data kategori yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="pagination-wrap">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Kategori -->
    <dialog id="addCategoryModal" closedby="any" aria-labelledby="addCategoryTitle" class="modal-dialog">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h3 id="addCategoryTitle">Tambah Kategori Barang Baru</h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="kode_kategori">Kode Kategori <span class="req">*</span></label>
                    <input type="text" id="kode_kategori" name="kode_kategori" class="form-control" placeholder="Contoh: KAT-ELK" required autofocus>
                </div>
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori <span class="req">*</span></label>
                    <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" placeholder="Contoh: Elektronik & Multimedia" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi Kategori</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat jenis barang dalam kategori ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Edit Kategori -->
    @foreach($categories as $category)
        <dialog id="editCategoryModal-{{ $category->id }}" closedby="any" aria-labelledby="editCategoryTitle-{{ $category->id }}" class="modal-dialog">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h3 id="editCategoryTitle-{{ $category->id }}">Edit Kategori: {{ $category->nama_kategori }}</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kode_kategori_{{ $category->id }}">Kode Kategori <span class="req">*</span></label>
                        <input type="text" id="kode_kategori_{{ $category->id }}" name="kode_kategori" class="form-control" value="{{ $category->kode_kategori }}" placeholder="Contoh: KAT-ELK" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="nama_kategori_{{ $category->id }}">Nama Kategori <span class="req">*</span></label>
                        <input type="text" id="nama_kategori_{{ $category->id }}" name="nama_kategori" class="form-control" value="{{ $category->nama_kategori }}" placeholder="Masukkan nama kategori" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_{{ $category->id }}">Deskripsi Kategori</label>
                        <textarea id="deskripsi_{{ $category->id }}" name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi singkat kategori...">{{ $category->deskripsi }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </dialog>
    @endforeach
@endsection
