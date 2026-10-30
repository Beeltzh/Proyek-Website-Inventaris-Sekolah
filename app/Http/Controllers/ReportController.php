<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $status = $request->query('status');
        $categoryId = $request->query('category_id');

        $loans = Loan::with(['user', 'item.category', 'approver', 'returnLog.receiver'])
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_pinjam', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('tanggal_pinjam', '<=', $endDate))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->whereHas('item', fn ($iq) => $iq->where('category_id', $categoryId)))
            ->orderByDesc('tanggal_pinjam')
            ->paginate(10, ['*'], 'loans_page')
            ->withQueryString();

        $itemsSummary = Item::with('category')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('kode_barang')
            ->paginate(10, ['*'], 'items_page')
            ->withQueryString();

        $categories = Category::orderBy('nama_kategori')->get();

        return view('reports.index', compact(
            'loans',
            'itemsSummary',
            'categories',
            'startDate',
            'endDate',
            'status',
            'categoryId'
        ));
    }

    public function print(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $type = $request->query('type', 'loans');

        $loans = Loan::with(['user', 'item.category', 'approver', 'returnLog.receiver'])
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_pinjam', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('tanggal_pinjam', '<=', $endDate))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->whereHas('item', fn ($iq) => $iq->where('category_id', $categoryId)))
            ->orderByDesc('tanggal_pinjam')
            ->get();

        $itemsSummary = Item::with('category')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('kode_barang')
            ->get();

        return view('reports.print', compact(
            'loans',
            'itemsSummary',
            'startDate',
            'endDate',
            'status',
            'categoryId',
            'type'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->query('type', 'loans');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $status = $request->query('status');
        $categoryId = $request->query('category_id');

        ActivityLog::record(
            'EXPORT',
            'Reports',
            'Admin mengunduh laporan CSV untuk jenis laporan: '.strtoupper($type).'.',
            null,
            [
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
            ]
        );

        if (in_array($type, ['items', 'inventaris'], true)) {
            $items = Item::with('category')
                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                ->orderBy('kode_barang')
                ->get();

            $filename = 'Laporan_Data_Barang_Inventaris_'.now()->format('Ymd_His').'.csv';

            return response()->streamDownload(function () use ($items) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, [
                    'No',
                    'Kode Barang',
                    'Nama Barang',
                    'Kategori',
                    'Merk',
                    'Lokasi Penyimpanan',
                    'Stok Total',
                    'Stok Tersedia',
                    'Sedang Dipinjam',
                    'Satuan',
                    'Kondisi',
                ]);

                foreach ($items as $idx => $item) {
                    fputcsv($handle, [
                        $idx + 1,
                        $item->kode_barang,
                        $item->nama_barang,
                        $item->category->nama_kategori ?? '-',
                        $item->merk ?? '-',
                        $item->lokasi,
                        format_number($item->stok_total),
                        format_number($item->stok_tersedia),
                        format_number($item->sedang_dipinjam),
                        $item->satuan,
                        $item->kondisi_label,
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $loans = Loan::with(['user', 'item.category', 'approver', 'returnLog.receiver'])
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_pinjam', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('tanggal_pinjam', '<=', $endDate))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->whereHas('item', fn ($iq) => $iq->where('category_id', $categoryId)))
            ->orderByDesc('tanggal_pinjam')
            ->get();

        $filename = 'Laporan_Peminjaman_Pengembalian_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($loans) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Kode Peminjaman',
                'Nama Peminjam',
                'Tipe Peminjam',
                'NIP/NISN',
                'Kelas/Jabatan',
                'Kode Barang',
                'Nama Barang',
                'Kategori',
                'Jumlah',
                'Tanggal Pinjam',
                'Rencana Kembali',
                'Tanggal Dikembalikan',
                'Keterlambatan (Hari)',
                'Status',
                'Kondisi Saat Kembali',
                'Keperluan',
                'Disetujui Oleh',
                'Diterima Kembali Oleh',
            ]);

            foreach ($loans as $idx => $loan) {
                fputcsv($handle, [
                    $idx + 1,
                    $loan->kode_peminjaman,
                    $loan->user->name ?? '-',
                    $loan->user->user_type ?? '-',
                    $loan->user->nomor_induk ?? '-',
                    $loan->user->kelas_jabatan ?? '-',
                    $loan->item->kode_barang ?? '-',
                    $loan->item->nama_barang ?? '-',
                    $loan->item->category->nama_kategori ?? '-',
                    format_number($loan->jumlah).' '.($loan->item->satuan ?? 'Unit'),
                    format_date($loan->tanggal_pinjam),
                    format_date($loan->rencana_kembali),
                    $loan->returnLog?->tanggal_dikembalikan ? format_datetime($loan->returnLog->tanggal_dikembalikan) : '-',
                    format_number($loan->returnLog?->terlambat_hari ?? 0),
                    $loan->status_label,
                    $loan->returnLog?->kondisi_label ?? '-',
                    $loan->keperluan,
                    $loan->approver->name ?? '-',
                    $loan->returnLog?->receiver?->name ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
