<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Server - ThreatPulse</title>
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

        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            margin-top: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-control, .form-select {
            background-color: var(--bg-base);
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 8px;
            padding: 10px 14px;
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-base);
            color: var(--text-main);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.15);
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        
        .section-divider {
            border-bottom: 1px solid var(--border);
            margin: 24px 0;
            padding-bottom: 8px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--accent-blue);
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="form-card">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="m-0 font-bold"><i class="fas fa-server me-2 text-primary"></i>Register Cloud Instance</h2>
                    <p class="text-muted mb-0 small">Enter resource properties to configure monitoring parameters for this node.</p>
                </div>
                <a href="{{ route('servers.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="fas fa-arrow-left me-1"></i> Back Catalog
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 bg-danger text-white mb-4 rounded-3 small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('servers.store') }}">
                @csrf

                <!-- Section 1: Connection Meta -->
                <div class="section-divider">Connection Metadata</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Server / Instance Name *</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. AWS-EC2-Production" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ip_address" class="form-label">IP Address / Gateway *</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="e.g. 54.210.45.188" value="{{ old('ip_address') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="provider" class="form-label">Cloud Provider *</label>
                        <select class="form-select" id="provider" name="provider" required>
                            <option value="AWS" {{ old('provider') === 'AWS' ? 'selected' : '' }}>Amazon Web Services (AWS)</option>
                            <option value="Azure" {{ old('provider') === 'Azure' ? 'selected' : '' }}>Microsoft Azure</option>
                            <option value="GCP" {{ old('provider') === 'GCP' ? 'selected' : '' }}>Google Cloud Platform (GCP)</option>
                            <option value="Local" {{ old('provider') === 'Local' ? 'selected' : '' }}>Local Host / Physical PC</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Initial Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="online" {{ old('status') === 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ old('status') === 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="firewall_status" class="form-label">Firewall Status *</label>
                        <select class="form-select" id="firewall_status" name="firewall_status" required>
                            <option value="Active" {{ old('firewall_status') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Restricted" {{ old('firewall_status') === 'Restricted' ? 'selected' : '' }}>Restricted</option>
                            <option value="Disabled" {{ old('firewall_status') === 'Disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>

                <!-- Section 2: Resource Baseline -->
                <div class="section-divider">Resource Usage Thresholds</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="cpu_usage" class="form-label">CPU Usage (%)</label>
                        <input type="number" class="form-control" id="cpu_usage" name="cpu_usage" min="0" max="100" placeholder="e.g. 50" value="{{ old('cpu_usage', 20) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="memory_usage" class="form-label">RAM Memory Usage (%)</label>
                        <input type="number" class="form-control" id="memory_usage" name="memory_usage" min="0" max="100" placeholder="e.g. 45" value="{{ old('memory_usage', 30) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="storage_used" class="form-label">Storage Used (TB)</label>
                        <input type="number" step="0.01" class="form-control" id="storage_used" name="storage_used" min="0" placeholder="e.g. 1.2" value="{{ old('storage_used', 0.5) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="storage_total" class="form-label">Storage Total Size (TB)</label>
                        <input type="number" step="0.01" class="form-control" id="storage_total" name="storage_total" min="0" placeholder="e.g. 2.0" value="{{ old('storage_total', 1.0) }}">
                    </div>
                </div>

                <!-- Section 3: Network & Security -->
                <div class="section-divider">Network & Security Metrics</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="bandwidth_usage" class="form-label">Bandwidth (MB/s)</label>
                        <input type="number" step="0.01" class="form-control" id="bandwidth_usage" name="bandwidth_usage" min="0" placeholder="e.g. 55.4" value="{{ old('bandwidth_usage', 10.0) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="incoming_traffic" class="form-label">Incoming traffic (GB/day)</label>
                        <input type="number" step="0.01" class="form-control" id="incoming_traffic" name="incoming_traffic" min="0" placeholder="e.g. 12.5" value="{{ old('incoming_traffic', 5.0) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="outgoing_traffic" class="form-label">Outgoing traffic (GB/day)</label>
                        <input type="number" step="0.01" class="form-control" id="outgoing_traffic" name="outgoing_traffic" min="0" placeholder="e.g. 10.0" value="{{ old('outgoing_traffic', 4.0) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="response_time" class="form-label">Response Time (ms)</label>
                        <input type="number" class="form-control" id="response_time" name="response_time" min="0" placeholder="e.g. 45" value="{{ old('response_time', 20) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="failed_logins" class="form-label">Failed Logins (24h)</label>
                        <input type="number" class="form-control" id="failed_logins" name="failed_logins" min="0" placeholder="e.g. 0" value="{{ old('failed_logins', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="virtual_machines" class="form-label">Virtual Machines count</label>
                        <input type="number" class="form-control" id="virtual_machines" name="virtual_machines" min="0" value="{{ old('virtual_machines', 1) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="databases" class="form-label">Database nodes count</label>
                        <input type="number" class="form-control" id="databases" name="databases" min="0" value="{{ old('databases', 1) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="running_applications" class="form-label">Running Applications count</label>
                        <input type="number" class="form-control" id="running_applications" name="running_applications" min="0" value="{{ old('running_applications', 3) }}">
                    </div>
                </div>

                <!-- Section 4: Budget & Costing -->
                <div class="section-divider">Expenditure & Budgets</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="monthly_cost" class="form-label">Estimated Monthly Cost ($)</label>
                        <input type="number" step="0.01" class="form-control" id="monthly_cost" name="monthly_cost" min="0" placeholder="e.g. 120" value="{{ old('monthly_cost', 50.00) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="daily_usage" class="form-label">Estimated Daily Spend ($)</label>
                        <input type="number" step="0.01" class="form-control" id="daily_usage" name="daily_usage" min="0" placeholder="e.g. 4.00" value="{{ old('daily_usage', 1.60) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="budget_remaining" class="form-label">Remaining Budget ($)</label>
                        <input type="number" step="0.01" class="form-control" id="budget_remaining" name="budget_remaining" min="0" placeholder="e.g. 200" value="{{ old('budget_remaining', 150.00) }}">
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="mt-4 pt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('servers.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow">
                        <i class="fas fa-save me-1"></i> Register Node
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
