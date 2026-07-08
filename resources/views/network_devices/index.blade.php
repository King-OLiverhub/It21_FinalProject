<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Network Device Inventory - ThreatPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
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

        .device-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .device-type-router { background: rgba(88, 166, 255, 0.15); color: var(--accent-blue); border: 1px solid rgba(88, 166, 255, 0.3); }
        .device-type-switch { background: rgba(188, 140, 255, 0.15); color: #bc8cff; border: 1px solid rgba(188, 140, 255, 0.3); }
        .device-type-firewall { background: rgba(248, 81, 73, 0.15); color: var(--accent-red); border: 1px solid rgba(248, 81, 73, 0.3); }
        .device-type-accesspoint { background: rgba(210, 153, 34, 0.15); color: var(--accent-yellow); border: 1px solid rgba(210, 153, 34, 0.3); }
        .device-type-server { background: rgba(63, 185, 80, 0.15); color: var(--accent-green); border: 1px solid rgba(63, 185, 80, 0.3); }
        .device-type-other { background: rgba(139, 148, 158, 0.15); color: #8b949e; border: 1px solid rgba(139, 148, 158, 0.3); }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-active      { background-color: var(--accent-green);  box-shadow: 0 0 8px var(--accent-green); }
        .status-inactive    { background-color: var(--accent-red);    box-shadow: 0 0 8px var(--accent-red); }
        .status-maintenance { background-color: var(--accent-yellow); box-shadow: 0 0 8px var(--accent-yellow); }
        .status-blocked     { background-color: #f85149; box-shadow: 0 0 10px #f85149; animation: pulse-blocked 1.2s infinite; }

        @keyframes pulse-blocked {
            0%, 100% { box-shadow: 0 0 5px #f85149; }
            50%       { box-shadow: 0 0 16px #f85149, 0 0 30px rgba(248,81,73,0.4); }
        }

        .row-blocked {
            background: rgba(248, 81, 73, 0.06) !important;
            border-left: 3px solid rgba(248, 81, 73, 0.5);
        }

        .blocked-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 5px;
            background: rgba(248,81,73,0.18);
            color: #f85149;
            border: 1px solid rgba(248,81,73,0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .row-updated {
            animation: pulse-update 1.5s ease-out;
        }

        @keyframes pulse-update {
            0% { background-color: rgba(88, 166, 255, 0.25); }
            100% { background-color: transparent; }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ Auth::user()->isNetworkAdmin() ? route('network-devices.index') : route('dashboard') }}">
                <i class="fa fa-shield"></i>
                <span>ThreatPulse</span>
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(!Auth::user()->isNetworkAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-chart-network me-1"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('servers.index') }}"><i class="fas fa-server me-1"></i> Servers</a>
                        </li>
                    @endif
                    @if(Auth::user()->isAdmin() || Auth::user()->isNetworkAdmin())
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('network-devices.index') }}"><i class="fas fa-network-wired me-1"></i> Devices</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('packet-capture-logs.index') }}"><i class="fas fa-wave-square me-1"></i> Packets</a>
                        </li>
                    @endif
                    @if(!Auth::user()->isNetworkAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.index') }}"><i class="fas fa-file-invoice me-1"></i> Reports</a>
                        </li>
                    @endif
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users') }}"><i class="fas fa-users me-1"></i> Users</a>
                        </li>
                    @endif
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge rounded-pill bg-{{ Auth::user()->role_badge_color }} px-3 py-2 text-capitalize">
                        <i class="fas {{ Auth::user()->role_icon }} me-1"></i> {{ Auth::user()->role_name }}
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 bg-success text-white py-3 px-4 rounded-4 mt-4 shadow" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div id="alert-container"></div>

        <div class="page-card">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="m-0 font-bold"><i class="fas fa-network-wired me-2 text-primary"></i>Network Device Inventory</h2>
                    <p class="text-muted mb-0 small">Secure record log of physical routers, switches, and firewalls on the local network footprint.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="btn-scan" class="btn btn-outline-primary px-4 py-2 rounded-3 shadow d-flex align-items-center gap-2">
                        <i class="fas fa-sync" id="scan-icon"></i>
                        <span id="scan-text">Scan Network</span>
                    </button>
                    <a href="{{ route('network-devices.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow">
                        <i class="fas fa-plus me-1"></i> Add Device
                    </a>
                </div>
            </div>

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('network-devices.index') }}" class="row g-3 mb-4 py-3 border-top border-bottom border-secondary">
                <div class="col-md-3">
                    <label class="form-label text-muted small">Device Type</label>
                    <select name="device_type" class="form-select" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="Router" {{ $type === 'Router' ? 'selected' : '' }}>Router</option>
                        <option value="Switch" {{ $type === 'Switch' ? 'selected' : '' }}>Switch</option>
                        <option value="Firewall" {{ $type === 'Firewall' ? 'selected' : '' }}>Firewall</option>
                        <option value="Access Point" {{ $type === 'Access Point' ? 'selected' : '' }}>Access Point</option>
                        <option value="Server" {{ $type === 'Server' ? 'selected' : '' }}>Server</option>
                        <option value="Other" {{ $type === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Active"      {{ $status === 'Active'      ? 'selected' : '' }}>Active</option>
                        <option value="Inactive"    {{ $status === 'Inactive'    ? 'selected' : '' }}>Inactive</option>
                        <option value="Maintenance" {{ $status === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Blocked"     {{ $status === 'Blocked'     ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-md-end">
                    @if($type || $status)
                        <a href="{{ route('network-devices.index') }}" class="btn btn-outline-secondary px-3 rounded-3">
                            <i class="fas fa-times me-1"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Device Name</th>
                            <th>IP Address</th>
                            <th>MAC Address</th>
                            <th>Type</th>
                            <th>Firmware</th>
                            <th>Location</th>
                            <th>Last Scanned</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($devices as $dev)
                            <tr class="{{ $dev->status === 'Blocked' ? 'row-blocked' : '' }}" data-device-id="{{ $dev->id }}">
                                <td>
                                    <span class="status-dot status-{{ strtolower($dev->status) }}"></span>
                                    @if($dev->status === 'Blocked')
                                        <span class="blocked-badge"><i class="fas fa-ban me-1"></i>Blocked</span>
                                    @else
                                        <span class="text-capitalize small">{{ $dev->status }}</span>
                                    @endif
                                </td>
                                <td><strong>{{ $dev->device_name }}</strong></td>
                                <td class="font-monospace text-muted">{{ $dev->ip_address }}</td>
                                <td class="font-monospace text-muted small">{{ $dev->mac_address }}</td>
                                <td>
                                    <span class="device-badge device-type-{{ strtolower(str_replace(' ', '', $dev->device_type)) }}">
                                        {{ $dev->device_type }}
                                    </span>
                                </td>
                                <td>{{ $dev->firmware_version ?: 'N/A' }}</td>
                                <td>{{ $dev->location ?: 'N/A' }}</td>
                                <td class="small text-muted">
                                    {{ $dev->last_scanned_at ? $dev->last_scanned_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="text-end">
                                    @if($dev->status !== 'Blocked')
                                        <a href="{{ route('network-devices.edit', $dev->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 me-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('network-devices.block', $dev->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Block device [{{ $dev->device_name }}] at {{ $dev->ip_address }}?\nThis will flag the device as Blocked.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-3">
                                                <i class="fas fa-ban me-1"></i> Block
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small me-2"><i class="fas fa-lock me-1"></i>Access Denied</span>
                                        <form action="{{ route('network-devices.unblock', $dev->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Unblock device [{{ $dev->device_name }}]?\nStatus will be set to Inactive for review.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-3">
                                                <i class="fas fa-lock-open me-1"></i> Unblock
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-network-wired d-block mb-2 text-secondary" style="font-size: 28px;"></i>
                                    No network devices found in inventory.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $devices->links() }}
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const btnScan = document.getElementById('btn-scan');
            const scanIcon = document.getElementById('scan-icon');
            const scanText = document.getElementById('scan-text');

            // Parse URL parameters for filters and page
            const urlParams = new URLSearchParams(window.location.search);
            const currentPage = urlParams.get('page') || 1;
            const deviceTypeSelect = document.querySelector('select[name="device_type"]');
            const statusSelect = document.querySelector('select[name="status"]');

            // Keep track of active statuses to highlight updates
            let previousDevices = {};

            // Collect active device table data and cache it initially
            document.querySelectorAll('table tbody tr[data-device-id]').forEach(row => {
                const id = row.getAttribute('data-device-id');
                const statusDot = row.querySelector('.status-dot');
                const lastScanned = row.querySelector('td:nth-child(8)');
                const ipAddress = row.querySelector('td:nth-child(3)');
                
                previousDevices[id] = {
                    status: statusDot ? statusDot.className : '',
                    last_scanned: lastScanned ? lastScanned.textContent.trim() : '',
                    ip: ipAddress ? ipAddress.textContent.trim() : ''
                };
            });

            // AJAX Manual Scan Trigger
            if (btnScan) {
                btnScan.addEventListener('click', function () {
                    const runSubnetScan = confirm(
                        "Perform a full subnet discovery scan?\n\n" +
                        "• Click OK to scan the entire local /24 subnet (discovers new devices, takes ~3 seconds).\n" +
                        "• Click Cancel to scan only registered inventory and ARP cache (takes <1 second)."
                    );

                    // Disable button and start loading indicator
                    btnScan.disabled = true;
                    scanIcon.classList.add('fa-spin');
                    scanText.textContent = "Scanning...";

                    fetch("{{ route('network-devices.scan') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({ subnet: runSubnetScan })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to refresh pagination and show session success message
                            window.location.reload();
                        } else {
                            showAlert("danger", data.message || "An error occurred during scanning.");
                            // Re-enable scan button
                            btnScan.disabled = false;
                            scanIcon.classList.remove('fa-spin');
                            scanText.textContent = "Scan Network";
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showAlert("danger", "Failed to contact scan service.");
                        // Re-enable scan button
                        btnScan.disabled = false;
                        scanIcon.classList.remove('fa-spin');
                        scanText.textContent = "Scan Network";
                    });
                });
            }

            // AJAX Polling for Live Status Updates
            function pollLiveDeviceStatus() {
                const typeVal = deviceTypeSelect ? deviceTypeSelect.value : '';
                const statusVal = statusSelect ? statusSelect.value : '';
                
                const params = new URLSearchParams({
                    page: currentPage,
                    device_type: typeVal,
                    status: statusVal
                });

                fetch(`{{ route('network-devices.data') }}?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data || !data.data) return;
                        
                        const devices = data.data;
                        const tbody = document.querySelector('table tbody');
                        if (!tbody) return;

                        // If table is empty
                        if (devices.length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-network-wired d-block mb-2 text-secondary" style="font-size: 28px;"></i>
                                        No network devices found in inventory.
                                    </td>
                                </tr>
                            `;
                            return;
                        }

                        let html = '';
                        devices.forEach(dev => {
                            const isBlocked = dev.status === 'Blocked';
                            const rowClass = isBlocked ? 'row-blocked' : '';
                            const statusLower = dev.status.toLowerCase();
                            
                            // Check if status, last scanned diff, or IP changed to add animation pulse
                            const prev = previousDevices[dev.id];
                            let pulseClass = '';
                            
                            const currentStatusClass = `status-dot status-${statusLower}`;
                            const currentLastScanned = dev.last_scanned_diff;
                            const currentIp = dev.ip_address;

                            if (prev) {
                                if (prev.status !== currentStatusClass || prev.last_scanned !== currentLastScanned || prev.ip !== currentIp) {
                                    pulseClass = 'row-updated';
                                }
                            }

                            // Cache status for next check
                            previousDevices[dev.id] = {
                                status: currentStatusClass,
                                last_scanned: currentLastScanned,
                                ip: currentIp
                            };

                            const statusBadge = isBlocked 
                                ? `<span class="blocked-badge"><i class="fas fa-ban me-1"></i>Blocked</span>` 
                                : `<span class="text-capitalize small">${dev.status}</span>`;
                                
                            const typeClass = dev.device_type.toLowerCase().replace(/\s+/g, '');
                            const firmwareStr = dev.firmware_version ? dev.firmware_version : 'N/A';
                            const locationStr = dev.location ? dev.location : 'N/A';
                            
                            let actionHtml = '';
                            if (!isBlocked) {
                                actionHtml = `
                                    <a href="/network-devices/${dev.id}/edit" class="btn btn-sm btn-outline-secondary rounded-3 me-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="/network-devices/${dev.id}/block" method="POST" class="d-inline"
                                          onsubmit="return confirm('Block device [${dev.device_name}] at ${dev.ip_address}?\\nThis will flag the device as Blocked.')">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-sm btn-danger rounded-3">
                                            <i class="fas fa-ban me-1"></i> Block
                                        </button>
                                    </form>
                                `;
                            } else {
                                actionHtml = `
                                    <span class="text-muted small me-2"><i class="fas fa-lock me-1"></i>Access Denied</span>
                                    <form action="/network-devices/${dev.id}/unblock" method="POST" class="d-inline"
                                          onsubmit="return confirm('Unblock device [${dev.device_name}]?\\nStatus will be set to Inactive for review.')">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-3">
                                            <i class="fas fa-lock-open me-1"></i> Unblock
                                        </button>
                                    </form>
                                `;
                            }

                            html += `
                                <tr class="${rowClass} ${pulseClass}" data-device-id="${dev.id}">
                                    <td>
                                        <span class="status-dot status-${statusLower}"></span>
                                        ${statusBadge}
                                    </td>
                                    <td><strong>${dev.device_name}</strong></td>
                                    <td class="font-monospace text-muted">${dev.ip_address}</td>
                                    <td class="font-monospace text-muted small">${dev.mac_address}</td>
                                    <td>
                                        <span class="device-badge device-type-${typeClass}">
                                            ${dev.device_type}
                                        </span>
                                    </td>
                                    <td>${firmwareStr}</td>
                                    <td>${locationStr}</td>
                                    <td class="small text-muted">${dev.last_scanned_diff}</td>
                                    <td class="text-end">${actionHtml}</td>
                                </tr>
                            `;
                        });

                        tbody.innerHTML = html;
                    })
                    .catch(err => console.error("Error polling live status:", err));
            }

            // Start live status polling every 15 seconds
            setInterval(pollLiveDeviceStatus, 15000);

            // Display dynamic alert messages
            function showAlert(type, message) {
                const container = document.getElementById('alert-container');
                if (container) {
                    container.innerHTML = `
                        <div class="alert alert-${type} alert-dismissible fade show border-0 bg-${type} text-white py-3 px-4 rounded-4 mt-4 shadow" role="alert">
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i> ${message}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }
            }
        });
    </script>
</body>
</html>
