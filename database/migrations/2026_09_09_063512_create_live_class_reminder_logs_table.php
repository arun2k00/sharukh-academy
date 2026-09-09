<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bookkeeping only: guarantees the reminder command never sends the same
     * notification twice for the same class, even if the schedule reruns.
     */
    public function up(): void
    {
        Schema::create('live_class_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->timestamp('sent_at');

            $table->unique(['live_class_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_class_reminder_logs');
    }
};
