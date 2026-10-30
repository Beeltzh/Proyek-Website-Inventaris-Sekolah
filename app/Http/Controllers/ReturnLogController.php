<?php

namespace App\Http\Controllers;

use App\Models\ReturnLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->query('search', ''));
        $condition = $request->query('condition', $request->query('kondisi'));
        $punctuality = $request->query('punctuality', $request->query('ketepatan'));

        $returnLogs = ReturnLog::with(['loan.user', 'loan.item.category', 'receiver'])
            ->whereNotNull('tanggal_dikembalikan')
            ->when(! $user->isAdmin(), function ($q) use ($user) {
                $q->whereHas('loan', fn ($lq) => $lq->where('user_id', $user->id));
            })
            ->when($condition, fn ($q) => $q->where('kondisi_saat_kembali', $condition))
            ->when(in_array($punctuality, ['late', 'terlambat'], true), fn ($q) => $q->where('terlambat_hari', '>', 0))
            ->when(in_array($punctuality, ['on_time', 'tepat_waktu'], true), fn ($q) => $q->where('terlambat_hari', '=', 0))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('catatan_pengembalian', 'like', "%{$search}%")
                        ->orWhereHas('loan', function ($lq) use ($search) {
                            $lq->where('kode_peminjaman', 'like', "%{$search}%")
                                ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('item', fn ($iq) => $iq->where('nama_barang', 'like', "%{$search}%")->orWhere('kode_barang', 'like', "%{$search}%"));
                        });
                });
            })
            ->orderByDesc('tanggal_dikembalikan')
            ->paginate(10)
            ->withQueryString();

        return view('return_logs.index', compact('returnLogs', 'search', 'condition', 'punctuality'));
    }
}
