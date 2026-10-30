<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Inventaris Sekolah</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            margin: 24px;
            font-size: 12px;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #111827;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-kop h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header-kop h2 {
            margin: 4px 0;
            font-size: 14px;
        }
        .header-kop p {
            margin: 2px 0;
            font-size: 11px;
            color: #4b5563;
        }
        .report-title {
            text-align: center;
            margin-bottom: 16px;
        }
        .report-title h3 {
            margin: 0 0 4px 0;
            font-size: 14px;
            text-decoration: underline;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th, td {
            border: 1px solid #374151;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .signature-box {
            margin-top: 36px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-inner {
            width: 240px;
            text-align: center;
        }
        .no-print {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
        }
        .btn-print {
            padding: 8px 14px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Sekarang / Simpan PDF</button>
        <button class="btn-print" style="background:#475569;" onclick="window.close()">Tutup</button>
    </div>

    <div class="header-kop">
        <h1>PEMERINTAH PROVINSI — DINAS PENDIDIKAN</h1>
        <h2>UNIT PENGELOLA SARANA & PRASARANA INVENTARIS SEKOLAH</h2>
        <p>Jl. Pendidikan Raya No. 10, Telepon (021) 555-0192 • Email: sarpras@sekolah.sch.id</p>
    </div>

    @if(in_array($type, ['items', 'inventaris'], true))
        <div class="report-title">
            <h3>LAPORAN DATA BARANG INVENTARIS SEKOLAH</h3>
            <p>Tanggal Cetak: {{ format_datetime(now()) }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Merk</th>
                    <th>Lokasi</th>
                    <th>Stok Total</th>
                    <th>Tersedia</th>
                    <th>Dipinjam</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemsSummary as $i => $item)
                    <tr>
                        <td>{{ format_number($i + 1) }}</td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->category->nama_kategori ?? '-' }}</td>
                        <td>{{ $item->merk ?: '-' }}</td>
                        <td>{{ $item->lokasi }}</td>
                        <td>{{ format_number($item->stok_total) }} {{ $item->satuan }}</td>
                        <td>{{ format_number($item->stok_tersedia) }} {{ $item->satuan }}</td>
                        <td>{{ format_number($item->sedang_dipinjam) }} {{ $item->satuan }}</td>
                        <td>{{ $item->kondisi_label }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="report-title">
            <h3>LAPORAN PEMINJAMAN & PENGEMBALIAN BARANG INVENTARIS</h3>
            <p>Periode: {{ format_date($startDate) }} s/d {{ format_date($endDate) }} • Dicetak pada: {{ format_datetime(now()) }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Pinjam</th>
                    <th>Nama Peminjam</th>
                    <th>Status/Kelas</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Tgl Dikembalikan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $i => $b)
                    <tr>
                        <td>{{ format_number($i + 1) }}</td>
                        <td>{{ $b->kode_peminjaman }}</td>
                        <td>{{ $b->user->name ?? '-' }}</td>
                        <td>{{ $b->user->user_type ?? '-' }} ({{ $b->user->kelas_jabatan ?? '-' }})</td>
                        <td>{{ $b->item->nama_barang ?? '-' }}</td>
                        <td>{{ format_number($b->jumlah) }} {{ $b->item->satuan ?? 'Unit' }}</td>
                        <td>{{ format_date($b->tanggal_pinjam) }}</td>
                        <td>{{ format_date($b->rencana_kembali) }}</td>
                        <td>{{ $b->returnLog?->tanggal_dikembalikan ? format_datetime($b->returnLog->tanggal_dikembalikan) . ' (' . $b->returnLog->kondisi_label . ')' : '-' }}</td>
                        <td>{{ $b->status_label }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;">Tidak ada data peminjaman pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="signature-box">
        <div class="signature-inner">
            <p>Mengetahui,<br>Pengelola Inventaris Sekolah</p>
            <br><br><br>
            <p><strong><u>{{ auth()->user()->name }}</u></strong><br>NIP. {{ auth()->user()->nomor_induk ?: '-' }}</p>
        </div>
    </div>
</body>
</html>
