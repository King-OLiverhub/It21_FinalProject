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
        Schema::create('cloud_site_baselines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('label')->nullable();
            $table->string('provider')->default('Unknown Cloud Site');
            $table->json('baseline_data'); // popup_count, lag_level, load_delay_seconds, files[]
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_site_baselines');
    }
};
