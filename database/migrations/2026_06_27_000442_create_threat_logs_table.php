// database/migrations/2024_01_01_000003_create_threat_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('threat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('source_ip')->nullable();
            $table->string('destination_ip')->nullable();
            $table->string('action');
            $table->json('details');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('threat_logs');
    }
};