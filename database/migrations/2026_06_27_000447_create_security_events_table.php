// database/migrations/2024_01_01_000004_create_security_events_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->string('event_type');
            $table->string('severity');
            $table->text('description');
            $table->json('affected_systems')->nullable();
            $table->string('status')->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_events');
    }
};