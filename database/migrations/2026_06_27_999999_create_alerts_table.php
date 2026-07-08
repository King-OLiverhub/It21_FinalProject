<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            
            // Change this: use unsignedBigInteger instead of foreignId (make nullable)
            $table->unsignedBigInteger('threat_indicator_id')->nullable();
            
            // Add the foreign key constraint separately
            $table->foreign('threat_indicator_id')
                  ->references('id')
                  ->on('threat_indicators')
                  ->onDelete('cascade');
            
            $table->string('alert_type');
            $table->string('severity');
            $table->text('message');
            $table->text('recommendation')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_resolved')->default(false);
            
            // Change this: use unsignedBigInteger for user_id too
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->foreign('resolved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index('severity');
            $table->index('is_read');
            $table->index('is_resolved');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerts');
    }
};