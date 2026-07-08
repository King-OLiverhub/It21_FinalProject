<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Network Device - ThreatPulse</title>
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
            max-width: 700px;
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
            color: var(--text-main);
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ Auth::user()->isNetworkAdmin() ? route('network-devices.index') : route('dashboard') }}">
                <i class="fas fa-microchip"></i>
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="form-card">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="m-0 font-bold"><i class="fas fa-edit me-2 text-warning"></i>Modify Network Device</h2>
                    <p class="text-muted mb-0 small">Edit metadata parameters or status settings for this network node.</p>
                </div>
                <a href="{{ route('network-devices.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Catalog
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

            <form method="POST" action="{{ route('network-devices.update', $device->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="device_name" class="form-label">Device Name *</label>
                        <input type="text" class="form-control" id="device_name" name="device_name" placeholder="e.g. Gateway-Router-1" value="{{ old('device_name', $device->device_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ip_address" class="form-label">IP Address *</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="e.g. 192.168.1.1" value="{{ old('ip_address', $device->ip_address) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mac_address" class="form-label">MAC Address *</label>
                        <input type="text" class="form-control" id="mac_address" name="mac_address" placeholder="e.g. 00:1A:2B:3C:4D:5E" value="{{ old('mac_address', $device->mac_address) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="device_type" class="form-label">Device Type *</label>
                        <select class="form-select" id="device_type" name="device_type" required>
                            <option value="Router" {{ old('device_type', $device->device_type) === 'Router' ? 'selected' : '' }}>Router</option>
                            <option value="Switch" {{ old('device_type', $device->device_type) === 'Switch' ? 'selected' : '' }}>Switch</option>
                            <option value="Firewall" {{ old('device_type', $device->device_type) === 'Firewall' ? 'selected' : '' }}>Firewall</option>
                            <option value="Access Point" {{ old('device_type', $device->device_type) === 'Access Point' ? 'selected' : '' }}>Access Point</option>
                            <option value="Server" {{ old('device_type', $device->device_type) === 'Server' ? 'selected' : '' }}>Server</option>
                            <option value="Other" {{ old('device_type', $device->device_type) === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Operational Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Active" {{ old('status', $device->status) === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status', $device->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Maintenance" {{ old('status', $device->status) === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="firmware_version" class="form-label">Firmware Version</label>
                        <input type="text" class="form-control" id="firmware_version" name="firmware_version" placeholder="e.g. v15.2.4" value="{{ old('firmware_version', $device->firmware_version) }}">
                    </div>
                    <div class="col-md-12">
                        <label for="location" class="form-label">Physical Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="e.g. Core Server Rack 3-A" value="{{ old('location', $device->location) }}">
                    </div>
                </div>

                <div class="mt-4 pt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('network-devices.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-warning text-dark px-4 rounded-3 shadow font-bold">
                        <i class="fas fa-save me-1"></i> Update Device
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
