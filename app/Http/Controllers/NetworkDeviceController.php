<?php

namespace App\Http\Controllers;

use App\Models\NetworkDevice;
use App\Services\NetworkDeviceScanner;
use Illuminate\Http\Request;

class NetworkDeviceController extends Controller
{
    /**
     * Display a listing of the network devices.
     */
    public function index(Request $request)
    {
        $type = $request->get('device_type');
        $status = $request->get('status');

        $query = NetworkDevice::query();

        if ($type) {
            $query->where('device_type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $devices = $query->orderBy('device_name')->paginate(10);

        return view('network_devices.index', compact('devices', 'type', 'status'));
    }

    /**
     * Show the form for creating a new network device.
     */
    public function create()
    {
        return view('network_devices.create');
    }

    /**
     * Store a newly created network device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'mac_address' => 'required|string|max:50',
            'device_type' => 'required|string|in:Router,Switch,Firewall,Access Point,Server,Other',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            'firmware_version' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['last_scanned_at'] = now();

        NetworkDevice::create($validated);

        return redirect()->route('network-devices.index')->with('success', 'Network device ' . $request->device_name . ' added to inventory successfully.');
    }

    /**
     * Show the form for editing the specified network device.
     */
    public function edit($id)
    {
        $device = NetworkDevice::findOrFail($id);
        return view('network_devices.edit', compact('device'));
    }

    /**
     * Update the specified network device in storage.
     */
    public function update(Request $request, $id)
    {
        $device = NetworkDevice::findOrFail($id);

        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'mac_address' => 'required|string|max:50',
            'device_type' => 'required|string|in:Router,Switch,Firewall,Access Point,Server,Other',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            'firmware_version' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
        ]);

        $device->update($validated);

        return redirect()->route('network-devices.index')->with('success', 'Network device ' . $request->device_name . ' updated successfully.');
    }

    /**
     * Remove the specified network device from storage.
     */
    public function destroy($id)
    {
        $device = NetworkDevice::findOrFail($id);
        $name = $device->device_name;
        $device->delete();

        return redirect()->route('network-devices.index')->with('success', 'Network device ' . $name . ' removed from inventory.');
    }

    /**
     * Block a network device — sets status to Blocked.
     * Admin-only via route middleware.
     */
    public function block($id)
    {
        $device = NetworkDevice::findOrFail($id);
        $device->update(['status' => 'Blocked']);

        return redirect()->route('network-devices.index')
            ->with('success', "Device [{$device->device_name}] has been BLOCKED. All traffic from {$device->ip_address} is now flagged.");
    }

    /**
     * Unblock a network device — sets status back to Inactive for review.
     * Admin-only via route middleware.
     */
    public function unblock($id)
    {
        $device = NetworkDevice::findOrFail($id);
        $device->update(['status' => 'Inactive']);

        return redirect()->route('network-devices.index')
            ->with('success', "Device [{$device->device_name}] has been UNBLOCKED and set to Inactive for review.");
    }

    /**
     * Trigger a live network discovery scan.
     */
    public function scan(Request $request, NetworkDeviceScanner $scanner)
    {
        try {
            $scanSubnet = (bool) $request->input('subnet', false);
            $results = $scanner->scanAndSync($scanSubnet);
            
            return response()->json([
                'success' => true,
                'message' => 'Network scan completed successfully.',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error running network scan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve live device data list (filtered and paginated) in JSON.
     */
    public function data(Request $request)
    {
        $type = $request->get('device_type');
        $status = $request->get('status');

        $query = NetworkDevice::query();

        if ($type) {
            $query->where('device_type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $devices = $query->orderBy('device_name')->paginate(10);

        // Include diffForHumans for each device's last_scanned_at
        $devices->getCollection()->transform(function($device) {
            $device->last_scanned_diff = $device->last_scanned_at 
                ? $device->last_scanned_at->diffForHumans() 
                : 'Never';
            return $device;
        });

        return response()->json($devices);
    }
}

