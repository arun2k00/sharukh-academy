<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('student')->after('email');
            $table->string('avatar_path')->nullable()->after('password');
            $table->string('phone', 32)->nullable()->after('avatar_path');
            $table->text('bio')->nullable()->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('bio');
            $table->softDeletes();

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropSoftDeletes();
            $table->dropColumn(['role', 'avatar_path', 'phone', 'bio', 'last_login_at']);
        });
    }
};
