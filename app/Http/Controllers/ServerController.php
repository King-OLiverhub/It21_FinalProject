<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * Display a listing of the servers.
     */
    public function index(Request $request)
    {
        $provider = $request->get('provider');
        $status = $request->get('status');

        $query = Server::query();

        if ($provider) {
            $query->where('provider', $provider);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $servers = $query->orderBy('name')->paginate(10);

        return view('servers.index', compact('servers', 'provider', 'status'));
    }

    /**
     * Show the form for creating a new server.
     */
    public function create()
    {
        return view('servers.create');
    }

    /**
     * Store a newly created server in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:45',
            'provider' => 'required|string|in:AWS,Azure,GCP,Local',
            'status' => 'required|string|in:online,offline,archived',
            'cpu_usage' => 'nullable|integer|between:0,100',
            'memory_usage' => 'nullable|integer|between:0,100',
            'storage_used' => 'nullable|numeric|min:0',
            'storage_total' => 'nullable|numeric|min:0',
            'virtual_machines' => 'nullable|integer|min:0',
            'databases' => 'nullable|integer|min:0',
            'running_applications' => 'nullable|integer|min:0',
            'bandwidth_usage' => 'nullable|numeric|min:0',
            'incoming_traffic' => 'nullable|numeric|min:0',
            'outgoing_traffic' => 'nullable|numeric|min:0',
            'response_time' => 'nullable|integer|min:0',
            'failed_logins' => 'nullable|integer|min:0',
            'firewall_status' => 'required|string|in:Active,Restricted,Disabled',
            'monthly_cost' => 'nullable|numeric|min:0',
            'daily_usage' => 'nullable|numeric|min:0',
            'budget_remaining' => 'nullable|numeric|min:0',
        ]);

        // Default empty values to 0
        foreach ($validated as $key => $value) {
            if ($value === null && $key !== 'name' && $key !== 'ip_address' && $key !== 'provider' && $key !== 'status' && $key !== 'firewall_status') {
                $validated[$key] = 0;
            }
        }

        Server::create($validated);

        return redirect()->route('dashboard')->with('success', 'Server ' . $request->name . ' registered successfully!');
    }

    /**
     * Show the form for editing the specified server.
     */
    public function edit(Server $server)
    {
        return view('servers.edit', compact('server'));
    }

    /**
     * Update the specified server in database.
     */
    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:45',
            'provider' => 'required|string|in:AWS,Azure,GCP,Local',
            'status' => 'required|string|in:online,offline,archived',
            'cpu_usage' => 'nullable|integer|between:0,100',
            'memory_usage' => 'nullable|integer|between:0,100',
            'storage_used' => 'nullable|numeric|min:0',
            'storage_total' => 'nullable|numeric|min:0',
            'virtual_machines' => 'nullable|integer|min:0',
            'databases' => 'nullable|integer|min:0',
            'running_applications' => 'nullable|integer|min:0',
            'bandwidth_usage' => 'nullable|numeric|min:0',
            'incoming_traffic' => 'nullable|numeric|min:0',
            'outgoing_traffic' => 'nullable|numeric|min:0',
            'response_time' => 'nullable|integer|min:0',
            'failed_logins' => 'nullable|integer|min:0',
            'firewall_status' => 'required|string|in:Active,Restricted,Disabled',
            'monthly_cost' => 'nullable|numeric|min:0',
            'daily_usage' => 'nullable|numeric|min:0',
            'budget_remaining' => 'nullable|numeric|min:0',
        ]);

        // Default empty values to 0
        foreach ($validated as $key => $value) {
            if ($value === null && $key !== 'name' && $key !== 'ip_address' && $key !== 'provider' && $key !== 'status' && $key !== 'firewall_status') {
                $validated[$key] = 0;
            }
        }

        $server->update($validated);

        return redirect()->route('dashboard')->with('success', 'Server ' . $request->name . ' updated successfully!');
    }

    /**
     * Remove or archive the specified server from database.
     */
    public function destroy(Server $server)
    {
        $name = $server->name;
        $server->delete();

        return redirect()->route('dashboard')->with('success', 'Server ' . $name . ' deleted successfully.');
    }
}
