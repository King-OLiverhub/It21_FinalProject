<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Information - ThreatPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base:    #06090e;
            --bg-card:    #0d1117;
            --bg-surface: #151b23;
            --border:     #21262d;
            --text-main:  #f0f6fc;
            --text-muted: #8b949e;
            --accent-blue:   #58a6ff;
            --accent-green:  #3fb950;
            --accent-red:    #f85149;
            --accent-yellow: #d29922;
            --accent-purple: #bc8cff;
            --accent-cyan:   #39c5cf;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg-base);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background: rgba(6,9,14,0.92) !important;
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 22px;
            color: var(--accent-blue) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 14px !important;
            border-radius: 8px;
            transition: all 0.25s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--text-main) !important;
            background: rgba(255,255,255,0.06);
        }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px 26px;
            transition: transform 0.25s, box-shadow 0.25s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }
        .stat-card.blue::before  { background: var(--accent-blue); }
        .stat-card.green::before { background: var(--accent-green); }
        .stat-card.yellow::before{ background: var(--accent-yellow); }
        .stat-card.purple::before{ background: var(--accent-purple); }
        .stat-card.cyan::before  { background: var(--accent-cyan); }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); margin-bottom: 6px; }
        .stat-value { font-size: 32px; font-weight: 800; line-height: 1; }
        .stat-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 36px;
            opacity: 0.08;
        }

        /* ── Page Card ── */
        .page-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 26px 28px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.6);
        }

        /* ── Form Controls ── */
        .form-control, .form-select {
            background-color: var(--bg-base);
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 13px;
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-base);
            color: var(--text-main);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88,166,255,0.18);
        }
        .form-control::placeholder { color: var(--text-muted); }

        /* ── Table ── */
        .table {
            color: var(--text-main);
            border-color: var(--border);
            font-size: 13px;
        }
        .table thead th {
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
            padding: 12px 14px;
        }
        .table tbody td {
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            padding: 12px 14px;
        }
        .table tbody tr:hover { background: rgba(255,255,255,0.025); }

        /* ── Badges ── */
        .badge-role {
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .role-admin     { background: rgba(248,81,73,0.15);  color: #f85149; border: 1px solid rgba(248,81,73,0.3); }
        .role-security_analyst { background: rgba(88,166,255,0.15); color: #58a6ff; border: 1px solid rgba(88,166,255,0.3); }
        .role-network_admin { background: rgba(57,197,207,0.15); color: #39c5cf; border: 1px solid rgba(57,197,207,0.3); }
        .role-system_admin  { background: rgba(210,153,34,0.15);  color: #d29922; border: 1px solid rgba(210,153,34,0.3); }
        .role-it_security_staff { background: rgba(139,148,158,0.15); color: #8b949e; border: 1px solid rgba(139,148,158,0.3); }
        .role-info_security_officer { background: rgba(63,185,80,0.15); color: #3fb950; border: 1px solid rgba(63,185,80,0.3); }

        /* ── Status Dot ── */
        .status-dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            flex-shrink: 0;
        }
        .dot-active   { background: var(--accent-green); box-shadow: 0 0 6px var(--accent-green); }
        .dot-inactive { background: var(--text-muted); }

        /* ── Activity Type Badge ── */
        .activity-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .activity-login    { background: rgba(63,185,80,0.15); color: #3fb950; border: 1px solid rgba(63,185,80,0.3); }
        .activity-logout   { background: rgba(139,148,158,0.15); color: #8b949e; border: 1px solid rgba(139,148,158,0.3); }
        .activity-registration { background: rgba(88,166,255,0.15); color: #58a6ff; border: 1px solid rgba(88,166,255,0.3); }
        .activity-admin-action { background: rgba(210,153,34,0.15); color: #d29922; border: 1px solid rgba(210,153,34,0.3); }

        /* ── Avatar ── */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: #fff;
            flex-shrink: 0;
        }

        /* ── Mono font ── */
        .mono { font-family: 'JetBrains Mono', monospace; font-size: 12px; }

        /* ── Alert box ── */
        .alert-success-dark {
            background: rgba(63,185,80,0.12);
            border: 1px solid rgba(63,185,80,0.3);
            color: #3fb950;
            border-radius: 10px;
        }
        .alert-danger-dark {
            background: rgba(248,81,73,0.12);
            border: 1px solid rgba(248,81,73,0.3);
            color: #f85149;
            border-radius: 10px;
        }

        /* ── Tabs ── */
        .section-tabs {
            display: flex;
            gap: 4px;
            background: var(--bg-surface);
            border-radius: 10px;
            padding: 4px;
            border: 1px solid var(--border);
        }
        .section-tab {
            padding: 7px 18px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s;
            border: none;
            background: none;
        }
        .section-tab.active {
            background: var(--accent-blue);
            color: #fff;
        }

        /* ── Pagination ── */
        .pagination { gap: 4px; }
        .page-link {
            background: var(--bg-surface);
            border-color: var(--border);
            color: var(--text-muted);
            border-radius: 7px !important;
            font-size: 13px;
            padding: 5px 12px;
        }
        .page-link:hover, .page-item.active .page-link {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #fff;
        }
    </style>
</head>
<body>

<!-- ═══ Navbar ═══ -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fa fa-shield"></i>
            <span>ThreatPulse</span>
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-chart-network me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('servers.index') }}"><i class="fas fa-server me-1"></i> Servers</a>
                </li>
                @if(Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('network-devices.index') }}"><i class="fas fa-network-wired me-1"></i> Devices</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('packet-capture-logs.index') }}"><i class="fas fa-wave-square me-1"></i> Packets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.index') }}"><i class="fas fa-file-invoice me-1"></i> Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.users') }}"><i class="fas fa-users me-1"></i> Users</a>
                    </li>
                @endif
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill bg-{{ Auth::user()->role_badge_color }} px-3 py-2 text-capitalize">
                    <i class="fas {{ Auth::user()->role_icon }} me-1"></i> {{ Auth::user()->role_name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success-dark alert-dismissible fade show py-3 px-4 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger-dark alert-dismissible fade show py-3 px-4 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- ═══ Page Header ═══ -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1 fw-bold"><i class="fas fa-users-cog me-2 text-primary"></i>User Information</h2>
            <p class="text-muted mb-0 small">Monitor all registered accounts, login sessions, and user activity on ThreatPulse.</p>
        </div>
    </div>

    <!-- ═══ Stat Cards ═══ -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md col-lg">
            <div class="stat-card blue">
                <div class="stat-label">Total Users</div>
                <div class="stat-value text-white">{{ $totalUsers }}</div>
                <i class="fas fa-users stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="stat-card green">
                <div class="stat-label">Active Accounts</div>
                <div class="stat-value" style="color:var(--accent-green)">{{ $activeUsers }}</div>
                <i class="fas fa-user-check stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="stat-card" style="--c:#f85149; border-top: 3px solid var(--accent-red);">
                <div class="stat-label">Administrators</div>
                <div class="stat-value" style="color:var(--accent-red)">{{ $adminCount }}</div>
                <i class="fas fa-user-cog stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="stat-card" style="border-top: 3px solid var(--accent-blue);">
                <div class="stat-label">Security Analysts</div>
                <div class="stat-value" style="color:var(--accent-blue)">{{ $analystCount }}</div>
                <i class="fas fa-shield-alt stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="stat-card cyan">
                <div class="stat-label">Network Admins</div>
                <div class="stat-value" style="color:var(--accent-cyan)">{{ $netAdminCount }}</div>
                <i class="fas fa-network-wired stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- ═══ Section Tabs ═══ -->
    <div class="d-flex gap-3 mb-4 align-items-center">
        <div class="section-tabs">
            <button class="section-tab active" id="tab-users" onclick="switchTab('users')">
                <i class="fas fa-users me-1"></i> Registered Users
            </button>
            <button class="section-tab" id="tab-activity" onclick="switchTab('activity')">
                <i class="fas fa-history me-1"></i> Activity Log
            </button>
        </div>
    </div>

    <!-- ═══ SECTION: Registered Users ═══ -->
    <div id="section-users">
        <div class="page-card mb-4">
            <!-- Filter bar -->
            <form method="GET" action="{{ route('admin.users') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold">Search by Name or Email</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg-base);border-color:var(--border);color:var(--text-muted);">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="e.g. John or john@example.com" value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Filter by Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="security_analyst" {{ $role === 'security_analyst' ? 'selected' : '' }}>Security Analyst</option>
                        <option value="network_admin" {{ $role === 'network_admin' ? 'selected' : '' }}>Network Administrator</option>
                        <option value="system_admin" {{ $role === 'system_admin' ? 'selected' : '' }}>System Administrator</option>
                        <option value="it_security_staff" {{ $role === 'it_security_staff' ? 'selected' : '' }}>IT Security Staff</option>
                        <option value="info_security_officer" {{ $role === 'info_security_officer' ? 'selected' : '' }}>Info Security Officer</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-3 flex-grow-1">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    @if($search || $role)
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-3 px-3">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="mono text-muted">{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:13px;">
                                                {{ $user->name }}
                                                @if($user->id === Auth::id())
                                                    <span class="badge bg-primary ms-1" style="font-size:9px;">YOU</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="mono text-muted">{{ $user->email }}</td>
                                <td>
                                    <span class="badge-role role-{{ str_replace(' ', '_', $user->role) }}">
                                        <i class="fas {{ $user->role_icon }} me-1"></i>{{ $user->role_name }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="status-dot {{ $user->is_active ? 'dot-active' : 'dot-inactive' }}"></span>
                                        <span class="small {{ $user->is_active ? 'text-success' : 'text-muted' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="small text-muted">
                                    @if($user->last_login_at)
                                        <span class="mono">{{ $user->last_login_at->format('M d, Y H:i') }}</span><br>
                                        <span style="font-size:10px;">{{ $user->last_login_at->diffForHumans() }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Never logged in</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    <span class="mono">{{ $user->created_at->format('M d, Y') }}</span><br>
                                    <span style="font-size:10px;">{{ $user->created_at->diffForHumans() }}</span>
                                </td>
                                <td>
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm rounded-3 px-3 {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} {{ $user->name }}\'s account?')">
                                                <i class="fas {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }} me-1"></i>
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small fst-italic">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash d-block mb-2 text-secondary" style="font-size:28px;"></i>
                                    No users found matching the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    Showing <strong class="text-white">{{ $users->count() }}</strong> of <strong class="text-white">{{ $users->total() }}</strong> users
                </span>
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- ═══ SECTION: Activity Log ═══ -->
    <div id="section-activity" style="display:none;">
        <div class="page-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-warning"></i>User Login / Activity Log</h6>
                <span class="text-muted small">{{ $activityLogs->total() }} total events</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Event Type</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>IP Address</th>
                            <th>Details</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>
                                    @php
                                        $actClass = match(strtolower($log->activity_type)) {
                                            'login'  => 'activity-login',
                                            'logout' => 'activity-logout',
                                            'registration' => 'activity-registration',
                                            'admin action' => 'activity-admin-action',
                                            default  => 'activity-logout'
                                        };
                                        $actIcon = match(strtolower($log->activity_type)) {
                                            'login'  => 'fa-sign-in-alt',
                                            'logout' => 'fa-sign-out-alt',
                                            'registration' => 'fa-user-plus',
                                            'admin action' => 'fa-shield-alt',
                                            default  => 'fa-circle'
                                        };
                                    @endphp
                                    <span class="activity-badge {{ $actClass }}">
                                        <i class="fas {{ $actIcon }} me-1"></i>{{ $log->activity_type }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="user-avatar" style="width:28px;height:28px;font-size:11px;">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size:12px;">{{ $log->user->name }}</div>
                                                <div class="mono" style="font-size:10px;color:var(--text-muted);">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Deleted User</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->user)
                                        <span class="badge-role role-{{ $log->user->role }}">{{ $log->user->role_name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="mono text-muted" style="font-size:12px;">{{ $log->ip_address ?? '—' }}</td>
                                <td class="small text-muted" style="max-width:240px;">{{ $log->details }}</td>
                                <td class="small text-muted">
                                    <span class="mono">{{ $log->activity_at->format('M d, Y H:i:s') }}</span><br>
                                    <span style="font-size:10px;">{{ $log->activity_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-history d-block mb-2 text-secondary" style="font-size:28px;"></i>
                                    No activity logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $activityLogs->links() }}
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function switchTab(tab) {
        document.getElementById('section-users').style.display    = tab === 'users'    ? '' : 'none';
        document.getElementById('section-activity').style.display = tab === 'activity' ? '' : 'none';
        document.getElementById('tab-users').classList.toggle('active', tab === 'users');
        document.getElementById('tab-activity').classList.toggle('active', tab === 'activity');
    }

    // Restore active tab based on URL hash
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#activity') {
            switchTab('activity');
        }
    });
</script>
</body>
</html>
