<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->string('unlock_type', 20)->default('immediate')->after('sort_order');
            $table->unsignedInteger('unlock_after_days')->nullable()->after('unlock_type');
            $table->date('unlock_at')->nullable()->after('unlock_after_days');
        });
    }

    public function down(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn(['unlock_type', 'unlock_after_days', 'unlock_at']);
        });
    }
};
