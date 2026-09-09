<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('career_paths', function (Blueprint $table) {
            $table->foreignId('bundle_course_id')->nullable()->after('capstone_description')
                ->constrained('courses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('career_paths', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bundle_course_id');
        });
    }
};
