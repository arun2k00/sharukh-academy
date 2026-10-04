<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('account_type');
            $table->string('suspension_reason', 500)->nullable()->after('suspended_at');
            $table->foreignId('suspended_by')->nullable()->after('suspension_reason')
                ->constrained('users')->nullOnDelete();

            $table->index('suspended_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('suspended_by');
            $table->dropIndex(['suspended_at']);
            $table->dropColumn(['suspended_at', 'suspension_reason']);
        });
    }
};
