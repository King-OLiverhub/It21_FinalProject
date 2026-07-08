<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Servers - ThreatPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #06090e;
            --bg-card: #151b23;
            --border: #21262d;
            --text-main: #f0f6fc;
            --text-muted: #8b949e;
            --accent-blue: #58a6ff;
            --accent-green: #3fb950;
            --accent-red: #f85149;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(13, 17, 23, 0.8) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 22px;
            color: var(--accent-blue) !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .nav-link:hover {
            color: var(--text-main) !important;
            background: rgba(255, 255, 255, 0.05);
        }

        .page-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            margin-top: 30px;
        }

        .form-select, .form-control {
            background-color: var(--bg-base);
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 8px;
            padding: 10px 14px;
        }
        .form-select:focus, .form-control:focus {
            background-color: var(--bg-base);
            color: var(--text-main);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.15);
        }

        .table {
            color: var(--text-main);
            border-color: var(--border);
        }
        .table th {
            color: var(--text-muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            border-bottom: 2px solid var(--border);
        }
        .table td {
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .provider-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .provider-aws { background: rgba(255, 153, 0, 0.15); color: #ff9900; }
        .provider-azure { background: rgba(0, 137, 214, 0.15); color: #0089d6; }
        .provider-gcp { background: rgba(66, 133, 244, 0.15); color: #4285f4; }
        .provider-local { background: rgba(139, 148, 158, 0.15); color: #8b949e; }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-online { background-color: var(--accent-green); box-shadow: 0 0 8px var(--accent-green); }
        .status-offline { background-color: var(--accent-red); box-shadow: 0 0 8px var(--accent-red); }
        .status-archived { background-color: var(--text-muted); }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
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
                        <a class="nav-link active" href="{{ route('servers.index') }}"><i class="fas fa-server me-1"></i> Servers</a>
                    </li>
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('network-devices.index') }}"><i class="fas fa-network-wired me-1"></i> Devices</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('packet-capture-logs.index') }}"><i class="fas fa-wave-square me-1"></i> Packets</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.index') }}"><i class="fas fa-file-invoice me-1"></i> Reports</a>
                    </li>
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users') }}"><i class="fas fa-users me-1"></i> Users</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-card">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="m-0 font-bold"><i class="fas fa-server me-2 text-primary"></i>Server Catalog</h2>
                    <p class="text-muted mb-0 small">Catalog of cloud instances and systems currently monitored under your computer profile.</p>
                </div>
                @if(Auth::user()->isSecurityAnalyst())
                    <a href="{{ route('servers.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow">
                        <i class="fas fa-plus me-1"></i> Register New Server
                    </a>
                @endif
            </div>

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('servers.index') }}" class="row g-3 mb-4 py-3 border-bottom border-top border-secondary">
                <div class="col-md-3">
                    <label class="form-label text-muted small">Filter by Cloud Provider</label>
                    <select name="provider" class="form-select" onchange="this.form.submit()">
                        <option value="">All Providers</option>
                        <option value="AWS" {{ $provider === 'AWS' ? 'selected' : '' }}>Amazon Web Services (AWS)</option>
                        <option value="Azure" {{ $provider === 'Azure' ? 'selected' : '' }}>Microsoft Azure</option>
                        <option value="GCP" {{ $provider === 'GCP' ? 'selected' : '' }}>Google Cloud Platform (GCP)</option>
                        <option value="Local" {{ $provider === 'Local' ? 'selected' : '' }}>Localhost / Physical PC</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">Filter by Operational Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="online" {{ $status === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ $status === 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-md-end">
                    @if($provider || $status)
                        <a href="{{ route('servers.index') }}" class="btn btn-outline-secondary px-3 rounded-3">
                            <i class="fas fa-times me-1"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </form>

            <!-- Servers Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Server Name</th>
                            <th>IP Address</th>
                            <th>Provider</th>
                            <th>VMS</th>
                            <th>DBs</th>
                            <th>Applications</th>
                            <th>Response Time</th>
                            <th>Monthly Cost</th>
                            @if(Auth::user()->isAdmin())
                                <th class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($servers as $srv)
                            <tr>
                                <td>
                                    <span class="status-dot status-{{ $srv->status }}"></span>
                                    <span class="text-capitalize small">{{ $srv->status }}</span>
                                </td>
                                <td><strong>{{ $srv->name }}</strong></td>
                                <td class="font-monospace text-muted">{{ $srv->ip_address }}</td>
                                <td>
                                    <span class="provider-badge provider-{{ strtolower($srv->provider) }}">{{ $srv->provider }}</span>
                                </td>
                                <td>{{ $srv->virtual_machines ?: 0 }} VM(s)</td>
                                <td>{{ $srv->databases ?: 0 }} DB(s)</td>
                                <td>{{ $srv->running_applications ?: 0 }} Run</td>
                                <td>
                                    @if ($srv->status === 'online')
                                        {{ $srv->response_time }} ms
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-info">${{ number_format($srv->monthly_cost, 2) }}</td>
                                @if(Auth::user()->isAdmin())
                                    <td class="text-end">
                                        <a href="{{ route('servers.edit', $srv->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 me-2">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('servers.destroy', $srv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this server?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-server d-block mb-2 text-secondary" style="font-size: 28px;"></i>
                                    No servers registered matching filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination links -->
            <div class="mt-4">
                {{ $servers->links() }}
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
