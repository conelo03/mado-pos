<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_boms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('items')->onDelete('cascade');
            $table->decimal('qty', 12, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_boms');
    }
};
