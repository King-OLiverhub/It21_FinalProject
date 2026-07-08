<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column if it doesn't exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('security_analyst')->after('password');
            }
            
            // Add avatar column if it doesn't exist
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('role');
            }
            
            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('avatar');
            }
            
            // Add last_login_at column if it doesn't exist
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'is_active', 'last_login_at']);
        });
    }
};