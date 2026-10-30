<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $module = $request->query('module');
        $action = $request->query('action');
        $date = $request->query('date');

        $logs = ActivityLog::with('user')
            ->when($module, fn ($q) => $q->where('module', $module))
            ->when($action, fn ($q) => $q->where('action', $action))
            ->when($date, fn ($q) => $q->whereDate('created_at', $date))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('nama_user', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $modules = ['Auth', 'Profile', 'Categories', 'Items', 'Loans', 'ReturnLogs', 'Users', 'Reports'];
        $actions = [
            'LOGIN',
            'LOGIN_FAILED',
            'REGISTER',
            'LOGOUT',
            'CREATE',
            'UPDATE',
            'DELETE',
            'LOAN_REQUEST',
            'LOAN_APPROVE',
            'LOAN_REJECT',
            'RETURN_REQUEST',
            'RETURN_CONFIRM',
            'EXPORT',
        ];

        return view('activity_logs.index', compact(
            'logs',
            'modules',
            'actions',
            'search',
            'module',
            'action',
            'date'
        ));
    }
}
