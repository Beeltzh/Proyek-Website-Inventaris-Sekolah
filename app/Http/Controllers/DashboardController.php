<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $stats = [
                'total_jenis_barang' => Item::count(),
                'total_unit_barang' => (int) Item::sum('stok_total'),
                'stok_tersedia' => (int) Item::sum('stok_tersedia'),
                'total_kategori' => Category::count(),
                'menunggu_persetujuan' => Loan::where('status', 'menunggu_persetujuan')->count(),
                'sedang_dipinjam' => Loan::whereIn('status', ['dipinjam', 'menunggu_pengembalian'])->count(),
                'menunggu_pengembalian' => Loan::where('status', 'menunggu_pengembalian')->count(),
                'terlambat' => Loan::whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                    ->whereDate('rencana_kembali', '<', now()->toDateString())
                    ->count(),
                'total_pengembalian' => ReturnLog::whereNotNull('tanggal_dikembalikan')->count(),
                'total_user' => User::where('role', 'user')->count(),
            ];

            $pendingRequests = Loan::with(['user', 'item.category', 'returnLog'])
                ->whereIn('status', ['menunggu_persetujuan', 'menunggu_pengembalian'])
                ->latest()
                ->take(8)
                ->get();

            $recentLoans = Loan::with(['user', 'item', 'returnLog'])
                ->latest()
                ->take(6)
                ->get();

            $recentActivities = ActivityLog::with('user')
                ->latest()
                ->take(6)
                ->get();

            $kondisiStats = [
                'baik' => Item::where('kondisi', 'baik')->count(),
                'rusak_ringan' => Item::where('kondisi', 'rusak_ringan')->count(),
                'rusak_berat' => Item::where('kondisi', 'rusak_berat')->count(),
            ];

            return view('dashboard.admin', compact(
                'stats',
                'pendingRequests',
                'recentLoans',
                'recentActivities',
                'kondisiStats'
            ));
        }

        // User Dashboard (Guru / Siswa)
        $stats = [
            'menunggu_persetujuan' => Loan::where('user_id', $user->id)
                ->where('status', 'menunggu_persetujuan')
                ->count(),
            'sedang_dipinjam' => Loan::where('user_id', $user->id)
                ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                ->count(),
            'dikembalikan' => Loan::where('user_id', $user->id)
                ->where('status', 'dikembalikan')
                ->count(),
            'terlambat' => Loan::where('user_id', $user->id)
                ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                ->whereDate('rencana_kembali', '<', now()->toDateString())
                ->count(),
        ];

        $activeLoans = Loan::with(['item.category', 'returnLog'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['menunggu_persetujuan', 'dipinjam', 'menunggu_pengembalian'])
            ->latest()
            ->get();

        $availableItems = Item::with('category')
            ->where('stok_tersedia', '>', 0)
            ->where('kondisi', '!=', 'rusak_berat')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.user', compact('stats', 'activeLoans', 'availableItems'));
    }
}
