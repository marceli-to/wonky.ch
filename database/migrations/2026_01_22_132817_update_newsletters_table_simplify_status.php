<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->unsignedInteger('recipients_count')->default(0)->after('preheader');
            $table->unsignedInteger('sent_count')->default(0)->after('recipients_count');
            $table->dropColumn(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('preheader');
            $table->timestamp('scheduled_at')->nullable()->after('status');
            $table->dropColumn(['recipients_count', 'sent_count']);
        });
    }
};
