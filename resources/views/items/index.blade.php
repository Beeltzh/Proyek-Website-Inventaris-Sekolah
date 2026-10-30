@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Data Barang Inventaris' : 'Katalog Barang Inventaris')
@section('page_title', auth()->user()->isAdmin() ? 'Data Barang Inventaris Sekolah' : 'Katalog Barang Inventaris Sekolah')
@section('page_subtitle', auth()->user()->isAdmin() ? 'Kelola pencatatan barang, lokasi penyimpanan, stok, dan kondisi fisik barang' : 'Cari dan ajukan peminjaman barang inventaris untuk kegiatan belajar mengajar')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">{{ auth()->user()->isAdmin() ? 'Data Barang' : 'Katalog Barang' }}</span>
@endsection

@section('content')
    @php $isAdmin = auth()->user()->isAdmin(); @endphp

    <div class="card">
        <div class="card-header">
            <form action="{{ route('items.index') }}" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:230px;" placeholder="Cari nama, kode, merk, lokasi..." value="{{ $search }}" autofocus>

                <select name="category_id" class="form-control" style="width:190px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <select name="condition" class="form-control" style="width:160px;">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ $condition === 'baik' ? 'selected' : '' }}>Kondisi Baik</option>
                    <option value="rusak_ringan" {{ $condition === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ $condition === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>

                <select name="availability" class="form-control" style="width:165px;">
                    <option value="">Semua Stok</option>
                    <option value="available" {{ in_array($availability, ['available', 'tersedia'], true) ? 'selected' : '' }}>Siap Dipinjam</option>
                    <option value="empty" {{ in_array($availability, ['empty', 'habis'], true) ? 'selected' : '' }}>Stok Sedang Habis</option>
                </select>

                <button type="submit" class="btn btn-outline">Filter</button>
                @if($search || $categoryId || $condition || $availability)
                    <a href="{{ route('items.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>

            @if($isAdmin)
                <button type="button" class="btn btn-primary" onclick="document.getElementById('addItemModal').showModal()">
                    + Tambah Data Barang
                </button>
            @endif
        </div>

        <div tabindex="0" role="region" aria-labelledby="items-table-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Nama Barang'; --label-3: 'Kategori'; --label-4: 'Lokasi'; --label-5: 'Stok'; --label-6: 'Kondisi'; --label-7: 'Aksi';">
                <caption id="items-table-caption">Daftar barang inventaris sekolah</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Barang</th>
                        <th scope="col">Nama Barang & Merk</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Lokasi Penyimpanan</th>
                        <th scope="col">Stok Tersedia / Total</th>
                        <th scope="col">Kondisi</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <th scope="row">
                                <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;">{{ $item->kode_barang }}</span>
                            </th>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    @if($item->foto)
                                        <img src="{{ asset($item->foto) }}" alt="{{ $item->nama_barang }}" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid var(--border-color);">
                                    @else
                                        <div style="width:40px; height:40px; border-radius:6px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('items.show', $item) }}" style="font-weight:700; color:var(--text-main); text-decoration:none;">
                                            {{ $item->nama_barang }}
                                        </a>
                                        <div style="font-size:0.76rem; color:var(--text-muted);">
                                            Merk: {{ $item->merk ?: '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $item->category->nama_kategori ?? '-' }}</span>
                            </td>
                            <td>{{ $item->lokasi }}</td>
                            <td>
                                @if($item->stok_tersedia > 0)
                                    <span style="font-weight:700; color:var(--success);">{{ format_number($item->stok_tersedia) }}</span>
                                @else
                                    <span style="font-weight:700; color:var(--danger);">0</span>
                                @endif
                                <span style="color:var(--text-muted);">/ {{ format_number($item->stok_total) }} {{ $item->satuan }}</span>
                            </td>
                            <td>
                                @if($item->kondisi === 'baik')
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>Baik</span></span>
                                @elseif($item->kondisi === 'rusak_ringan')
                                    <span class="badge badge-warning"><span class="badge-dot"></span><span>Rusak Ringan</span></span>
                                @else
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>Rusak Berat</span></span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    <a href="{{ route('items.show', $item) }}" class="btn btn-outline btn-sm">Detail</a>

                                    @if($item->stok_tersedia > 0 && $item->kondisi !== 'rusak_berat')
                                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('borrowItemModal-{{ $item->id }}').showModal()">
                                            Pinjam
                                        </button>
                                    @endif

                                    @if($isAdmin)
                                        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('editItemModal-{{ $item->id }}').showModal()">
                                            Edit
                                        </button>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus barang {{ $item->nama_barang }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Barang">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 2.25rem; color:var(--text-muted);">
                                Tidak ada data barang yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="pagination-wrap">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Barang (Khusus Admin) -->
    @if($isAdmin)
        <dialog id="addItemModal" closedby="any" aria-labelledby="addItemTitle" class="modal-dialog">
            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h3 id="addItemTitle">Tambah Data Barang Inventaris</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kode_barang">Kode Barang <span class="req">*</span></label>
                            <input type="text" id="kode_barang" name="kode_barang" class="form-control" placeholder="Contoh: INV-ELK-005" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Kategori Barang <span class="req">*</span></label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama_barang">Nama Barang <span class="req">*</span></label>
                        <input type="text" id="nama_barang" name="nama_barang" class="form-control" placeholder="Contoh: Proyektor Epson EB-X500" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="merk">Merk / Brand</label>
                            <input type="text" id="merk" name="merk" class="form-control" placeholder="Contoh: Epson">
                        </div>
                        <div class="form-group">
                            <label for="lokasi">Lokasi Penyimpanan <span class="req">*</span></label>
                            <input type="text" id="lokasi" name="lokasi" class="form-control" placeholder="Contoh: Ruang Sarpras Lt. 1" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stok_total">Jumlah Stok Total <span class="req">*</span></label>
                            <input type="number" id="stok_total" name="stok_total" class="form-control" value="1" min="0" placeholder="Masukkan jumlah stok" required>
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan <span class="req">*</span></label>
                            <select id="satuan" name="satuan" class="form-control" required>
                                <option value="Unit">Unit</option>
                                <option value="Buah">Buah</option>
                                <option value="Set">Set</option>
                                <option value="Pasang">Pasang</option>
                                <option value="Pcs">Pcs</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kondisi">Kondisi Fisik <span class="req">*</span></label>
                            <select id="kondisi" name="kondisi" class="form-control" required>
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto Barang (Opsional, Maks 2MB)</label>
                        <input type="file" id="foto" name="foto" class="form-control" accept="image/*" placeholder="Pilih file gambar barang">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Spesifikasi / Keterangan Tambahan</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Kelengkapan barang, nomor seri, atau keterangan lainnya..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </div>
            </form>
        </dialog>
    @endif

    <!-- Modal Pinjam & Modal Edit untuk Setiap Barang -->
    @foreach($items as $item)
        @if($item->stok_tersedia > 0 && $item->kondisi !== 'rusak_berat')
            <dialog id="borrowItemModal-{{ $item->id }}" closedby="any" aria-labelledby="borrowItemTitle-{{ $item->id }}" class="modal-dialog">
                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    @if($isAdmin)
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    @endif
                    <div class="modal-header">
                        <h3 id="borrowItemTitle-{{ $item->id }}">Pinjam: {{ $item->nama_barang }}</h3>
                        <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                    </div>
                    <div class="modal-body">
                        <div style="padding:0.75rem 1rem; background:var(--primary-soft); border-radius:8px; margin-bottom:1rem;">
                            <strong>{{ $item->nama_barang }}</strong> ({{ $item->kode_barang }})<br>
                            <small>Stok tersedia: <strong>{{ format_number($item->stok_tersedia) }} {{ $item->satuan }}</strong> • Lokasi: {{ $item->lokasi }}</small>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_pinjam_{{ $item->id }}">Jumlah ({{ $item->satuan }}) <span class="req">*</span></label>
                            <input type="number" id="jumlah_pinjam_{{ $item->id }}" name="jumlah" class="form-control" value="1" min="1" max="{{ $item->stok_tersedia }}" placeholder="Masukkan jumlah unit" required autofocus>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tgl_pinjam_{{ $item->id }}">Tanggal Pinjam <span class="req">*</span></label>
                                <input type="date" id="tgl_pinjam_{{ $item->id }}" name="tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" placeholder="Pilih tanggal pinjam" required>
                            </div>
                            <div class="form-group">
                                <label for="rencana_kembali_{{ $item->id }}">Rencana Tanggal Kembali <span class="req">*</span></label>
                                <input type="date" id="rencana_kembali_{{ $item->id }}" name="rencana_kembali" class="form-control" value="{{ date('Y-m-d', strtotime('+2 days')) }}" placeholder="Pilih tanggal rencana kembali" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keperluan_{{ $item->id }}">Keperluan Peminjaman <span class="req">*</span></label>
                            <textarea id="keperluan_{{ $item->id }}" name="keperluan" class="form-control" rows="3" placeholder="Tuliskan keperluan peminjaman beserta kelas/kegiatan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isAdmin ? 'Catat Peminjaman' : 'Ajukan Peminjaman' }}
                        </button>
                    </div>
                </form>
            </dialog>
        @endif

        @if($isAdmin)
            <dialog id="editItemModal-{{ $item->id }}" closedby="any" aria-labelledby="editItemTitle-{{ $item->id }}" class="modal-dialog">
                <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h3 id="editItemTitle-{{ $item->id }}">Edit Barang: {{ $item->nama_barang }}</h3>
                        <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kode_barang_{{ $item->id }}">Kode Barang <span class="req">*</span></label>
                                <input type="text" id="kode_barang_{{ $item->id }}" name="kode_barang" class="form-control" value="{{ $item->kode_barang }}" placeholder="Contoh: INV-ELK-001" required autofocus>
                            </div>
                            <div class="form-group">
                                <label for="category_id_{{ $item->id }}">Kategori Barang <span class="req">*</span></label>
                                <select id="category_id_{{ $item->id }}" name="category_id" class="form-control" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $item->category_id === $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nama_barang_{{ $item->id }}">Nama Barang <span class="req">*</span></label>
                            <input type="text" id="nama_barang_{{ $item->id }}" name="nama_barang" class="form-control" value="{{ $item->nama_barang }}" placeholder="Masukkan nama barang" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="merk_{{ $item->id }}">Merk / Brand</label>
                                <input type="text" id="merk_{{ $item->id }}" name="merk" class="form-control" value="{{ $item->merk }}" placeholder="Masukkan merk barang">
                            </div>
                            <div class="form-group">
                                <label for="lokasi_{{ $item->id }}">Lokasi Penyimpanan <span class="req">*</span></label>
                                <input type="text" id="lokasi_{{ $item->id }}" name="lokasi" class="form-control" value="{{ $item->lokasi }}" placeholder="Masukkan lokasi penyimpanan" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="stok_total_{{ $item->id }}">Stok Total (Dipinjam: {{ format_number($item->sedang_dipinjam) }}) <span class="req">*</span></label>
                                <input type="number" id="stok_total_{{ $item->id }}" name="stok_total" class="form-control" value="{{ $item->stok_total }}" min="{{ $item->sedang_dipinjam }}" placeholder="Masukkan stok total" required>
                            </div>
                            <div class="form-group">
                                <label for="satuan_{{ $item->id }}">Satuan <span class="req">*</span></label>
                                <input type="text" id="satuan_{{ $item->id }}" name="satuan" class="form-control" value="{{ $item->satuan }}" placeholder="Contoh: Unit / Buah" required>
                            </div>
                            <div class="form-group">
                                <label for="kondisi_{{ $item->id }}">Kondisi <span class="req">*</span></label>
                                <select id="kondisi_{{ $item->id }}" name="kondisi" class="form-control" required>
                                    <option value="baik" {{ $item->kondisi === 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="rusak_ringan" {{ $item->kondisi === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="rusak_berat" {{ $item->kondisi === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="foto_{{ $item->id }}">Ganti Foto Barang (Opsional)</label>
                            <input type="file" id="foto_{{ $item->id }}" name="foto" class="form-control" accept="image/*" placeholder="Pilih foto baru">
                        </div>

                        <div class="form-group">
                            <label for="deskripsi_{{ $item->id }}">Spesifikasi / Keterangan</label>
                            <textarea id="deskripsi_{{ $item->id }}" name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi atau kelengkapan barang...">{{ $item->deskripsi }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </dialog>
        @endif
    @endforeach
@endsection
