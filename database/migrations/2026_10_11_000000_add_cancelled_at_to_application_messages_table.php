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
        Schema::table('application_messages', function (Blueprint $table) {
            // An open info/document request is auto-cancelled (never
            // answerable again) when the admin changes status, resolves,
            // rejects or closes the application while it's still open.
            $table->timestamp('cancelled_at')->nullable()->after('responded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_messages', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
        });
    }
};
