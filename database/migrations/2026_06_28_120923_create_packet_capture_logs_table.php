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
        Schema::create('packet_capture_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source_ip');
            $table->string('destination_ip');
            $table->integer('source_port');
            $table->integer('destination_port');
            $table->string('protocol'); // TCP, UDP, ICMP, DNS, HTTP, HTTPS, ARP, FTP
            $table->integer('packet_size'); // in bytes
            $table->string('info')->nullable(); // Payload summary / description
            $table->string('status')->default('Normal'); // Normal, Suspicious, Malicious
            $table->string('direction')->default('Inbound'); // Inbound, Outbound, Internal
            $table->timestamp('captured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packet_capture_logs');
    }
};
