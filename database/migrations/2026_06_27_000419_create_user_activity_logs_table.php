<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type');
            $table->string('ip_address')->nullable();
            $table->text('details')->nullable();
            $table->timestamp('activity_at');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('activity_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activity_logs');
    }
};