// database/migrations/2024_01_01_000001_create_threat_indicators_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('threat_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('indicator_type'); // ip, domain, url, file_hash
            $table->string('value');
            $table->string('severity'); // Low, Medium, High, Critical
            $table->float('confidence_score')->default(0);
            $table->json('threat_data')->nullable();
            $table->string('source');
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->integer('reports_count')->default(0);
            $table->timestamp('detected_at');
            $table->timestamps();
            
            $table->index(['indicator_type', 'value']);
            $table->index('severity');
            $table->index('detected_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('threat_indicators');
    }
};