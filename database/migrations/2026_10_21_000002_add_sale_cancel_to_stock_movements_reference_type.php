<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->enum('reference_type', ['PURCHASE', 'SALE', 'SALE_CANCEL', 'ADJUSTMENT', 'WASTE'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->enum('reference_type', ['PURCHASE', 'SALE', 'ADJUSTMENT', 'WASTE'])->nullable()->change();
        });
    }
};
