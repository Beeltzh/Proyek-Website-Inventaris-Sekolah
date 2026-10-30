<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Inventaris Sekolah') — SMAN 1 Nusantara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-sidebar: #1e293b;
            --bg-sidebar-header: #0f172a;
            --bg-sidebar-hover: rgba(255, 255, 255, 0.08);
            --sidebar-border: rgba(255, 255, 255, 0.08);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --border-subtle: #f1f5f9;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-soft: #eff6ff;
            --primary-border: #bfdbfe;
            --success: #059669;
            --success-soft: #ecfdf5;
            --success-border: #a7f3d0;
            --warning: #d97706;
            --warning-soft: #fffbeb;
            --warning-border: #fde68a;
            --danger: #dc2626;
            --danger-soft: #fef2f2;
            --danger-border: #fecaca;
            --info: #0284c7;
            --info-soft: #f0f9ff;
            --info-border: #bae6fd;
            --radius-sm: 6px;
            --radius: 10px;
            --radius-lg: 14px;
            --shadow-xs: 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 260px;
            background-color: var(--bg-sidebar);
            color: #f8fafc;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            transition: transform 0.25s ease-in-out;
        }

        .sidebar-brand {
            padding: 1.25rem 1.15rem;
            background-color: var(--bg-sidebar-header);
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
        }

        .brand-text h1 {
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.2;
            color: #ffffff;
            letter-spacing: -0.01em;
        }

        .brand-text span {
            font-size: 0.72rem;
            color: #94a3b8;
            display: block;
            margin-top: 1px;
            font-weight: 500;
        }

        .sidebar-section {
            padding: 1.1rem 1.15rem 0.4rem;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 700;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0.35rem 0.65rem;
            flex: 1;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            border-radius: var(--radius-sm);
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.2rem;
            transition: all 0.15s ease;
        }

        .sidebar-link-inner {
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .sidebar-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.85;
            transition: opacity 0.15s ease;
        }

        .sidebar-link:hover {
            background-color: var(--bg-sidebar-hover);
            color: #ffffff;
        }

        .sidebar-link:hover .sidebar-icon {
            opacity: 1;
        }

        .sidebar-link.active {
            background-color: var(--primary);
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-link.active .sidebar-icon {
            opacity: 1;
        }

        .sidebar-badge {
            background: #ef4444;
            color: #fff;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
            line-height: 1;
        }

        .sidebar-footer {
            padding: 1rem 1.15rem;
            border-top: 1px solid var(--sidebar-border);
            background: rgba(15, 23, 42, 0.4);
        }

        .user-mini {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #334155;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-meta {
            overflow: hidden;
        }

        .user-meta strong {
            display: block;
            font-size: 0.84rem;
            color: #f8fafc;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
        }

        .user-meta small {
            display: block;
            font-size: 0.72rem;
            color: #94a3b8;
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.45rem 0.75rem;
            background: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: var(--radius-sm);
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(2px);
            z-index: 35;
        }

        /* Main Area */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 0.85rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 20;
            box-shadow: var(--shadow-xs);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 0.4rem;
            cursor: pointer;
            color: var(--text-main);
            align-items: center;
            justify-content: center;
        }

        .topbar-title h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .topbar-title p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.15rem;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb .sep {
            color: #cbd5e1;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        /* Live Clock Badge */
        .live-clock-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.75rem;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
        }

        .live-clock-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--success);
            display: inline-block;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .role-pill.admin {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .role-pill.user {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .content-container {
            padding: 1.5rem 1.75rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* Cards & Grids */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.15rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 1.25rem 1.35rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.65rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-card .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            line-height: 1.3;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg {
            width: 18px;
            height: 18px;
        }

        .stat-icon.primary { background: var(--primary-soft); color: var(--primary); }
        .stat-icon.warning { background: var(--warning-soft); color: var(--warning); }
        .stat-icon.success { background: var(--success-soft); color: var(--success); }
        .stat-icon.danger { background: var(--danger-soft); color: var(--danger); }
        .stat-icon.info { background: var(--info-soft); color: var(--info); }

        .stat-card .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .stat-card .stat-sub {
            font-size: 0.77rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.85rem;
            background: #ffffff;
        }

        .card-header h3 {
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h3 svg {
            width: 18px;
            height: 18px;
            color: var(--primary);
        }

        .card-body {
            padding: 1.4rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.52rem 0.95rem;
            border-radius: var(--radius-sm);
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
            line-height: 1.25;
            white-space: nowrap;
        }

        .btn svg {
            width: 15px;
            height: 15px;
        }

        .btn-sm {
            padding: 0.36rem 0.68rem;
            font-size: 0.78rem;
            border-radius: var(--radius-sm);
        }

        .btn-sm svg {
            width: 13px;
            height: 13px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-success {
            background-color: var(--success);
            color: #ffffff;
            border-color: var(--success);
        }

        .btn-success:hover {
            background-color: #047857;
            border-color: #047857;
        }

        .btn-warning {
            background-color: var(--warning);
            color: #ffffff;
            border-color: var(--warning);
        }

        .btn-warning:hover {
            background-color: #b45309;
            border-color: #b45309;
        }

        .btn-danger {
            background-color: var(--danger);
            color: #ffffff;
            border-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
        }

        .btn-outline {
            background-color: #ffffff;
            border-color: var(--border-color);
            color: #334155;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: var(--text-main);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.22rem 0.6rem;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 600;
            white-space: nowrap;
            border: 1px solid transparent;
            line-height: 1.25;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .badge-success {
            background: var(--success-soft);
            color: var(--success);
            border-color: var(--success-border);
        }
        .badge-success .badge-dot { background: var(--success); }

        .badge-warning {
            background: var(--warning-soft);
            color: var(--warning);
            border-color: var(--warning-border);
        }
        .badge-warning .badge-dot { background: var(--warning); }

        .badge-danger {
            background: var(--danger-soft);
            color: var(--danger);
            border-color: var(--danger-border);
        }
        .badge-danger .badge-dot { background: var(--danger); }

        .badge-info {
            background: var(--info-soft);
            color: var(--info);
            border-color: var(--info-border);
        }
        .badge-info .badge-dot { background: var(--info); }

        .badge-primary {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: var(--primary-border);
        }
        .badge-primary .badge-dot { background: var(--primary); }

        .badge-neutral {
            background: #f1f5f9;
            color: #475569;
            border-color: #e2e8f0;
        }
        .badge-neutral .badge-dot { background: #94a3b8; }

        /* Forms & Filter Bar */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            align-items: center;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            color: #334155;
        }

        .req {
            color: var(--danger);
            font-weight: 700;
            margin-left: 2px;
        }

        .form-control {
            width: 100%;
            padding: 0.54rem 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: var(--radius-sm);
            font-size: 0.86rem;
            font-family: inherit;
            color: var(--text-main);
            background-color: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        /* Alerts */
        .alert {
            padding: 0.85rem 1.15rem;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            font-size: 0.86rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* Responsive Table */
        .table-wrapper {
            overflow: auto;
            max-inline-size: 100%;
            max-block-size: min(620px, 80vh);
            container-type: inline-size;
        }

        .table-wrapper:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: -2px;
        }

        table.data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.86rem;
        }

        table.data-table caption {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        table.data-table thead {
            position: sticky;
            inset-block-start: 0;
            z-index: 2;
        }

        table.data-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.8rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        table.data-table tbody th,
        table.data-table tbody td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            text-align: left;
            background-color: #ffffff;
            transition: background-color 0.12s ease;
        }

        table.data-table tbody th {
            position: sticky;
            inset-inline-start: 0;
            font-weight: 600;
            color: var(--text-main);
            z-index: 1;
        }

        table.data-table tbody tr:hover td,
        table.data-table tbody tr:hover th {
            background-color: #f8fafc;
        }

        @container (width < 600px) {
            table.data-table,
            table.data-table thead,
            table.data-table tbody,
            table.data-table tr,
            table.data-table th,
            table.data-table td {
                display: block;
            }

            table.data-table thead {
                display: none;
            }

            table.data-table tbody tr {
                margin-block-end: 1rem;
                border: 1px solid var(--border-color);
                border-radius: var(--radius);
                overflow: hidden;
                background: #fff;
            }

            table.data-table tbody th {
                position: sticky;
                inset-block-start: 0;
                z-index: 2;
                background: #f1f5f9;
                border-bottom: 1px solid var(--border-color);
            }

            table.data-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 0.75rem;
                padding: 0.65rem 1rem;
            }

            table.data-table tbody td:nth-child(2)::before {
                content: var(--label-2, "Info") ": ";
                font-weight: 600;
                color: var(--text-muted);
            }
            table.data-table tbody td:nth-child(3)::before {
                content: var(--label-3, "Detail") ": ";
                font-weight: 600;
                color: var(--text-muted);
            }
            table.data-table tbody td:nth-child(4)::before {
                content: var(--label-4, "Status") ": ";
                font-weight: 600;
                color: var(--text-muted);
            }
            table.data-table tbody td:nth-child(5)::before {
                content: var(--label-5, "Keterangan") ": ";
                font-weight: 600;
                color: var(--text-muted);
            }
            table.data-table tbody td:nth-child(6)::before {
                content: var(--label-6, "Aksi") ": ";
                font-weight: 600;
                color: var(--text-muted);
            }
            table.data-table tbody td:nth-child(7)::before {
                content: var(--label-7, "Aksi") ": ";
                font-weight: 600;
                color: var(--text-muted);
            }
        }

        /* Modal Dialog */
        dialog.modal-dialog {
            margin: auto;
            border: none;
            border-radius: var(--radius-lg);
            padding: 0;
            width: min(560px, 92vw);
            max-height: 90vh;
            background: #ffffff;
            color: var(--text-main);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
        }

        dialog.modal-dialog::backdrop {
            background-color: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(2px);
        }

        .modal-header {
            padding: 1.15rem 1.4rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 1rem;
            font-weight: 700;
        }

        .modal-body {
            padding: 1.35rem 1.4rem;
            overflow-y: auto;
            max-height: calc(90vh - 130px);
        }

        .modal-footer {
            padding: 1rem 1.4rem;
            border-top: 1px solid var(--border-color);
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 0.65rem;
        }

        /* Pagination */
        .pagination-wrap {
            padding: 0.9rem 1.4rem;
            border-top: 1px solid var(--border-color);
            background: #ffffff;
        }

        .custom-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .pagination-info {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .pagination-list {
            list-style: none;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.3rem;
        }

        .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-main);
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .page-item a.page-link:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
        }

        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
        }

        .page-item.disabled .page-link {
            background: #f8fafc;
            color: #94a3b8;
            cursor: not-allowed;
        }

        @media (max-width: 992px) {
            .mobile-toggle {
                display: inline-flex;
            }
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.active {
                display: block;
            }
            .content-container {
                padding: 1.25rem;
            }
            .topbar {
                padding: 0.75rem 1.25rem;
            }
            .live-clock-badge {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $authUser = auth()->user();
        $pendingApprovalCount = $authUser && $authUser->isAdmin()
            ? \App\Models\Loan::whereIn('status', ['menunggu_persetujuan', 'menunggu_pengembalian'])->count()
            : 0;
    @endphp

    <!-- Mobile Overlay Backdrop -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="brand-logo" aria-hidden="true">
                <!-- School Shield Icon -->
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M9 12l2 2 4-4"/>
                </svg>
            </div>
            <div class="brand-text">
                <h1>SMAN 1 Nusantara</h1>
                <span>Sistem Sarpras & Inventaris</span>
            </div>
        </a>

        <div class="sidebar-section">Navigasi Utama</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="sidebar-link-inner">
                        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('items.index') }}" class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                    <span class="sidebar-link-inner">
                        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span>{{ $authUser->isAdmin() ? 'Data Barang' : 'Katalog Barang' }}</span>
                    </span>
                </a>
            </li>

            @if($authUser->isAdmin())
                <li>
                    <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <span class="sidebar-link-inner">
                            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                            <span>Kategori Barang</span>
                        </span>
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ route('loans.index') }}" class="sidebar-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                    <span class="sidebar-link-inner">
                        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            <path d="M9 14l2 2 4-4"></path>
                        </svg>
                        <span>{{ $authUser->isAdmin() ? 'Data Peminjaman' : 'Peminjaman Saya' }}</span>
                    </span>
                    @if($pendingApprovalCount > 0)
                        <span class="sidebar-badge" title="Menunggu Verifikasi">{{ $pendingApprovalCount }}</span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('return-logs.index') }}" class="sidebar-link {{ request()->routeIs('return-logs.*') ? 'active' : '' }}">
                    <span class="sidebar-link-inner">
                        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 10 4 15 9 20"></polyline>
                            <path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
                        </svg>
                        <span>Log Pengembalian</span>
                    </span>
                </a>
            </li>

            @if($authUser->isAdmin())
                <div class="sidebar-section">Administrasi & Audit</div>
                <li>
                    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <span class="sidebar-link-inner">
                            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <span>Laporan & Export</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="sidebar-link-inner">
                            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Data Pengguna</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                        <span class="sidebar-link-inner">
                            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <circle cx="12" cy="11" r="3"></circle>
                            </svg>
                            <span>Log Aktivitas (Audit)</span>
                        </span>
                    </a>
                </li>
            @endif

            <div class="sidebar-section">Pengaturan</div>
            <li>
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <span class="sidebar-link-inner">
                        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Profil Akun</span>
                    </span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-mini">
                <div class="user-avatar">{{ strtoupper(substr($authUser->name, 0, 1)) }}</div>
                <div class="user-meta">
                    <strong>{{ $authUser->name }}</strong>
                    <small>{{ $authUser->user_type }} • {{ $authUser->kelas_jabatan ?? ($authUser->isAdmin() ? 'Admin' : 'Peminjam') }}</small>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-toggle" onclick="toggleSidebar()" aria-label="Buka navigasi menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="topbar-title">
                    <nav aria-label="Breadcrumb" class="breadcrumb">
                        @hasSection('breadcrumbs')
                            @yield('breadcrumbs')
                        @else
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                            @if(! request()->routeIs('dashboard'))
                                <span class="sep">/</span>
                                <span aria-current="page">@yield('page_title')</span>
                            @endif
                        @endif
                    </nav>
                    <h2>@yield('page_title', 'Dashboard')</h2>
                    <p>@yield('page_subtitle', 'Sistem Informasi Inventarisasi dan Peminjaman Barang Sekolah')</p>
                </div>
            </div>
            <div class="topbar-actions">
                <div class="live-clock-badge" id="liveClockBadge" title="Waktu Sistem Saat Ini">
                    <span class="live-clock-dot"></span>
                    <span id="liveClockText">Memuat waktu...</span>
                </div>
                <span class="role-pill {{ $authUser->isAdmin() ? 'admin' : 'user' }}">
                    <span class="badge-dot"></span>
                    <span>{{ $authUser->isAdmin() ? 'Admin Sarpras' : $authUser->user_type }}</span>
                </span>
            </div>
        </header>

        <main class="content-container">
            @if(session('success'))
                <div class="alert alert-success" role="status">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>
                        <strong>Terdapat kesalahan input:</strong>
                        <ul style="margin-top:0.35rem; padding-left:1.25rem;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Toggle Mobile Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) {
                sidebar.classList.toggle('open');
            }
            if (overlay) {
                overlay.classList.toggle('active');
            }
        }

        // Live Clock (Waktu Indonesia)
        (function() {
            const clockEl = document.getElementById('liveClockText');
            if (!clockEl) return;

            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            function updateTime() {
                const now = new Date();
                const day = days[now.getDay()];
                const date = now.getDate();
                const month = months[now.getMonth()];
                const year = now.getFullYear();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');

                clockEl.textContent = `${day}, ${date} ${month} ${year} • ${hours}:${minutes}:${seconds} WIB`;
            }

            updateTime();
            setInterval(updateTime, 1000);
        })();

        // Autofocus input pertama saat modal dibuka
        (function () {
            const origShowModal = HTMLDialogElement.prototype.showModal;
            HTMLDialogElement.prototype.showModal = function () {
                origShowModal.apply(this, arguments);
                const firstInput = this.querySelector(
                    '.modal-body input:not([type="hidden"]):not([disabled]), .modal-body select:not([disabled]), .modal-body textarea:not([disabled])'
                );
                if (firstInput) {
                    requestAnimationFrame(() => firstInput.focus());
                }
            };
        })();

        // Tutup modal saat klik area backdrop
        document.querySelectorAll('dialog[closedby="any"]').forEach((dialog) => {
            if (!('closedBy' in HTMLDialogElement.prototype)) {
                dialog.addEventListener('click', (event) => {
                    if (event.target !== dialog) return;
                    const rect = dialog.getBoundingClientRect();
                    const isDialogContent = (
                        rect.top <= event.clientY &&
                        event.clientY <= rect.top + rect.height &&
                        rect.left <= event.clientX &&
                        event.clientX <= rect.left + rect.width
                    );
                    if (isDialogContent) return;
                    dialog.close();
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
