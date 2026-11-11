<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
        });
    }
};
