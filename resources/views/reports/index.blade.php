<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelligence Reports - ThreatPulse</title>
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
            --text-secondary: #c9d1d9;
            --accent-blue: #58a6ff;
            --accent-green: #3fb950;
            --accent-red: #f85149;
            --accent-yellow: #d29922;
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
        .nav-link:hover, .nav-link.active {
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

        /* Tabs custom styling */
        .nav-pills .nav-link {
            background-color: var(--bg-base);
            border: 1px solid var(--border);
            color: var(--text-muted) !important;
            border-radius: 8px;
            margin-right: 8px;
            padding: 10px 20px !important;
        }
        .nav-pills .nav-link.active {
            background-color: var(--accent-blue) !important;
            color: #fff !important;
            border-color: var(--accent-blue);
        }

        .report-section {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            background-color: rgba(255, 255, 255, 0.01);
            margin-top: 24px;
        }

        .report-meta {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--accent-blue);
            font-weight: 700;
        }

        .table {
            color: var(--text-main);
            border-color: var(--border);
        }
        .table th {
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .provider-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .provider-aws { background: rgba(255, 153, 0, 0.15); color: #ff9900; }
        .provider-azure { background: rgba(0, 137, 214, 0.15); color: #0089d6; }
        .provider-gcp { background: rgba(66, 133, 244, 0.15); color: #4285f4; }
        .provider-local { background: rgba(139, 148, 158, 0.15); color: #8b949e; }

        /* Print formatting stylesheets */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
            }
            .navbar, .nav-pills, .btn-print {
                display: none !important;
            }
            .page-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
            }
            .report-section {
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
            }
            .table {
                color: #000 !important;
                border-color: #ccc !important;
            }
            .table th {
                color: #333 !important;
                border-bottom: 2px solid #333 !important;
            }
            .table td {
                border-bottom: 1px solid #ddd !important;
            }
            .text-muted {
                color: #555 !important;
            }
            .text-info {
                color: #000 !important;
            }
            .provider-badge {
                border: 1px solid #666 !important;
                background: transparent !important;
                color: #000 !important;
            }
        }
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
                        <a class="nav-link" href="{{ route('servers.index') }}"><i class="fas fa-server me-1"></i> Servers</a>
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
                        <a class="nav-link active" href="{{ route('reports.index') }}"><i class="fas fa-file-invoice me-1"></i> Reports</a>
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

    <div class="container my-4">
        <div class="page-card">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="m-0 font-bold"><i class="fas fa-file-invoice me-2 text-primary"></i>Cloud Intelligence Reports</h2>
                    <p class="text-muted mb-0 small font-bold">Review resource baselines, network traffic profiles, budgets, and security audits.</p>
                </div>
                <button onclick="window.print()" class="btn btn-primary px-4 py-2 rounded-3 shadow btn-print">
                    <i class="fas fa-print me-1"></i> Print Report
                </button>
            </div>

            <!-- Tabs selector for Daily, Weekly, Monthly -->
            <ul class="nav nav-pills mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'daily' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'daily']) }}">
                        <i class="fas fa-calendar-day me-1"></i> Daily Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'weekly' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'weekly']) }}">
                        <i class="fas fa-calendar-week me-1"></i> Weekly Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'monthly' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'monthly']) }}">
                        <i class="fas fa-calendar-alt me-1"></i> Monthly Summary
                    </a>
                </li>
            </ul>

            <!-- Report content box -->
            <div class="report-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="report-meta">Reporting Interval: {{ $trends['period'] }}</div>
                    <small class="text-muted">Generated on {{ now()->format('Y-m-d H:i') }}</small>
                </div>

                <h3 class="mb-3 text-capitalize">{{ $type }} Performance Summary</h3>
                <p class="text-secondary mb-4">{{ $trends['summary'] }}</p>

                <!-- Section 1: Aggregate Metrics -->
                <h5 class="mb-3 text-white border-bottom border-secondary pb-2"><i class="fas fa-chart-line me-2 text-info"></i>Key Performance Indicators (KPIs)</h5>
                <div class="row g-3 mb-4">
                    @foreach ($trends['metrics'] as $label => $val)
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3" style="background-color: rgba(255, 255, 255, 0.02); border: 1px solid var(--border);">
                                <span class="text-muted d-block small mb-1">{{ $label }}</span>
                                <strong class="text-white" style="font-size: 18px;">{{ $val }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Section 2: Active Server Details Table -->
                <h5 class="mb-3 text-white border-bottom border-secondary pb-2"><i class="fas fa-server me-2 text-info"></i>Telemetry Baseline Summary</h5>
                <div class="table-responsive mb-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Server Name</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>CPU Usage</th>
                                <th>Memory Usage</th>
                                <th>Storage Used</th>
                                <th>Monthly Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($servers as $srv)
                                <tr>
                                    <td><strong>{{ $srv->name }}</strong></td>
                                    <td><span class="provider-badge provider-{{ strtolower($srv->provider) }}">{{ $srv->provider }}</span></td>
                                    <td><span class="text-capitalize small">{{ $srv->status }}</span></td>
                                    <td>{{ $srv->status === 'online' ? $srv->cpu_usage.'%' : '0%' }}</td>
                                    <td>{{ $srv->status === 'online' ? $srv->memory_usage.'%' : '0%' }}</td>
                                    <td>{{ $srv->storage_used }} / {{ $srv->storage_total }} TB</td>
                                    <td>${{ number_format($srv->monthly_cost, 2) }}</td>
                                </tr>
                            @endforeach
                            <!-- Total Aggregate Row -->
                            <tr class="fw-bold" style="background-color: rgba(255, 255, 255, 0.02);">
                                <td>Aggregate Total</td>
                                <td>-</td>
                                <td>{{ $activeServers }} / {{ $totalServers }} Active</td>
                                <td>Avg {{ round($avgCpu, 1) }}%</td>
                                <td>Avg {{ round($avgRam, 1) }}%</td>
                                <td>{{ round($totalStorageUsed, 2) }} / {{ round($totalStorageCapacity, 2) }} TB</td>
                                <td class="text-info">${{ number_format($totalMonthlyCost, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Section 3: Logged Incident Auditing -->
                <h5 class="mb-3 text-white border-bottom border-secondary pb-2"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>System Incident Log</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Severity</th>
                                <th>Incident / Alert Type</th>
                                <th>Alert Message</th>
                                <th>Resolution</th>
                                <th>Triggered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($alerts as $alt)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $alt->severity === 'Critical' ? 'danger' : ($alt->severity === 'High' ? 'warning' : 'info') }} text-dark px-2">
                                            {{ $alt->severity }}
                                        </span>
                                    </td>
                                    <td>{{ $alt->alert_type }}</td>
                                    <td class="small">{{ $alt->message }}</td>
                                    <td>
                                        @if ($alt->is_resolved)
                                            <span class="text-success"><i class="fas fa-check-circle me-1"></i> Resolved</span>
                                        @else
                                            <span class="text-warning"><i class="fas fa-exclamation-circle me-1"></i> Active</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $alt->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No security incidents recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Sign off -->
                <div class="mt-5 pt-4 border-top border-secondary text-end">
                    <span class="text-muted d-block small">Audited & Verified By:</span>
                    <strong class="text-white">ThreatPulse Intelligence Engine</strong>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
