<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ThreatController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NetworkDeviceController;
use App\Http\Controllers\PacketCaptureLogController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    
    // Pages restricted from Network Administrator role
    Route::middleware(['role:admin,security_analyst,system_admin,it_security_staff,info_security_officer'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
        Route::get('/dashboard/trends', [DashboardController::class, 'getTrends'])->name('dashboard.trends');
        Route::get('/dashboard/geo', [DashboardController::class, 'getGeoData'])->name('dashboard.geo');
        Route::get('/dashboard/alerts', [DashboardController::class, 'getAlertStats'])->name('dashboard.alerts');
        Route::get('/dashboard/health', [DashboardController::class, 'getSystemHealth'])->name('dashboard.health');

        // Server routes (view-only)
        Route::get('/servers', [ServerController::class, 'index'])->name('servers.index');

        // Reports route
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Alert resolution
        Route::post('/alerts/{id}/resolve', [DashboardController::class, 'resolveAlert'])->name('alerts.resolve');

        // Scan management
        Route::delete('/threats/scan/{id}', [ThreatController::class, 'destroyScan'])->name('threats.destroy-scan');
        Route::get('/threats/scan/{id}/report', [ThreatController::class, 'getScanReport'])->name('threats.scan-report');
    });

    // Server Creation routes (User/Security Analyst only)
    Route::middleware(['role:security_analyst'])->group(function () {
        Route::get('/servers/create', [ServerController::class, 'create'])->name('servers.create');
        Route::post('/servers', [ServerController::class, 'store'])->name('servers.store');
    });

    // Server Mutation routes (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/servers/{server}/edit', [ServerController::class, 'edit'])->name('servers.edit');
        Route::put('/servers/{server}', [ServerController::class, 'update'])->name('servers.update');
        Route::delete('/servers/{server}', [ServerController::class, 'destroy'])->name('servers.destroy');
        Route::post('/alerts/simulate', [DashboardController::class, 'simulateAlert'])->name('alerts.simulate');
    });

    // Network Device Inventory + Packet Capture Logs (Admin AND Network Administrator)
    Route::middleware(['role:admin,network_admin'])->group(function () {
        // Network Device Inventory
        Route::post('/network-devices/scan', [NetworkDeviceController::class, 'scan'])->name('network-devices.scan');
        Route::get('/network-devices/data', [NetworkDeviceController::class, 'data'])->name('network-devices.data');
        Route::resource('network-devices', NetworkDeviceController::class);
        Route::patch('/network-devices/{id}/block', [NetworkDeviceController::class, 'block'])->name('network-devices.block');
        Route::patch('/network-devices/{id}/unblock', [NetworkDeviceController::class, 'unblock'])->name('network-devices.unblock');
        
        // Packet Capture Logs (live capture + read + delete)
        Route::get('/packet-capture-logs/interfaces', [PacketCaptureLogController::class, 'interfaces'])->name('packet-capture-logs.interfaces');
        Route::get('/packet-capture-logs/stream', [PacketCaptureLogController::class, 'stream'])->name('packet-capture-logs.stream');
        Route::get('/packet-capture-logs', [PacketCaptureLogController::class, 'index'])->name('packet-capture-logs.index');
        Route::delete('/packet-capture-logs', [PacketCaptureLogController::class, 'flush'])->name('packet-capture-logs.flush');
    });
});

// Threats - Only for Security Analysts, Admins, and Info Security Officers
Route::middleware(['auth', 'role:security_analyst,admin,info_security_officer'])->group(function () {
    Route::get('/threats', [ThreatController::class, 'index'])->name('threats.index');
    Route::get('/threats/create', [ThreatController::class, 'create'])->name('threats.create');
    Route::post('/threats', [ThreatController::class, 'store'])->name('threats.store');
    Route::get('/threats/{id}', [ThreatController::class, 'show'])->name('threats.show');
    Route::get('/threats/{id}/edit', [ThreatController::class, 'edit'])->name('threats.edit');
    Route::put('/threats/{id}', [ThreatController::class, 'update'])->name('threats.update');
    Route::delete('/threats/{id}', [ThreatController::class, 'destroy'])->name('threats.destroy');
    
    // Analysis routes
    Route::post('/threats/analyze-ip', [ThreatController::class, 'analyzeIP'])->name('threats.analyze-ip');
    Route::post('/threats/analyze-domain', [ThreatController::class, 'analyzeDomain'])->name('threats.analyze-domain');
    Route::post('/threats/analyze-hash', [ThreatController::class, 'analyzeHash'])->name('threats.analyze-hash');
    Route::get('/threats/statistics', [ThreatController::class, 'statistics'])->name('threats.statistics');
    
    // Cloud Scan and Cleaning routes
    Route::post('/threats/analyze-cloud-link', [ThreatController::class, 'analyzeCloudLink'])->name('threats.analyze-cloud-link');
    Route::post('/threats/clean-file', [ThreatController::class, 'cleanVirtualFile'])->name('threats.clean-file');
    Route::post('/threats/save-baseline', [ThreatController::class, 'saveBaseline'])->name('threats.save-baseline');
});

// Admin Only Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::patch('/admin/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');
    Route::get('/admin/logs', function () {
        return view('admin.logs');
    })->name('admin.logs');
});

// System Admin Routes
Route::middleware(['auth', 'role:system_admin,admin'])->group(function () {
    Route::get('/system/status', function () {
        return view('system.status');
    })->name('system.status');
    Route::get('/system/backup', function () {
        return view('system.backup');
    })->name('system.backup');
});

// Network Admin Routes
Route::middleware(['auth', 'role:network_admin,admin'])->group(function () {
    Route::get('/network/monitor', function () {
        return view('network.monitor');
    })->name('network.monitor');
    Route::get('/network/logs', function () {
        return view('network.logs');
    })->name('network.logs');
});