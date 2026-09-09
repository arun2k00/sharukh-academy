<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('preparation_notes')->nullable();
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->text('zoom_url')->nullable();
            $table->string('zoom_meeting_id', 64)->nullable();
            $table->string('zoom_passcode', 64)->nullable();
            $table->text('recording_url')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('slug');
            $table->index(['scheduled_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
