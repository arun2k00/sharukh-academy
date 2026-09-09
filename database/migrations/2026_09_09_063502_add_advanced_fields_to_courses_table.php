<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('certificate_template_id')->nullable()->after('completion_threshold')
                ->constrained()->nullOnDelete();
            $table->string('access_type', 20)->default('lifetime')->after('certificate_template_id');
            $table->unsignedInteger('access_duration_days')->nullable()->after('access_type');
            $table->date('access_fixed_expiry_date')->nullable()->after('access_duration_days');
            $table->string('completion_mode', 20)->default('all_lessons')->after('access_fixed_expiry_date');
            $table->boolean('completion_requires_live_attendance')->default(false)->after('completion_mode');
            $table->string('banner_path')->nullable()->after('thumbnail_path');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('certificate_template_id');
            $table->dropColumn([
                'access_type', 'access_duration_days', 'access_fixed_expiry_date',
                'completion_mode', 'completion_requires_live_attendance', 'banner_path',
            ]);
        });
    }
};
