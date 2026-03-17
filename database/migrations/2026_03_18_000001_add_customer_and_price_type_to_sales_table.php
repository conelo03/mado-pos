<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('id');
            $table->unsignedBigInteger('price_list_type_id')->nullable()->after('customer_id');
            
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('price_list_type_id')->references('id')->on('price_list_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeignKey(['customer_id']);
            $table->dropForeignKey(['price_list_type_id']);
            $table->dropColumn(['customer_id', 'price_list_type_id']);
        });
    }
};
