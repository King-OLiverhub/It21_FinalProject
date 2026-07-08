<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('network_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('ip_address');
            $table->string('mac_address');
            $table->string('device_type'); // Router, Switch, Firewall, Access Point, Server, Other
            $table->string('status')->default('Active'); // Active, Inactive, Maintenance
            $table->string('firmware_version')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_devices');
    }
};
