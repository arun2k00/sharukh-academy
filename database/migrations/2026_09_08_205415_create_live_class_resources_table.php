<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_class_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 120)->nullable();
            $table->string('extension', 12)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['live_class_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_class_resources');
    }
};
