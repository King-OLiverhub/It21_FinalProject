<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ThreatPulse - Cloud Intelligence Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #06090e;
            --bg-surface: #0d1117;
            --bg-card: #151b23;
            --bg-input: #090d12;
            --border: #21262d;
            --border-hover: #30363d;
            --text-main: #f0f6fc;
            --text-muted: #8b949e;
            --text-secondary: #c9d1d9;
            --accent-blue: #58a6ff;
            --accent-green: #3fb950;
            --accent-yellow: #d29922;
            --accent-red: #f85149;
            --accent-purple: #bc8cff;
            --accent-glow: rgba(88, 166, 255, 0.15);
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Glassmorphic Navbar */
        .navbar {
            background: rgba(13, 17, 23, 0.8) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
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
        .navbar-brand i {
            font-size: 24px;
            text-shadow: 0 0 12px var(--accent-blue);
        }
        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--text-main) !important;
            background: rgba(255, 255, 255, 0.05);
        }

        /* Hero Statistics Area */
        .stats-header-card {
            background: radial-gradient(circle at 10% 20%, rgba(88, 166, 255, 0.05) 0%, transparent 50%), var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .stats-header-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent-blue);
        }
        .summary-title {
            font-weight: 800;
            font-size: 26px;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #fff 0%, var(--accent-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Metric Grid Cards */
        .metric-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .metric-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-hover);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 15px rgba(88, 166, 255, 0.05);
        }
        .metric-title {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .metric-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
        }
        .metric-icon-bg {
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 36px;
            opacity: 0.06;
            color: var(--text-main);
            transition: all 0.3s;
        }
        .metric-card:hover .metric-icon-bg {
            opacity: 0.12;
            transform: scale(1.1);
        }

        /* Custom Badges */
        .badge-provider {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .provider-aws { background: rgba(255, 153, 0, 0.15); color: #ff9900; border: 1px solid rgba(255, 153, 0, 0.3); }
        .provider-azure { background: rgba(0, 137, 214, 0.15); color: #0089d6; border: 1px solid rgba(0, 137, 214, 0.3); }
        .provider-gcp { background: rgba(66, 133, 244, 0.15); color: #4285f4; border: 1px solid rgba(66, 133, 244, 0.3); }
        .provider-local { background: rgba(139, 148, 158, 0.15); color: #8b949e; border: 1px solid rgba(139, 148, 158, 0.3); }

        /* Server status row / card */
        .server-grid-item {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            transition: all 0.3s;
            position: relative;
        }
        .server-grid-item:hover {
            border-color: var(--border-hover);
            transform: translateY(-2px);
        }
        .server-name {
            font-weight: 700;
            font-size: 16px;
            color: var(--text-main);
        }
        .server-ip {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-muted);
        }
        .gauge-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }
        .progress {
            background-color: var(--bg-base);
            height: 6px;
            border-radius: 3px;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-dot-online {
            background-color: var(--accent-green);
            box-shadow: 0 0 8px var(--accent-green);
        }
        .status-dot-offline {
            background-color: var(--accent-red);
            box-shadow: 0 0 8px var(--accent-red);
        }

        /* Widgets styling */
        .widget-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        .widget-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
        }
        .widget-title i {
            color: var(--accent-blue);
        }

        /* Console styling */
        .console-box {
            background: #03060a;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #39ff14;
            max-height: 240px;
            overflow-y: auto;
            box-shadow: inset 0 0 10px rgba(0,255,0,0.1);
        }
        .console-line {
            margin-bottom: 4px;
        }

        /* Alerts List */
        .alert-item {
            background: rgba(255, 255, 255, 0.02);
            border-left: 3px solid var(--accent-blue);
            border-radius: 4px 10px 10px 4px;
            padding: 14px 18px;
            margin-bottom: 12px;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alert-item.severity-Critical {
            border-left-color: var(--accent-red);
            background: rgba(248, 81, 73, 0.04);
        }
        .alert-item.severity-High {
            border-left-color: var(--accent-yellow);
            background: rgba(210, 153, 34, 0.04);
        }
        .alert-item.severity-Medium {
            border-left-color: var(--accent-blue);
        }
        .alert-title {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 3px;
        }
        .alert-desc {
            font-size: 12px;
            color: var(--text-muted);
        }
        .btn-resolve {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-main);
            transition: all 0.2s;
        }
        .btn-resolve:hover {
            background: rgba(255,255,255,0.08);
            border-color: var(--text-muted);
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 30px 0;
            font-size: 13px;
        }

        /* Chart Canvas wrapper */
        .chart-container {
            position: relative;
            height: 220px;
            width: 100%;
        }

        /* Simulation Controls */
        .sim-btn {
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: rgba(88, 166, 255, 0.08);
            color: var(--accent-blue);
            transition: all 0.2s;
            margin-right: 6px;
            margin-bottom: 6px;
        }
        .sim-btn:hover {
            background: var(--accent-blue);
            color: #fff;
            box-shadow: 0 0 10px rgba(88, 166, 255, 0.3);
        }

        /* Admin alert toast styling */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1050;
        }
    </style>
</head>
<body>

    <!-- Transparent Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fa fa-shield"></i>
                <span>ThreatPulse</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('dashboard') }}">
                            <i class="fas fa-chart-network me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('servers.index') }}">
                            <i class="fas fa-server me-1"></i> Servers
                        </a>
                    </li>
                    @if($user->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('network-devices.index') }}">
                                <i class="fas fa-network-wired me-1"></i> Devices
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('packet-capture-logs.index') }}">
                                <i class="fas fa-wave-square me-1"></i> Packets
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.index') }}">
                            <i class="fas fa-file-invoice me-1"></i> Reports
                        </a>
                    </li>
                    @if($user->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users') }}">
                                <i class="fas fa-users me-1"></i> Users
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge rounded-pill bg-{{ $user->role_badge_color }} px-3 py-2 text-capitalize">
                        <i class="fas {{ $user->role_icon }} me-1"></i> {{ $user->role_name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm border-secondary px-3 py-1.5 rounded-3">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        
        <!-- Welcome Alert banner -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 bg-success text-white py-3 px-4 rounded-4 mb-4 shadow" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Summary Header Card -->
        <div class="stats-header-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="summary-title">Cloud Intelligence Dashboard</h1>
                    <p class="text-muted mb-0">Real-time resource tracking, dynamic network monitoring, security events, and expenditure auditing.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="text-muted d-block small">Live System Status</span>
                    <span class="d-inline-flex align-items-center mt-1">
                        <span class="status-dot status-dot-online"></span>
                        <strong class="text-white">{{ $systemStatus['status'] }}</strong>
                    </span>
                    <span class="text-muted ms-3 small">Uptime: {{ $systemStatus['uptime'] }}</span>
                </div>
            </div>
        </div>

        <!-- 6 Core Summary Cards Grid -->
        <div class="row g-4 mb-4">
            <!-- 1. Total Servers -->
            <div class="col-6 col-lg-2">
                <div class="metric-card">
                    <div class="metric-title">Total Servers</div>
                    <div class="metric-value" id="card-total-servers">{{ $stats['total_servers'] }}</div>
                    <i class="fas fa-server metric-icon-bg"></i>
                </div>
            </div>
            <!-- 2. Active Servers -->
            <div class="col-6 col-lg-2">
                <div class="metric-card">
                    <div class="metric-title">Active Servers</div>
                    <div class="metric-value text-success" id="card-active-servers">{{ $stats['active_servers'] }}</div>
                    <i class="fas fa-check-circle metric-icon-bg"></i>
                </div>
            </div>
            <!-- 3. Avg CPU Usage -->
            <div class="col-6 col-lg-2">
                <div class="metric-card">
                    <div class="metric-title">CPU Usage</div>
                    <div class="metric-value text-info" id="card-avg-cpu">{{ $stats['avg_cpu'] }}%</div>
                    <i class="fas fa-microchip metric-icon-bg"></i>
                </div>
            </div>
            <!-- 4. Avg Memory Usage -->
            <div class="col-6 col-lg-2">
                <div class="metric-card">
                    <div class="metric-title">Memory Usage</div>
                    <div class="metric-value text-warning" id="card-avg-memory">{{ $stats['avg_memory'] }}%</div>
                    <i class="fas fa-memory metric-icon-bg"></i>
                </div>
            </div>
            <!-- 5. Total Storage Used -->
            <div class="col-6 col-lg-2">
                <div class="metric-card">
                    <div class="metric-title">Storage Used</div>
                    <div class="metric-value text-primary" id="card-storage-used">{{ $stats['storage_used'] }} TB</div>
                    <i class="fas fa-hdd metric-icon-bg"></i>
                </div>
            </div>
            <!-- 6. Monthly Cost -->
            <div class="col-6 col-lg-2">
                <div class="metric-card">
                    <div class="metric-title">Monthly Cost</div>
                    <div class="metric-value text-danger" id="card-monthly-cost">${{ number_format($stats['monthly_cost']) }}</div>
                    <i class="fas fa-dollar-sign metric-icon-bg"></i>
                </div>
            </div>
        </div>

        <!-- Charts Layout Grid -->
        <div class="row g-4 mb-4">
            <!-- CPU Usage Graph -->
            <div class="col-md-6 col-lg-3">
                <div class="widget-box">
                    <div class="widget-title">
                        <span><i class="fas fa-microchip me-2"></i>CPU Usage Timeline</span>
                        <span class="badge bg-secondary small">7 Days</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="cpuChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Storage Graph -->
            <div class="col-md-6 col-lg-3">
                <div class="widget-box">
                    <div class="widget-title">
                        <span><i class="fas fa-database me-2"></i>Storage Trajectory</span>
                        <span class="badge bg-secondary small">7 Days</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="storageChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Network Traffic Graph -->
            <div class="col-md-6 col-lg-3">
                <div class="widget-box">
                    <div class="widget-title">
                        <span><i class="fas fa-network-wired me-2"></i>Network Traffic</span>
                        <span class="badge bg-secondary small">7 Days</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="networkChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Cost Breakdown Graph -->
            <div class="col-md-6 col-lg-3">
                <div class="widget-box">
                    <div class="widget-title">
                        <span><i class="fas fa-chart-pie me-2"></i>Cost Distribution</span>
                        <span class="badge bg-secondary small">By Provider</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="costChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Details Section -->
        <div class="row g-4">
            
            <!-- Left Side: Server Status & Management -->
            <div class="col-lg-8">
                <div class="widget-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="m-0 font-bold"><i class="fas fa-tasks me-2 text-primary"></i>Server Status & Telemetry</h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted me-2" id="live-indicator"><i class="fas fa-circle-notch fa-spin text-success me-1"></i>Live Polling</span>
                            @if($user->isSecurityAnalyst())
                                <a href="{{ route('servers.create') }}" class="btn btn-primary btn-sm px-3 rounded-3 shadow">
                                    <i class="fas fa-plus me-1"></i> Add Server
                                </a>
                            @else
                                <button class="btn btn-secondary btn-sm px-3 rounded-3 shadow" disabled data-bs-toggle="tooltip" title="Only User can add servers.">
                                    <i class="fas fa-plus me-1"></i> Add Server
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Servers telemetry rows -->
                    <div class="row g-3" id="servers-telemetry-container">
                        @foreach ($servers as $srv)
                            <div class="col-md-6 server-card-container" data-server-id="{{ $srv->id }}">
                                <div class="server-grid-item">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="status-dot status-dot-{{ $srv->status === 'online' ? 'online' : 'offline' }}" id="dot-{{ $srv->id }}"></span>
                                            <span class="server-name" id="name-{{ $srv->id }}">{{ $srv->name }}</span>
                                            <div class="server-ip">{{ $srv->ip_address }}</div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="badge-provider provider-{{ strtolower($srv->provider) }}">{{ $srv->provider }}</span>
                                            @if($user->isAdmin())
                                                <div class="mt-2">
                                                    <a href="{{ route('servers.edit', $srv->id) }}" class="btn btn-link btn-sm text-secondary p-0 me-2" title="Edit Server Configuration">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('servers.destroy', $srv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this server?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0" title="Delete/Archive Server">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Gauge Progress Indicators -->
                                    <div class="mb-2">
                                        <div class="gauge-label">
                                            <span>CPU Usage</span>
                                            <span id="cpu-val-{{ $srv->id }}">{{ $srv->status === 'online' ? $srv->cpu_usage.'%' : '0%' }}</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" id="cpu-bar-{{ $srv->id }}" role="progressbar" style="width: {{ $srv->status === 'online' ? $srv->cpu_usage : 0 }}%"></div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="gauge-label">
                                            <span>Memory Usage</span>
                                            <span id="ram-val-{{ $srv->id }}">{{ $srv->status === 'online' ? $srv->memory_usage.'%' : '0%' }}</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" id="ram-bar-{{ $srv->id }}" role="progressbar" style="width: {{ $srv->status === 'online' ? $srv->memory_usage : 0 }}%"></div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <div class="gauge-label">
                                            <span>Disk Storage ({{ $srv->storage_used }} / {{ $srv->storage_total }} TB)</span>
                                            <span>{{ $srv->storage_total > 0 ? round(($srv->storage_used / $srv->storage_total)*100).'%' : '0%' }}</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $srv->storage_total > 0 ? ($srv->storage_used / $srv->storage_total)*100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Live Updates / Metrics Simulator Box -->
                <div class="widget-box">
                    <h4 class="widget-title">
                        <span><i class="fas fa-terminal me-2"></i>Live Diagnostics Console & Simulator</span>
                        <span class="badge bg-success small">Healthy</span>
                    </h4>
                    
                    <!-- Admin Simulation Triggers -->
                    @if ($user->isAdmin())
                        <div class="mb-3">
                            <span class="text-muted d-block mb-2 small font-bold uppercase"><i class="fas fa-cogs me-1"></i> Admin Action Simulator:</span>
                            <button onclick="triggerSimulatedAlert('High CPU')" class="sim-btn"><i class="fas fa-exclamation-triangle me-1"></i> Spike CPU Alert</button>
                            <button onclick="triggerSimulatedAlert('Low Storage')" class="sim-btn"><i class="fas fa-hdd me-1"></i> Low Storage Alert</button>
                            <button onclick="triggerSimulatedAlert('Server Offline')" class="sim-btn"><i class="fas fa-power-off me-1"></i> Crash Server Alert</button>
                            <button onclick="triggerSimulatedAlert('Security Alert')" class="sim-btn"><i class="fas fa-shield-alt me-1"></i> Failed Logins Alert</button>
                        </div>
                    @else
                        <div class="mb-3">
                            <span class="text-muted d-block mb-2 small"><i class="fas fa-info-circle me-1"></i> Note: Simulation triggers are restricted to Administrator accounts.</span>
                        </div>
                    @endif

                    <div class="console-box" id="diagnostic-console">
                        <div class="console-line">[12:00:00] ThreatPulse system initialized. Listening for resource events...</div>
                        <div class="console-line">[12:00:01] Loaded cloud credential keys for AWS, Azure, GCP. Status active.</div>
                        <div class="console-line">[12:00:03] Secure firewalls reporting operational integrity.</div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Alerts, Network, Cost, Security -->
            <div class="col-lg-4">
                
                <!-- Recent Alerts Widget -->
                <div class="widget-box">
                    <div class="widget-title">
                        <span><i class="fas fa-bell me-2 text-danger"></i>Recent Unresolved Alerts</span>
                        <span class="badge bg-danger rounded-pill px-2.5" id="alert-counter">{{ $recentAlerts->count() }}</span>
                    </div>

                    <div id="alerts-widget-container">
                        @forelse ($recentAlerts as $alert)
                            <div class="alert-item severity-{{ $alert->severity }}" id="alert-item-{{ $alert->id }}">
                                <div>
                                    <div class="alert-title">
                                        <i class="fas fa-exclamation-triangle me-1"></i> {{ $alert->alert_type }}
                                    </div>
                                    <div class="alert-desc">{{ $alert->message }}</div>
                                    <small class="text-muted style-italic">{{ $alert->created_at->diffForHumans() }}</small>
                                </div>
                                <div>
                                    <button class="btn-resolve" onclick="resolveAlert({{ $alert->id }})">Acknowledge</button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state" id="no-alerts-placeholder">
                                <i class="fas fa-check-double mb-2 d-block text-success" style="font-size: 24px;"></i>
                                All systems operational. No unresolved alerts.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Network Monitoring Widget -->
                <div class="widget-box">
                    <h4 class="widget-title">
                        <span><i class="fas fa-network-wired me-2"></i>Network Monitor</span>
                    </h4>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted"><i class="fas fa-hourglass-half me-2"></i>Avg Response Time</span>
                        <strong class="text-white" id="net-resp-time">{{ $stats['response_time'] }} ms</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted"><i class="fas fa-cloud-download-alt me-2"></i>Incoming Traffic</span>
                        <strong class="text-white" id="net-incoming">{{ $stats['incoming_traffic'] }} GB/day</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted"><i class="fas fa-cloud-upload-alt me-2"></i>Outgoing Traffic</span>
                        <strong class="text-white" id="net-outgoing">{{ $stats['outgoing_traffic'] }} GB/day</strong>
                    </div>
                </div>

                <!-- Security Monitor Widget -->
                <div class="widget-box">
                    <h4 class="widget-title">
                        <span><i class="fas fa-shield-alt me-2"></i>Security & Compliance</span>
                    </h4>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted"><i class="fas fa-user-times me-2"></i>Failed Logins (24h)</span>
                        <strong class="text-danger" id="sec-failed-logins">{{ $stats['failed_logins'] }} Attempts</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted"><i class="fas fa-firewall me-2"></i>Active Firewalls</span>
                        <strong class="text-success" id="sec-firewall-count">{{ $stats['firewall_active'] }} Online</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted"><i class="fas fa-user-shield me-2"></i>Security Compliance</span>
                        <strong class="text-info">ISO-27001 Passed</strong>
                    </div>
                </div>

                <!-- Resource & VM Aggregates -->
                <div class="widget-box">
                    <h4 class="widget-title">
                        <span><i class="fas fa-cubes me-2"></i>Cloud Resources</span>
                    </h4>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted"><i class="fas fa-cubes me-2"></i>Virtual Machines</span>
                        <strong class="text-white">{{ $stats['vms'] }} Instances</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted"><i class="fas fa-database me-2"></i>Databases</span>
                        <strong class="text-white">{{ $stats['databases'] }} active</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted"><i class="fas fa-laptop-code me-2"></i>Running Applications</span>
                        <strong class="text-white">{{ $stats['applications'] }} Apps</strong>
                    </div>
                </div>

                <!-- Cost Budget Widget -->
                <div class="widget-box">
                    <h4 class="widget-title">
                        <span><i class="fas fa-wallet me-2 text-success"></i>Cost Auditing</span>
                    </h4>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted">Total Monthly Cost</span>
                        <strong class="text-danger" id="cost-monthly">${{ number_format($stats['monthly_cost'], 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 py-2 border-bottom border-secondary">
                        <span class="text-muted">Daily Burn Rate</span>
                        <strong class="text-white">${{ number_format($servers->sum('daily_usage'), 2) }} / day</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Budget Remaining</span>
                        <strong class="text-success">${{ number_format($servers->sum('budget_remaining'), 2) }}</strong>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Toast container for live updates notifications -->
    <div class="toast-container">
        <div class="toast bg-dark text-white border-secondary" id="action-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-card text-white border-secondary">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toast-message"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global variables for Chart references
        let cpuChart, storageChart, networkChart, costChart;

        document.addEventListener('DOMContentLoaded', function() {
            // Load and build graphs
            loadTrends();

            // Set up live telemetry updating polling loop
            setInterval(fetchLiveTelemetry, 3000);
            
            // Console logging ticker simulation
            setInterval(runConsoleTicker, 5000);
        });

        // Initialize Charts with dynamic data
        function loadTrends() {
            fetch("{{ route('dashboard.trends') }}")
                .then(res => res.json())
                .then(resp => {
                    if (resp.success) {
                        const labels = resp.data.labels;

                        // 1. CPU Usage Chart
                        const ctxCpu = document.getElementById('cpuChart').getContext('2d');
                        cpuChart = new Chart(ctxCpu, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'CPU Usage %',
                                    data: resp.data.cpu,
                                    borderColor: '#58a6ff',
                                    backgroundColor: 'rgba(88, 166, 255, 0.05)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: getChartOptions()
                        });

                        // 2. Storage Trajectory Chart
                        const ctxStorage = document.getElementById('storageChart').getContext('2d');
                        storageChart = new Chart(ctxStorage, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Storage TB',
                                    data: resp.data.storage,
                                    borderColor: '#bc8cff',
                                    backgroundColor: 'rgba(188, 140, 255, 0.05)',
                                    fill: true,
                                    tension: 0.1
                                }]
                            },
                            options: getChartOptions()
                        });

                        // 3. Network Traffic Chart
                        const ctxNetwork = document.getElementById('networkChart').getContext('2d');
                        networkChart = new Chart(ctxNetwork, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Traffic GB',
                                    data: resp.data.traffic,
                                    borderColor: '#3fb950',
                                    backgroundColor: 'rgba(63, 185, 80, 0.05)',
                                    fill: true,
                                    tension: 0.3
                                }]
                            },
                            options: getChartOptions()
                        });

                        // 4. Cost Distribution Chart
                        const ctxCost = document.getElementById('costChart').getContext('2d');
                        const costLabels = Object.keys(resp.data.cost);
                        const costValues = Object.values(resp.data.cost);
                        costChart = new Chart(ctxCost, {
                            type: 'doughnut',
                            data: {
                                labels: costLabels,
                                datasets: [{
                                    data: costValues,
                                    backgroundColor: ['#ff9900', '#0089d6', '#4285f4', '#8b949e'],
                                    borderColor: '#151b23',
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: '#8b949e', boxWidth: 12 }
                                    }
                                }
                            }
                        });
                    }
                });
        }

        // Shared option structure for charts
        function getChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.03)' },
                        ticks: { color: '#8b949e', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.03)' },
                        ticks: { color: '#8b949e', font: { size: 10 } }
                    }
                }
            };
        }

        // Fetch updates via AJAX to update progress bars/gauges dynamically
        function fetchLiveTelemetry() {
            fetch("{{ route('dashboard.data') }}")
                .then(res => res.json())
                .then(resp => {
                    if (resp.success) {
                        const data = resp.data;
                        
                        // Update summaries
                        document.getElementById('card-total-servers').textContent = data.aggregates.total_servers;
                        document.getElementById('card-active-servers').textContent = data.aggregates.active_servers;
                        document.getElementById('card-avg-cpu').textContent = data.aggregates.avg_cpu + '%';
                        document.getElementById('card-avg-memory').textContent = data.aggregates.avg_memory + '%';
                        document.getElementById('card-storage-used').textContent = data.aggregates.storage_used + ' TB';
                        document.getElementById('card-monthly-cost').textContent = '$' + Number(data.aggregates.monthly_cost).toLocaleString();

                        // Loop servers and update progress bars
                        data.servers.forEach(server => {
                            const cpuBar = document.getElementById(`cpu-bar-${server.id}`);
                            const cpuVal = document.getElementById(`cpu-val-${server.id}`);
                            const ramBar = document.getElementById(`ram-bar-${server.id}`);
                            const ramVal = document.getElementById(`ram-val-${server.id}`);
                            const dot = document.getElementById(`dot-${server.id}`);

                            if (server.status === 'online') {
                                dot.className = 'status-dot status-dot-online';
                                
                                if (cpuBar && cpuVal) {
                                    cpuBar.style.width = server.cpu + '%';
                                    cpuVal.textContent = server.cpu + '%';
                                    
                                    // Make progress bar red if CPU spikes over 80
                                    if (server.cpu > 80) {
                                        cpuBar.className = 'progress-bar bg-danger';
                                    } else {
                                        cpuBar.className = 'progress-bar bg-info';
                                    }
                                }

                                if (ramBar && ramVal) {
                                    ramBar.style.width = server.ram + '%';
                                    ramVal.textContent = server.ram + '%';
                                }
                            } else {
                                dot.className = 'status-dot status-dot-offline';
                                if (cpuBar) cpuBar.style.width = '0%';
                                if (cpuVal) cpuVal.textContent = '0%';
                                if (ramBar) ramBar.style.width = '0%';
                                if (ramVal) ramVal.textContent = '0%';
                            }
                        });

                        // Re-render recent alerts list dynamically
                        renderAlertsList(data.recent_alerts);
                    }
                })
                .catch(err => console.error("Telemetry fetch failed: ", err));
        }

        // Render recent alerts dynamically
        function renderAlertsList(alerts) {
            const container = document.getElementById('alerts-widget-container');
            const alertCounter = document.getElementById('alert-counter');
            
            alertCounter.textContent = alerts.length;
            
            if (alerts.length === 0) {
                container.innerHTML = `
                    <div class="empty-state" id="no-alerts-placeholder">
                        <i class="fas fa-check-double mb-2 d-block text-success" style="font-size: 24px;"></i>
                        All systems operational. No unresolved alerts.
                    </div>`;
                return;
            }

            let html = '';
            alerts.forEach(alert => {
                const diffTime = 'just now'; // simplified timestamp
                html += `
                    <div class="alert-item severity-${alert.severity}" id="alert-item-${alert.id}">
                        <div>
                            <div class="alert-title">
                                <i class="fas fa-exclamation-triangle me-1"></i> ${alert.alert_type}
                            </div>
                            <div class="alert-desc">${alert.message}</div>
                            <small class="text-muted style-italic">Triggered ${diffTime}</small>
                        </div>
                        <div>
                            <button class="btn-resolve" onclick="resolveAlert(${alert.id})">Acknowledge</button>
                        </div>
                    </div>`;
            });
            container.innerHTML = html;
        }

        // AJAX resolve alert action
        function resolveAlert(id) {
            fetch(`/alerts/${id}/resolve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    showToast(resp.message);
                    fetchLiveTelemetry(); // refresh immediately
                    addConsoleLine(`Alert acknowledged & cleared. Resolved alert ID: ${id}`);
                }
            });
        }

        // AJAX simulate alert trigger (Admin only)
        function triggerSimulatedAlert(type) {
            fetch("{{ route('alerts.simulate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type: type })
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    showToast(resp.message);
                    fetchLiveTelemetry(); // refresh immediately
                    addConsoleLine(`Simulating alert event type: '${type}' triggered across provider nodes.`);
                }
            });
        }

        // Live Diagnostic Console ticker simulation
        function runConsoleTicker() {
            const actions = [
                "CPU thermal levels healthy across all physical host servers.",
                "Completed micro-service container scan. No virus hashes identified.",
                "AWS Billing socket connection verified. Usage audit on track.",
                "GCP network traffic routing tables updated. Latency is optimal.",
                "Security compliance audit: login check completed. Security audit compliant.",
                "Ping sensor verified connection to Local-Dev-Workstation in 2ms."
            ];
            const log = actions[Math.floor(Math.random() * actions.length)];
            addConsoleLine(log);
        }

        function addConsoleLine(text) {
            const consoleBox = document.getElementById('diagnostic-console');
            const time = new Date().toTimeString().split(' ')[0];
            const line = document.createElement('div');
            line.className = 'console-line';
            line.textContent = `[${time}] ${text}`;
            consoleBox.appendChild(line);
            consoleBox.scrollTop = consoleBox.scrollHeight;
        }

        // Toast controller
        function showToast(message) {
            const toastEl = document.getElementById('action-toast');
            document.getElementById('toast-message').textContent = message;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    </script>
</body>
</html>