<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Packet Capture Logs - ThreatPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
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
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background: rgba(6, 9, 14, 0.92) !important;
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
            border-radius: 14px;
            padding: 20px 24px;
            transition: transform 0.25s, box-shadow 0.25s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .stat-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); margin-bottom: 6px; }
        .stat-value { font-size: 30px; font-weight: 800; line-height: 1; }

        /* ── Main Card ── */
        .page-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px 30px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.6);
        }

        /* ── Form controls ── */
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
        .table tbody tr:hover { background: rgba(255,255,255,0.03); }

        /* ── Protocol badges ── */
        .proto-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .proto-tcp   { background: rgba(88,166,255,0.15);  color: #58a6ff;  border: 1px solid rgba(88,166,255,0.3); }
        .proto-udp   { background: rgba(188,140,255,0.15); color: #bc8cff;  border: 1px solid rgba(188,140,255,0.3); }
        .proto-icmp  { background: rgba(210,153,34,0.15);  color: #d29922;  border: 1px solid rgba(210,153,34,0.3); }
        .proto-dns   { background: rgba(63,185,80,0.15);   color: #3fb950;  border: 1px solid rgba(63,185,80,0.3); }
        .proto-http  { background: rgba(248,81,73,0.15);   color: #f85149;  border: 1px solid rgba(248,81,73,0.3); }
        .proto-https { background: rgba(63,185,80,0.12);   color: #39d353;  border: 1px solid rgba(63,185,80,0.3); }
        .proto-arp   { background: rgba(210,153,34,0.10);  color: #e3b341;  border: 1px solid rgba(210,153,34,0.3); }
        .proto-ftp   { background: rgba(248,81,73,0.10);   color: #ff7b72;  border: 1px solid rgba(248,81,73,0.3); }

        /* ── Status dots ── */
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            flex-shrink: 0;
        }
        .dot-normal     { background: var(--accent-green);  box-shadow: 0 0 6px var(--accent-green); }
        .dot-suspicious { background: var(--accent-yellow); box-shadow: 0 0 6px var(--accent-yellow); }
        .dot-malicious  { background: var(--accent-red);    box-shadow: 0 0 8px var(--accent-red); animation: pulse-red 1.5s infinite; }

        @keyframes pulse-red {
            0%, 100% { box-shadow: 0 0 5px var(--accent-red); }
            50%       { box-shadow: 0 0 14px var(--accent-red); }
        }

        /* ── Direction badge ── */
        .dir-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 7px;
            border-radius: 5px;
        }
        .dir-inbound  { background: rgba(248,81,73,0.12);  color: #f85149; }
        .dir-outbound { background: rgba(88,166,255,0.12); color: #58a6ff; }
        .dir-internal { background: rgba(63,185,80,0.12);  color: #3fb950; }

        /* ── Mono font for IPs ── */
        .mono { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-muted); }

        /* ── Alert box ── */
        .alert-success-dark {
            background: rgba(63,185,80,0.12);
            border: 1px solid rgba(63,185,80,0.3);
            color: #3fb950;
            border-radius: 10px;
        }

        /* ── Live Capture SSE Styles ── */
        @keyframes status-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .animate-pulse {
            animation: status-pulse 1.5s infinite;
        }
        @keyframes highlight-fade {
            from { background: rgba(88, 166, 255, 0.25); }
            to { background: transparent; }
        }
        .table-success-fade {
            animation: highlight-fade 2.5s ease-out;
        }
    </style>
</head>
<body>

<!-- ═══ Navbar ═══ -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
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
                        <a class="nav-link" href="{{ route('network-devices.index') }}"><i class="fas fa-network-wired me-1"></i> Devices</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('packet-capture-logs.index') }}"><i class="fas fa-wave-square me-1"></i> Packets</a>
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

    <!-- ═══ Header + Stats ═══ -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1 fw-bold"><i class="fas fa-wave-square me-2 text-primary"></i>Packet Capture Logs</h2>
            <p class="text-muted mb-0 small">Real-time network packet analysis log. Identifies Normal, Suspicious, and Malicious traffic flows across all monitored interfaces.</p>
        </div>
        <form action="{{ route('packet-capture-logs.flush') }}" method="POST" onsubmit="return confirm('Are you sure you want to flush ALL packet capture logs? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                <i class="fas fa-trash-alt me-1"></i> Flush All Logs
            </button>
        </form>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Packets</div>
                <div class="stat-value text-white" id="totalPacketsCount">{{ number_format($total) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Normal</div>
                <div class="stat-value" id="normalPacketsCount" style="color:var(--accent-green)">{{ number_format($normal) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Suspicious</div>
                <div class="stat-value" id="suspiciousPacketsCount" style="color:var(--accent-yellow)">{{ number_format($suspicious) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Malicious</div>
                <div class="stat-value" id="maliciousPacketsCount" style="color:var(--accent-red)">{{ number_format($malicious) }}</div>
            </div>
        </div>
    </div>

    <!-- ═══ Live Capture Control Panel ═══ -->
    <div class="page-card mb-4" id="liveCaptureCard">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-satellite-dish me-2 text-info animate-pulse"></i>TShark Live Traffic Capturer</h5>
            <span class="badge bg-secondary" id="captureStatusBadge"><i class="fas fa-stop me-1"></i>Stopped</span>
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Select Interface</label>
                <select id="interfaceSelect" class="form-select">
                    <option value="">Loading network interfaces...</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">BPF Capture Filter (Optional)</label>
                <input type="text" id="captureFilter" class="form-control" placeholder="e.g. tcp, udp, port 80, host 127.0.0.1">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Packet Limit</label>
                <select id="packetLimit" class="form-select">
                    <option value="25">25 packets</option>
                    <option value="50">50 packets</option>
                    <option value="100" selected>100 packets</option>
                    <option value="200">200 packets</option>
                    <option value="500">500 packets</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button id="startCaptureBtn" class="btn btn-info rounded-3 px-3 flex-grow-1 text-dark fw-bold">
                    <i class="fas fa-play me-1"></i> Start Capture
                </button>
                <button id="stopCaptureBtn" class="btn btn-outline-danger rounded-3 px-3" disabled>
                    <i class="fas fa-stop me-1"></i> Stop
                </button>
            </div>
        </div>
        <div class="mt-3 d-none" id="captureLoadingInfo">
            <div class="d-flex align-items-center gap-2 text-info small">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <span>Listening on interface. Please generate network traffic (e.g. refresh dashboard or browse pages) to stream packets in live...</span>
            </div>
        </div>
    </div>

    <!-- ═══ Filter Bar ═══ -->
    <div class="page-card mb-4">
        <form method="GET" action="{{ route('packet-capture-logs.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Search IP / Info</label>
                <input type="text" name="search" class="form-control" placeholder="e.g. 192.168.1.1 or SSH brute..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Normal"     {{ $status === 'Normal'     ? 'selected' : '' }}>Normal</option>
                    <option value="Suspicious" {{ $status === 'Suspicious' ? 'selected' : '' }}>Suspicious</option>
                    <option value="Malicious"  {{ $status === 'Malicious'  ? 'selected' : '' }}>Malicious</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Protocol</label>
                <select name="protocol" class="form-select">
                    <option value="">All Protocols</option>
                    @foreach(['TCP','UDP','ICMP','DNS','HTTP','HTTPS','ARP','FTP'] as $proto)
                        <option value="{{ $proto }}" {{ $protocol === $proto ? 'selected' : '' }}>{{ $proto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Direction</label>
                <select name="direction" class="form-select">
                    <option value="">All Directions</option>
                    <option value="Inbound"  {{ $direction === 'Inbound'  ? 'selected' : '' }}>Inbound</option>
                    <option value="Outbound" {{ $direction === 'Outbound' ? 'selected' : '' }}>Outbound</option>
                    <option value="Internal" {{ $direction === 'Internal' ? 'selected' : '' }}>Internal</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-3 px-3 flex-grow-1">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                @if($search || $status || $protocol || $direction)
                    <a href="{{ route('packet-capture-logs.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ═══ Packet Table ═══ -->
    <div class="page-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">
                Showing <strong class="text-white">{{ $packets->count() }}</strong> of <strong class="text-white">{{ $packets->total() }}</strong> captured packets
            </span>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Captured At</th>
                        <th>Direction</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Protocol</th>
                        <th>Size</th>
                        <th>Payload Info</th>
                    </tr>
                </thead>
                <tbody>
                <tbody id="packetTableBody">
                    @forelse($packets as $pkt)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="status-dot dot-{{ strtolower($pkt->status) }}"></span>
                                    <span class="small text-{{ $pkt->status_color }}">{{ $pkt->status }}</span>
                                </div>
                            </td>
                            <td class="mono">{{ $pkt->captured_at->format('H:i:s') }}<br><span style="font-size:10px;color:var(--text-muted)">{{ $pkt->captured_at->diffForHumans() }}</span></td>
                            <td>
                                <span class="dir-badge dir-{{ strtolower($pkt->direction) }}">
                                    <i class="fas fa-{{ $pkt->direction === 'Inbound' ? 'arrow-down' : ($pkt->direction === 'Outbound' ? 'arrow-up' : 'arrows-alt-h') }} me-1"></i>
                                    {{ $pkt->direction }}
                                </span>
                            </td>
                            <td>
                                <span class="mono">{{ $pkt->source_ip }}</span>
                                @if($pkt->source_port > 0)<br><span class="mono" style="font-size:10px;color:var(--text-muted)">:{{ $pkt->source_port }}</span>@endif
                            </td>
                            <td>
                                <span class="mono">{{ $pkt->destination_ip }}</span>
                                @if($pkt->destination_port > 0)<br><span class="mono" style="font-size:10px;color:var(--text-muted)">:{{ $pkt->destination_port }}</span>@endif
                            </td>
                            <td>
                                <span class="proto-badge proto-{{ strtolower($pkt->protocol) }}">{{ $pkt->protocol }}</span>
                            </td>
                            <td class="mono">{{ number_format($pkt->packet_size) }} B</td>
                            <td class="text-muted small" style="max-width:320px;">{{ $pkt->info }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-wave-square d-block mb-2 text-secondary" style="font-size:28px;"></i>
                                No packet capture records match the current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $packets->links() }}
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let streamSource = null;

        const startCaptureBtn = document.getElementById('startCaptureBtn');
        const stopCaptureBtn = document.getElementById('stopCaptureBtn');
        const interfaceSelect = document.getElementById('interfaceSelect');
        const captureFilter = document.getElementById('captureFilter');
        const packetLimit = document.getElementById('packetLimit');
        const statusBadge = document.getElementById('captureStatusBadge');
        const loadingInfo = document.getElementById('captureLoadingInfo');
        const tableBody = document.getElementById('packetTableBody');

        // Populate interfaces
        fetch("{{ route('packet-capture-logs.interfaces') }}")
            .then(res => res.json())
            .then(data => {
                interfaceSelect.innerHTML = '';
                if (data.error) {
                    interfaceSelect.innerHTML = `<option value="">Error: ${data.error}</option>`;
                    return;
                }
                if (data.length === 0) {
                    interfaceSelect.innerHTML = '<option value="">No interfaces found</option>';
                    return;
                }
                data.forEach(iface => {
                    const opt = document.createElement('option');
                    opt.value = iface.index;
                    opt.textContent = `[${iface.index}] ${iface.name}`;
                    // Prefer loopback or Wi-Fi
                    if (iface.name.toLowerCase().includes('wi-fi') || iface.name.toLowerCase().includes('wireless')) {
                        opt.selected = true;
                    } else if (!interfaceSelect.querySelector('[selected]') && iface.name.toLowerCase().includes('loopback')) {
                        opt.selected = true;
                    }
                    interfaceSelect.appendChild(opt);
                });
            })
            .catch(err => {
                interfaceSelect.innerHTML = '<option value="">Error loading interfaces</option>';
                console.error(err);
            });

        startCaptureBtn.addEventListener('click', () => {
            const ifaceIndex = interfaceSelect.value;
            if (!ifaceIndex) {
                alert('Please select a network interface first.');
                return;
            }

            const filterVal = encodeURIComponent(captureFilter.value);
            const limitVal = packetLimit.value;

            // Reset UI state
            startCaptureBtn.disabled = true;
            stopCaptureBtn.disabled = false;
            interfaceSelect.disabled = true;
            captureFilter.disabled = true;
            packetLimit.disabled = true;
            loadingInfo.classList.remove('d-none');

            statusBadge.className = 'badge bg-danger animate-pulse';
            statusBadge.innerHTML = '<i class="fas fa-circle me-1"></i>Capturing...';

            // Clear empty row if present
            const emptyCell = tableBody.querySelector('tr td[colspan]');
            if (emptyCell) {
                emptyCell.closest('tr').remove();
            }

            const url = `{{ route('packet-capture-logs.stream') }}?interface=${ifaceIndex}&filter=${filterVal}&limit=${limitVal}`;
            
            streamSource = new EventSource(url);

            streamSource.onmessage = function(event) {
                const pkt = JSON.parse(event.data);
                
                const rowHtml = `
                    <tr class="table-success-fade">
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="status-dot dot-${pkt.status.toLowerCase()}"></span>
                                <span class="small text-${pkt.status_color}">${pkt.status}</span>
                            </div>
                        </td>
                        <td class="mono">
                            ${pkt.captured_at_formatted}<br>
                            <span style="font-size:10px;color:var(--text-muted)">Just now</span>
                        </td>
                        <td>
                            <span class="dir-badge dir-${pkt.direction.toLowerCase()}">
                                <i class="fas fa-${pkt.direction === 'Inbound' ? 'arrow-down' : (pkt.direction === 'Outbound' ? 'arrow-up' : 'arrows-alt-h')} me-1"></i>
                                ${pkt.direction}
                            </span>
                        </td>
                        <td>
                            <span class="mono">${pkt.source_ip}</span>
                            ${pkt.source_port > 0 ? `<br><span class="mono" style="font-size:10px;color:var(--text-muted)">:${pkt.source_port}</span>` : ''}
                        </td>
                        <td>
                            <span class="mono">${pkt.destination_ip}</span>
                            ${pkt.destination_port > 0 ? `<br><span class="mono" style="font-size:10px;color:var(--text-muted)">:${pkt.destination_port}</span>` : ''}
                        </td>
                        <td>
                            <span class="proto-badge proto-${pkt.protocol.toLowerCase()}">${pkt.protocol}</span>
                        </td>
                        <td class="mono">${Number(pkt.packet_size).toLocaleString()} B</td>
                        <td class="text-muted small" style="max-width:320px;">${escapeHtml(pkt.info)}</td>
                    </tr>
                `;
                
                tableBody.insertAdjacentHTML('afterbegin', rowHtml);

                // Update counter cards
                incrementCount('totalPacketsCount');
                if (pkt.status === 'Normal') {
                    incrementCount('normalPacketsCount');
                } else if (pkt.status === 'Suspicious') {
                    incrementCount('suspiciousPacketsCount');
                } else if (pkt.status === 'Malicious') {
                    incrementCount('maliciousPacketsCount');
                }
            };

            streamSource.addEventListener('end', function(event) {
                stopCapture();
            });

            streamSource.onerror = function(event) {
                console.error("SSE Stream error", event);
                stopCapture();
            };
        });

        stopCaptureBtn.addEventListener('click', () => {
            stopCapture();
        });

        function stopCapture() {
            if (streamSource) {
                streamSource.close();
                streamSource = null;
            }
            startCaptureBtn.disabled = false;
            stopCaptureBtn.disabled = true;
            interfaceSelect.disabled = false;
            captureFilter.disabled = false;
            packetLimit.disabled = false;
            loadingInfo.classList.add('d-none');

            statusBadge.className = 'badge bg-secondary';
            statusBadge.innerHTML = '<i class="fas fa-stop me-1"></i>Stopped';
        }

        function incrementCount(elementId) {
            const el = document.getElementById(elementId);
            if (el) {
                let val = parseInt(el.textContent.replace(/,/g, ''), 10) || 0;
                el.textContent = (val + 1).toLocaleString();
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    });
</script>
</body>
</html>
