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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->string('provider'); // AWS, Azure, GCP, Local
            $table->string('status')->default('online'); // online, offline, archived
            $table->integer('cpu_usage')->default(0);
            $table->integer('memory_usage')->default(0);
            $table->decimal('storage_used', 10, 2)->default(0.00); // TB
            $table->decimal('storage_total', 10, 2)->default(0.00); // TB
            $table->integer('virtual_machines')->default(0);
            $table->integer('databases')->default(0);
            $table->integer('running_applications')->default(0);
            $table->decimal('bandwidth_usage', 10, 2)->default(0.00); // MB/s
            $table->decimal('incoming_traffic', 10, 2)->default(0.00); // GB/day
            $table->decimal('outgoing_traffic', 10, 2)->default(0.00); // GB/day
            $table->integer('response_time')->default(0); // ms
            $table->integer('failed_logins')->default(0);
            $table->string('firewall_status')->default('Active'); // Active, Restricted, Disabled
            $table->decimal('monthly_cost', 10, 2)->default(0.00); // USD
            $table->decimal('daily_usage', 10, 2)->default(0.00); // USD
            $table->decimal('budget_remaining', 10, 2)->default(0.00); // USD
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
