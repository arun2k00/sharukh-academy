<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_path');
            $table->unsignedInteger('image_width');
            $table->unsignedInteger('image_height');
            $table->boolean('is_active')->default(true);
            $table->boolean('show_qr_code')->default(false);
            // One entry per overlay field: student_name, course_name, completion_date,
            // certificate_id, instructor_name, academy_name. x/y/font_size are stored as
            // percentages or pixels-at-native-resolution; see CertificateImageService.
            $table->json('fields');
            $table->string('signature_path')->nullable();
            $table->decimal('signature_x', 5, 2)->nullable();
            $table->decimal('signature_y', 5, 2)->nullable();
            $table->decimal('signature_width', 5, 2)->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
