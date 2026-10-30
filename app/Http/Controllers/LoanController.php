<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveLoanRequest;
use App\Http\Requests\ConfirmReturnRequest;
use App\Http\Requests\RejectLoanRequest;
use App\Http\Requests\StoreLoanRequest;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = $request->query('status');
        $search = trim((string) $request->query('search', ''));

        $loans = Loan::with(['user', 'item.category', 'approver', 'returnLog.receiver'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when(in_array($status, ['overdue', 'terlambat'], true), function ($q) {
                $q->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                    ->whereDate('rencana_kembali', '<', now()->toDateString());
            })
            ->when($status && ! in_array($status, ['overdue', 'terlambat'], true), fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_peminjaman', 'like', "%{$search}%")
                        ->orWhere('keperluan', 'like', "%{$search}%")
                        ->orWhereHas('item', fn ($iq) => $iq->where('nama_barang', 'like', "%{$search}%")->orWhere('kode_barang', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('nomor_induk', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $availableItems = Item::with('category')
            ->where('stok_tersedia', '>', 0)
            ->where('kondisi', '!=', 'rusak_berat')
            ->orderBy('nama_barang')
            ->get();

        $users = $user->isAdmin()
            ? User::orderBy('name')->get()
            : collect([$user]);

        return view('loans.index', compact('loans', 'availableItems', 'users', 'status', 'search'));
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $actor = $request->user();
        $validated = $request->validated();
        $borrowerId = $actor->isAdmin() ? (int) $validated['user_id'] : $actor->id;

        return DB::transaction(function () use ($actor, $validated, $borrowerId) {
            /** @var Item $item */
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);
            $borrower = User::findOrFail($borrowerId);

            if ($item->kondisi === 'rusak_berat') {
                return back()->with('error', 'Barang "'.$item->nama_barang.'" sedang dalam kondisi Rusak Berat dan tidak dapat dipinjam.');
            }

            if ($item->stok_tersedia < (int) $validated['jumlah']) {
                return back()->with('error', "Stok tersedia untuk \"{$item->nama_barang}\" tidak mencukupi (Tersedia: {$item->stok_tersedia} {$item->satuan}).");
            }

            $datePrefix = 'PMJ-'.now()->format('Ymd').'-';
            $lastLoan = Loan::withTrashed()
                ->where('kode_peminjaman', 'like', $datePrefix.'%')
                ->orderByDesc('id')
                ->first();
            $nextNumber = 1;
            if ($lastLoan && preg_match('/-(\d+)$/', $lastLoan->kode_peminjaman, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1;
            }
            $kodePeminjaman = $datePrefix.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            $status = $actor->isAdmin() ? 'dipinjam' : 'menunggu_persetujuan';

            if ($actor->isAdmin()) {
                $item->decrement('stok_tersedia', (int) $validated['jumlah']);
            }

            $loan = Loan::create([
                'kode_peminjaman' => $kodePeminjaman,
                'user_id' => $borrowerId,
                'item_id' => $item->id,
                'approved_by' => $actor->isAdmin() ? $actor->id : null,
                'jumlah' => (int) $validated['jumlah'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'rencana_kembali' => $validated['rencana_kembali'],
                'keperluan' => $validated['keperluan'],
                'status' => $status,
                'catatan_admin' => $actor->isAdmin() ? 'Dicatat dan disetujui langsung oleh Admin.' : null,
            ]);

            ActivityLog::record(
                $actor->isAdmin() ? 'CREATE' : 'LOAN_REQUEST',
                'Loans',
                $actor->isAdmin()
                    ? "Admin mencatat peminjaman langsung {$kodePeminjaman} untuk {$borrower->name}: {$loan->jumlah} {$item->satuan} {$item->nama_barang}."
                    : "Pengajuan peminjaman baru {$kodePeminjaman} oleh {$borrower->name}: {$loan->jumlah} {$item->satuan} {$item->nama_barang}.",
                $loan,
                [
                    'kode_peminjaman' => $kodePeminjaman,
                    'peminjam' => $borrower->name,
                    'barang' => $item->nama_barang,
                    'jumlah' => $loan->jumlah,
                    'status' => $status,
                ]
            );

            $msg = $actor->isAdmin()
                ? "Peminjaman {$kodePeminjaman} berhasil dicatat dan stok barang otomatis diperbarui."
                : "Pengajuan peminjaman ({$kodePeminjaman}) berhasil dikirim dan menunggu persetujuan Admin.";

            return redirect()->route('loans.index')->with('success', $msg);
        });
    }

    public function approve(ApproveLoanRequest $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'menunggu_persetujuan') {
            return back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        $validated = $request->validated();

        return DB::transaction(function () use ($request, $loan, $validated) {
            /** @var Item $item */
            $item = Item::lockForUpdate()->findOrFail($loan->item_id);

            if ($item->stok_tersedia < $loan->jumlah) {
                return back()->with('error', "Gagal menyetujui: Stok barang \"{$item->nama_barang}\" tidak mencukupi (Tersedia: {$item->stok_tersedia} {$item->satuan}, Diminta: {$loan->jumlah} {$item->satuan}).");
            }

            $oldStok = $item->stok_tersedia;
            $item->decrement('stok_tersedia', $loan->jumlah);

            $loan->update([
                'status' => 'dipinjam',
                'approved_by' => $request->user()->id,
                'catatan_admin' => $validated['catatan_admin'] ?? $loan->catatan_admin,
            ]);

            ActivityLog::record(
                'LOAN_APPROVE',
                'Loans',
                "Admin menyetujui peminjaman {$loan->kode_peminjaman} ({$loan->jumlah} {$item->satuan} {$item->nama_barang}) oleh {$loan->user->name}.",
                $loan,
                [
                    'old_status' => 'menunggu_persetujuan',
                    'new_status' => 'dipinjam',
                    'stok_sebelum' => $oldStok,
                    'stok_sesudah' => $item->fresh()->stok_tersedia,
                ]
            );

            return back()->with('success', "Peminjaman {$loan->kode_peminjaman} oleh {$loan->user->name} telah disetujui.");
        });
    }

    public function reject(RejectLoanRequest $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'menunggu_persetujuan') {
            return back()->with('error', 'Peminjaman ini tidak dalam status menunggu persetujuan.');
        }

        $validated = $request->validated();

        $loan->update([
            'status' => 'ditolak',
            'approved_by' => $request->user()->id,
            'catatan_admin' => $validated['catatan_admin'],
        ]);

        ActivityLog::record(
            'LOAN_REJECT',
            'Loans',
            "Admin menolak pengajuan peminjaman {$loan->kode_peminjaman} oleh {$loan->user->name}. Alasan: {$validated['catatan_admin']}.",
            $loan,
            [
                'old_status' => 'menunggu_persetujuan',
                'new_status' => 'ditolak',
                'alasan' => $validated['catatan_admin'],
            ]
        );

        return back()->with('success', "Pengajuan peminjaman {$loan->kode_peminjaman} telah ditolak.");
    }

    public function requestReturn(Request $request, Loan $loan): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && $loan->user_id !== $user->id) {
            abort(403);
        }

        if ($loan->status !== 'dipinjam') {
            return back()->with('error', 'Hanya barang yang berstatus sedang dipinjam yang dapat diajukan pengembaliannya.');
        }

        DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => 'menunggu_pengembalian',
            ]);

            ActivityLog::record(
                'RETURN_REQUEST',
                'Loans',
                "Pengajuan pengembalian barang untuk transaksi {$loan->kode_peminjaman} ({$loan->item->nama_barang}).",
                $loan,
                [
                    'kode_peminjaman' => $loan->kode_peminjaman,
                    'old_status' => 'dipinjam',
                    'new_status' => 'menunggu_pengembalian',
                ]
            );
        });

        return back()->with('success', "Pengajuan pengembalian untuk {$loan->kode_peminjaman} berhasil dikirim. Silakan serahkan barang kepada pengelola inventaris (Admin).");
    }

    public function confirmReturn(ConfirmReturnRequest $request, Loan $loan): RedirectResponse
    {
        if (! in_array($loan->status, ['dipinjam', 'menunggu_pengembalian'], true)) {
            return back()->with('error', 'Peminjaman ini tidak dalam status aktif untuk dikembalikan.');
        }

        $validated = $request->validated();

        return DB::transaction(function () use ($request, $loan, $validated) {
            /** @var Item $item */
            $item = Item::lockForUpdate()->findOrFail($loan->item_id);

            $oldStok = $item->stok_tersedia;
            $newAvailable = min($item->stok_total, $item->stok_tersedia + $loan->jumlah);
            $item->stok_tersedia = $newAvailable;
            $item->save();

            $now = now();
            $lateDays = $loan->calculateLateDays($now);
            $existingLog = $loan->returnLog;

            $returnLog = ReturnLog::updateOrCreate(
                ['loan_id' => $loan->id],
                [
                    'received_by' => $request->user()->id,
                    'tanggal_pengajuan' => $existingLog?->tanggal_pengajuan ?? $now,
                    'tanggal_dikembalikan' => $now,
                    'jumlah_kembali' => $loan->jumlah,
                    'kondisi_saat_kembali' => $validated['kondisi_saat_kembali'],
                    'terlambat_hari' => $lateDays,
                    'catatan_pengembalian' => $validated['catatan_pengembalian'] ?? null,
                ]
            );

            $oldStatus = $loan->status;
            $loan->update([
                'status' => 'dikembalikan',
                'approved_by' => $loan->approved_by ?? $request->user()->id,
            ]);

            ActivityLog::record(
                'RETURN_CONFIRM',
                'ReturnLogs',
                "Admin memverifikasi pengembalian barang {$item->nama_barang} ({$loan->kode_peminjaman}) dari {$loan->user->name} dengan kondisi: {$returnLog->kondisi_label}.",
                $returnLog,
                [
                    'kode_peminjaman' => $loan->kode_peminjaman,
                    'old_status' => $oldStatus,
                    'new_status' => 'dikembalikan',
                    'kondisi_saat_kembali' => $validated['kondisi_saat_kembali'],
                    'terlambat_hari' => $lateDays,
                    'stok_sebelum' => $oldStok,
                    'stok_sesudah' => $newAvailable,
                ]
            );

            return back()->with('success', "Pengembalian barang {$item->nama_barang} ({$loan->kode_peminjaman}) berhasil dicatat ke dalam Return Logs dan stok telah dikembalikan.");
        });
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        return DB::transaction(function () use ($loan) {
            if (in_array($loan->status, ['dipinjam', 'menunggu_pengembalian'], true)) {
                /** @var Item|null $item */
                $item = Item::lockForUpdate()->find($loan->item_id);
                if ($item) {
                    $item->increment('stok_tersedia', $loan->jumlah);
                }
            }

            $kode = $loan->kode_peminjaman;
            $loan->delete();

            ActivityLog::record(
                'DELETE',
                'Loans',
                "Admin menghapus data peminjaman {$kode}.",
                null,
                ['kode_peminjaman' => $kode]
            );

            return back()->with('success', "Data peminjaman {$kode} berhasil dihapus.");
        });
    }
}
