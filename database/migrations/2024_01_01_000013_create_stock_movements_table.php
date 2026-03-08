<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->enum('type', ['IN', 'OUT']);
            $table->decimal('qty', 12, 2);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('reference_type', ['PURCHASE', 'SALE', 'ADJUSTMENT', 'WASTE'])->nullable();
            $table->date('date');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
