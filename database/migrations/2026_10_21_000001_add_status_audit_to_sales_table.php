<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('status');
            $table->foreignId('refunded_by')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable()->after('refunded_by');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            $table->text('status_reason')->nullable()->after('cancelled_by');
        });

        DB::table('sales')->where('status', 'REFUND')->update(['refunded_at' => DB::raw('updated_at')]);
        DB::table('sales')->where('status', 'VOID')->update(['cancelled_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('refunded_by');
            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropColumn(['refunded_at', 'cancelled_at', 'status_reason']);
        });
    }
};
