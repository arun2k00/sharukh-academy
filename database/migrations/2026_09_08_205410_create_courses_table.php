<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500);
            $table->longText('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('preview_video_url')->nullable();
            $table->string('preview_video_id', 32)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->string('level', 20)->default('beginner');
            $table->string('language', 40)->default('English');
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->string('status', 20)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('outcomes')->nullable();
            $table->json('requirements')->nullable();
            $table->json('audience')->nullable();
            $table->boolean('certificate_enabled')->default(true);
            $table->unsignedTinyInteger('completion_threshold')->default(100);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_featured']);
            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
