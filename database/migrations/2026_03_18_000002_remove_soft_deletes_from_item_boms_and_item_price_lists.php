<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_boms', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('item_price_lists', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('item_boms', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('item_price_lists', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
